<?php
/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Helper;

use Magento\Framework\Exception\LocalizedException;

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


    /**
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     *
     * @param integer $orderId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addOrderTransaction($orderId)
    {
        $order = $this->_orderFactory->create()->loadByIncrementId($orderId);
        $this->addLog('Will add transaction to order ID:' . $order->getId() .
            ' INC_ID:' . $orderId);

        if (!$order->canInvoice()) {
            throw new LocalizedException(
                __('The order does not allow an invoice to be created.')
            );
        }

        if (strpos($order->getPayment()->getMethodInstance()->getCode(), $this->code) === false
        ) {
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
    }
}
