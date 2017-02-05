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
     *
     * @param \Mygento\Payment\Helper\Data $helper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Mygento\Payment\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helper = $helper;
    }

    /**
     *
     * @return string
     */
    public function getPlaceUrl()
    {
        return '';
    }

    public function getCode()
    {
        return $this->_helper->getCode();
    }
}
