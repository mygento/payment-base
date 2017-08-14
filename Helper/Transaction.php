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
     * @var \Mygento\Payment\Model\Payment\Operations\Capture
     */
    protected $_captureOperation;

    public function __construct(
        \Mygento\Payment\Model\Payment\Operations\Capture $captureOperation,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->_captureOperation = $captureOperation;
    }


    /**
     * Process capture operation
     *
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface $orderPayment
     * @param string|float $amount
     * @param array $transactionData
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Magento\Sales\Api\Data\OrderPaymentInterface
     */
    public function capture(\Magento\Sales\Api\Data\OrderPaymentInterface $orderPayment, $amount, $transactionData)
    {
        return $this->_captureOperation->registerCaptureNotification($orderPayment, $amount, $transactionData);
    }
}