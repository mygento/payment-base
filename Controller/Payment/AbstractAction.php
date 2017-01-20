<?php
/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Controller\Payment;

abstract class AbstractAction extends \Magento\Framework\App\Action\Action
{

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Mygento\Payment\Helper\Data $helper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
    
        parent::__construct($context);
        $this->_helper = $helper;
        $this->_orderFactory = $orderFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_resultLayoutFactory = $resultLayoutFactory;
    }
}
