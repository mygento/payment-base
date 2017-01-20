<?php
/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Block;

class Redirect extends \Magento\Payment\Block\Form
{

    /**
     * @var string
     */
    protected $_template = 'Mygento_Payment::form/redirect.phtml';

    /**
     * @var \Mygento\Payment\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     *
     * @param \Mygento\Payment\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Mygento\Payment\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helper = $helper;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     *
     * @return string
     */
    public function getPlaceUrl()
    {
        return '';
    }
}
