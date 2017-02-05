<?php
/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Controller\Payment;

abstract class AbstractAction extends \Magento\Framework\App\Action\Action
{
    /** @var \Mygento\Payment\Helper\Data */
    protected $_helper;

    /** @var \Magento\Sales\Model\OrderFactory */
    protected $_orderFactory;

    /** @var \Magento\Checkout\Model\Session */
    protected $_checkoutSession;

    /** @var \Magento\Framework\View\Result\PageFactory */
    protected $_resultPageFactory;

    public function __construct(
        \Mygento\Payment\Helper\Data $helper,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\App\Action\Context $context
    ) {
    
        parent::__construct($context);
        $this->_helper = $helper;
        $this->_orderFactory = $orderFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_resultLayoutFactory = $resultLayoutFactory;
    }
}
