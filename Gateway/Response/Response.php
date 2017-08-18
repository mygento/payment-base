<?php

/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Gateway\Response;

class Response implements \Magento\Payment\Gateway\Response\HandlerInterface
{
    /**
     * @var \Magento\Payment\Gateway\ConfigInterface
     */
    protected $_helper;

    /**
     * @param ResultInterfaceFactory $resultFactory
     */
    public function __construct(
        \Mygento\Payment\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * Handles transaction id
     *
     * @param array $handlingSubject
     * @param array $response
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        return null;
    }
}
