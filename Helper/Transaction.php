<?php
/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Base
 */

namespace Mygento\Payment\Helper;

/**
 * Transaction Payment helper
 */
class Transaction extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mygento\Payment\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Sales\Api\TransactionRepositoryInterface
     */
    protected $transactionRepo;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Transaction\ManagerInterface
     */
    protected $transactionManager;

    public function __construct(
        \Mygento\Payment\Helper\Data $helper,
        \Magento\Sales\Model\Order\Payment\Transaction\ManagerInterface $transactionManager,
        \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepo,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->transactionRepo = $transactionRepo;
        $this->transactionManager = $transactionManager;
    }

    public function proceedAuthorize($order, $transactionId, $amount, $transData = [])
    {
        $this->helper->addLog('proceed authorize: ' . $transactionId . ' ' . $amount);
        $this->helper->addLog($transData);
        $payment = $order->getPayment();
        $payment->setTransactionId($transactionId);
        $payment->setIsTransactionClosed(0);
        $payment->registerAuthorizationNotification($amount);
        $payment->setAmountAuthorized($amount);

        $order->save();

        if (!empty($transData)) {
            $this->updateTransactionData(
                $transactionId,
                $payment->getId(),
                $order->getId(),
                $transData
            );
        }
    }

    public function proceedCapture($order, $transactionId, $amount, $transData = [])
    {
        $this->helper->addLog('proceed capture: '. $transactionId.' '.$amount);
        $this->helper->addLog($transData);
        $payment = $order->getPayment();
        $payment->setTransactionId($transactionId);
        $payment->setIsTransactionClosed(0);
        $payment->registerCaptureNotification($amount, true);

        $order->save();

        if (!empty($transData)) {
            $this->updateTransactionData(
                $transactionId,
                $payment->getId(),
                $order->getId(),
                $transData
            );
        }
    }

    // NOT WORKING PERFECTLY [wait for 2.2]
    public function proceedRefund($order, $transactionId, $parentTransactionId, $amount)
    {
        $this->helper->addLog('proceed refund: '. $transactionId.
            ' '.$parentTransactionId.' '.$amount);
        $payment = $order->getPayment();

        $payment->setTransactionId($transactionId)
            ->setParentTransactionId($parentTransactionId)
            ->setIsTransactionClosed(true)
        ;
        $payment->registerRefundNotification($amount);

        $order->save();
    }

    public function proceedVoid($order, $transactionId, $parentTransactionId, $amount)
    {
        $this->helper->addLog('proceed void: '. $transactionId
          .' '.$parentTransactionId.' '.$amount);
        $payment = $order->getPayment();
        $payment->registerVoidNotification($amount);

        $order->save();
    }

    protected function updateTransactionData($transactionId, $paymentId, $orderId, $transData)
    {
        $this->helper->addLog('seaching for transaction: '. $transactionId
            . ' '. $paymentId .' ' . $orderId);
        $transaction = $this->transactionRepo->getByTransactionId(
            $transactionId,
            $paymentId,
            $orderId
        );

        if (!$transaction) {
            $this->helper->addLog('not found payment transaction');
            return;
        }
        $this->helper->addLog('found transaction with id: '.$transaction->getId());
        $transaction->setAdditionalInformation(
            \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
            $transData
        );
        $transaction->save();
    }

    public function proceedReceipt($order, $transactionId, $parentTransactionId, $transData)
    {
        $this->helper->addLog('proceed receipt: '. $transactionId);
        $this->helper->addLog($transData);

        if ($this->checkIfTransactionExists(
            $transactionId,
            $order->getPayment()->getId(),
            $order->getId()
        )) {
            $this->helper->addLog('transaction %1 already exists', $transactionId);
            return;
        }

        $invoice = $this->getInvoiceForTransactionId($order, $parentTransactionId);

        $payment = $order->getPayment();
        $payment->setTransactionId($transactionId)
            ->setParentTransactionId($parentTransactionId)
            ->setIsTransactionClosed(true)
        ;
        $transaction = $payment->addTransaction(
            \Mygento\Base\Model\Payment\Transaction::TYPE_FISCAL,
            $invoice ? $invoice : $order
        );
        $transaction->setAdditionalInformation(
            \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
            $transData
        );
        $transaction->save();
        $order->addStatusHistoryComment(
            __('Got Fiscal Receipt for transaction %1', $parentTransactionId),
            false
        );
        $order->save();
    }

    public function proceedRefundReceipt($order, $transactionId, $parentTransactionId, $transData)
    {
        $this->helper->addLog('Proceed receipt refund: '. $transactionId);
        $this->helper->addLog($transData);

        if ($this->checkIfTransactionExists(
            $transactionId,
            $order->getPayment()->getId(),
            $order->getId()
        )) {
            $this->helper->addLog('Transaction %1 already exists', $transactionId);
            return;
        }

        $memo = $this->getCreditMemoForTransactionId($order, $parentTransactionId);

        $payment = $order->getPayment();
        $payment->setTransactionId($transactionId)
            ->setParentTransactionId($parentTransactionId)
            ->setIsTransactionClosed(true)
        ;
        $transaction = $payment->addTransaction(
            \Mygento\Base\Model\Payment\Transaction::TYPE_FISCAL_REFUND,
            $memo ? $memo : $order
        );
        $transaction->setAdditionalInformation(
            \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
            $transData
        );
        $transaction->save();
        $order->addStatusHistoryComment(
            __('Got Fiscal Refund Receipt for transaction %1', $parentTransactionId),
            false
        );
        $order->save();
    }

    /**
     * Return invoice model for transaction
     *
     * @param string $transactionId
     * @return \Magento\Sales\Model\Order\Invoice|false
     */
    protected function getInvoiceForTransactionId($order, $transactionId)
    {
        foreach ($order->getInvoiceCollection() as $invoice) {
            if ($invoice->getTransactionId() == $transactionId) {
                $invoice->load($invoice->getId());
                return $invoice;
            }
        }

        return false;
    }

    /**
     * Return invoice model for transaction
     *
     * @param string $transactionId
     * @return \Magento\Sales\Model\Order\Creditmemo|false
     */
    protected function getCreditMemoForTransactionId($order, $transactionId)
    {
        foreach ($order->getCreditmemosCollection() as $creditmemo) {
            if ($creditmemo->getTransactionId() == $transactionId) {
                $creditmemo->load($creditmemo->getId());
                return $creditmemo;
            }
        }

        return false;
    }

    /**
     * Checks if transaction exists by txt id
     *
     * @param string $transactionId
     * @param int $paymentId
     * @param int $orderId
     * @return bool
     */
    protected function checkIfTransactionExists($transactionId, $paymentId, $orderId)
    {
        return $this->transactionManager->isTransactionExists(
            $transactionId,
            $paymentId,
            $orderId
        );
    }
}
