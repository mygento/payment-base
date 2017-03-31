<?php
/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Block;

class Info extends \Magento\Payment\Block\Info
{

    /**
     * @var string
     */
    protected $_template = 'Mygento_Payment::form/info.phtml';

    /** @var \Mygento\Payment\Helper\Data */
    protected $_helper;

    public function __construct(
        \Mygento\Payment\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helper = $helper;
    }

    public function getOrder()
    {
        return $this->getInfo()->getOrder();
    }

    public function getPaylink()
    {
        return $this->_helper->getLink($this->getOrder()->getId());
    }

    public function getCode()
    {
        return $this->_helper->getCode();
    }
    
    public function isPaid()
    {
        if (!$this->getOrder()->hasInvoices()) {
            return false;
        }
        return true;
    }
}
