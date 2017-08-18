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
    protected $_helper;

    /**
     * @var \Magento\Sales\Api\TransactionRepositoryInterface
     */
    protected $_transactionRepo;

    public function __construct(
        \Mygento\Payment\Helper\Data $helper,
        \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepo,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->_helper = $helper;
        $this->_transactionRepo = $transactionRepo;
    }

    public function proceedAuthorize($order, $transactionId, $amount, $transactionData = [])
    {
        $this->_helper->addLog('proceed authorize: ' . $transactionId . ' ' . $amount);
        $this->_helper->addLog($transactionData);
        $payment = $order->getPayment();
        $payment->setTransactionId($transactionId);
        $payment->setIsTransactionClosed(0);
        $payment->registerAuthorizationNotification($amount);

        $order->save();

        if (!empty($transactionData)) {
            $this->updateTransactionData(
                $transactionId,
                $payment->getId(),
                $order->getId(),
                $transactionData
            );
        }
    }

    public function proceedCapture($order, $transactionId, $amount, $transactionData = [])
    {
        $this->_helper->addLog('proceed capture: '. $transactionId.' '.$amount);
        $this->_helper->addLog($transactionData);
        $payment = $order->getPayment();
        $payment->setTransactionId($transactionId);
        $payment->setIsTransactionClosed(0);
        $payment->registerCaptureNotification($amount, true);

        $order->save();

        if (!empty($transactionData)) {
            $this->updateTransactionData(
                $transactionId,
                $payment->getId(),
                $order->getId(),
                $transactionData
            );
        }
    }

    // NOT WORKING PERFECTLY [wait for 2.2]
    public function proceedRefund($order, $transactionId, $parentTransactionId, $amount)
    {
        $this->_helper->addLog('proceed refund: '. $transactionId.
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
        $this->_helper->addLog('proceed void: '. $transactionId
          .' '.$parentTransactionId.' '.$amount);
        $payment = $order->getPayment();
        $payment->registerVoidNotification($amount);

        $order->save();
    }

    protected function updateTransactionData($transactionId, $paymentId, $orderId, $transactionData)
    {
        $this->_helper->addLog('seaching for transaction: '. $transactionId
            . ' '. $paymentId .' ' . $orderId);
        $transaction = $this->_transactionRepo->getByTransactionId(
            $transactionId,
            $paymentId,
            $orderId
        );

        if (!$transaction) {
            $this->_helper->addLog('not found payment transaction');
            return;
        }
        $this->_helper->addLog('found transaction with id: '.$transaction->getId());
        $transaction->setAdditionalInformation(
            \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS,
            $transactionData
        );
        $transaction->save();
    }
}
