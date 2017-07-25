<?php

/**
 * @author Mygento Team
 * @copyright Copyright 2017 Mygento (https://www.mygento.ru)
 * @package Mygento_Cloudpayments
 */

namespace Mygento\Payment\Gateway\Config;

use Magento\Sales\Model\Order\Payment;

class CanVoidHandler implements \Magento\Payment\Gateway\Config\ValueHandlerInterface
{

    /**
     * @var \Magento\Payment\Gateway\ConfigInterface
     */
    private $config;

    /**
     * @param \Magento\Payment\Gateway\ConfigInterface $config
     */
    public function __construct(
        \Magento\Payment\Gateway\ConfigInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * Retrieve method configured value
     *
     * @param array $subject
     * @param int|null $storeId
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handle(array $subject, $storeId = null)
    {
        if($this->config->getValue('step') && $this->config->getValue('step') !== "2")  {
            return false;
        }

        $payment = $subject['payment']->getPayment();
        return $payment instanceof Payment && !(bool)$payment->getAmountPaid();
    }
}