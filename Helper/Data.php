<?php
/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Payment\Transaction;

/**
 * Payment Data helper
 */
class Data extends \Mygento\Base\Helper\Data
{
    /* @var string */

    protected $_code = 'payment';

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Framework\App\Helper\Context $context,
        \Mygento\Base\Model\Logger\LoggerFactory $loggerFactory,
        \Mygento\Base\Model\Logger\HandlerFactory $handlerFactory,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Magento\Framework\HTTP\Client\Curl $curl
    ) {

        parent::__construct(
            $context,
            $loggerFactory,
            $handlerFactory,
            $encryptor,
            $curl
        );
        $this->_orderFactory = $orderFactory;
        $this->_transactionFactory = $transactionFactory;
    }

    /**
     *
     * @param type $orderId
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getLink($orderId)
    {
        return '';
    }

    /**
     *
     * @param type $configPath
     * @return type
     */
    public function getConfig($path)
    {
        return parent::getConfig('payment/' . $this->_code . '/' . $path);
    }

    public function proceedCapture($orderId, $transactionId, $transactionData)
    {
        $invoice = $this->getInvoice($orderId);
        $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
        if($transactionId) {
            $invoice->setTransactionId($transactionId);
        }
        $invoice = $this->submitInvoice($invoice);

        if(!$invoice) {
            return;
        }
        $this->addCaptureTransaction(
            $invoice->getOrder(),
            $invoice,
            Transaction::TYPE_CAPTURE,
            $transactionId,
            $transactionData
        );
    }

    public function proceedAuthorize($orderId, $transactionId, $transactionData)
    {
        $this->addAuthorizeTransaction(
            $order = $this->_orderFactory->create()->loadByIncrementId($orderId),
            Transaction::TYPE_AUTH,
            $transactionId,
            $transactionData
        );
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\CreditMemo $entity
     * @param $transactionType
     * @param null $transactionId
     * @param array $transactionData
     */
    protected function addCaptureTransaction(
        $order,
        $entity,
        $transactionType,
        $transactionId = null,
        $transactionData = []
    )
    {
        $this->addLog('Payment ransaction -> ' . $order->getIncrementId());
        $payment = $order->getPayment();

        $payment->setTransactionId($transactionId);
        $payment->setIsTransactionClosed(false);

        $transaction = $payment->addTransaction($transactionType, $entity, true);
        if (!empty($transactionData)) {
            $transaction->setAdditionalInformation(
                Transaction::RAW_DETAILS,
                $transactionData
            );
        }
        $transaction->save();
        $payment->addTransactionCommentsToOrder(
            $transaction,
            __('Recieved payment')
        );
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param $transactionType
     * @param null $transactionId
     * @param array $transactionData
     */
    protected function addAuthorizeTransaction(
        $order,
        $transactionType,
        $transactionId = null,
        $transactionData = []
    )
    {
        $this->addLog('Payment ransaction -> ' . $order->getIncrementId());
        $payment = $order->getPayment();

        $payment->setShouldCloseParentTransaction(false);

        $payment->setTransactionId($transactionId);
        $payment->setLastTransId($transactionId);
        $payment->setIsTransactionClosed(false);
        $payment->setBaseAmountAuthorized($order->getBaseTotalDue());
        $payment->setAmountAuthorized($order->getTotalDue());

        $transaction = $payment->addTransaction($transactionType);
        if (!empty($transactionData)) {
            $transaction->setAdditionalInformation(
                Transaction::RAW_DETAILS,
                $transactionData
            );
        }
        $transaction->save();
        $payment->addTransactionCommentsToOrder(
            $transaction,
            __('Authorized payment')
        );
    }





    protected function getInvoice($orderId)
    {
        $order = $this->_orderFactory->create()->loadByIncrementId($orderId);
        $this->addLog('Will add transaction to orderID:' . $order->getId()
            . ' INC_ID:' . $orderId);

        if (!$order->canInvoice()) {
            throw new LocalizedException(
                __('The order does not allow an invoice to be created.')
            );
        }
        $payment = $order->getPayment();
        if (strpos($payment->getMethodInstance()->getCode(), $this->getCode()) === false) {
            throw new LocalizedException(
                __('The order method is not belonging to desired payment method')
            );
        }

        $invoice = $order->prepareInvoice();

        if (!$invoice) {
            throw new LocalizedException(__('We can\'t save the invoice right now.'));
        }

        if (!$invoice->getTotalQty()) {
            throw new LocalizedException(
                __('You can\'t create an invoice without products.')
            );
        }

        return $invoice;
    }

    protected function submitInvoice($invoice)
    {
        $invoice->register();

        $invoice->getOrder()->setCustomerNoteNotify(true);
        $invoice->getOrder()->setIsInProcess(true);

        /** @var \Magento\Framework\DB\Transaction $transaction */
        $transaction = $this->_transactionFactory->create();
        $transaction->addObject($invoice)
            ->addObject($invoice->getOrder())
            ->save();

        return $invoice;
    }


    /**
     *
     * @param integer $orderId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addOrderTransaction($orderId)
    {

        $order = $this->_orderFactory->create()->loadByIncrementId($orderId);
        $this->addLog('Will add transaction to orderID:' . $order->getId()
            . ' INC_ID:' . $orderId);

        if (!$order->canInvoice()) {
            throw new LocalizedException(
                __('The order does not allow an invoice to be created.')
            );
        }
        $payment = $order->getPayment();
        if (strpos($payment->getMethodInstance()->getCode(), $this->getCode()) === false) {
            throw new LocalizedException(
                __('The order method is not belonging to desired payment method')
            );
        }

        $invoice = $order->prepareInvoice();

        if (!$invoice) {
            throw new LocalizedException(__('We can\'t save the invoice right now.'));
        }

        if (!$invoice->getTotalQty()) {
            throw new LocalizedException(
                __('You can\'t create an invoice without products.')
            );
        }

        $invoice->register();

        $invoice->getOrder()->setCustomerNoteNotify(true);
        $invoice->getOrder()->setIsInProcess(true);

        /** @var \Magento\Framework\DB\Transaction $transaction */
        $transaction = $this->_transactionFactory->create();
        $transaction->addObject($invoice)
            ->addObject($invoice->getOrder())
            ->save();

        return $invoice;
    }

    public function genHash($orderId)
    {
        return strtr(base64_encode(microtime() . $orderId . rand(1, 1048576)), '+/=', '-_,');
    }
}
