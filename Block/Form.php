<?php
/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Block;

class Form extends \Magento\Payment\Block\Form
{

    /** @var \Mygento\Payment\Helper\Data */
    protected $_helper;

    /**
     * Instructions text
     *
     * @var string
     */
    protected $_instructions;

    /**
     *
     * @var string
     */
    protected $_template = 'Mygento_Payment::form/payment.phtml';

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
     * Returns applicable payment types
     *
     * @return array
     */
    public function getPayTypes()
    {
        return explode(',', $this->_helper->getConfig('paytype'));
    }

    /**
     *
     * @return array
     */
    public function getInstructions()
    {
        $this->_helper->addLog(get_class($this->getMethod()));
        if ($this->_instructions === null) {
            /** @var \Magento\Payment\Model\Method\AbstractMethod $method */
            $method = $this->getMethod();
            $this->_instructions = $this->_helper->getConfig('instructions');
        }
        return $this->_instructions;
    }
}
