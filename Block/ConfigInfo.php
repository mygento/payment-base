<?php
/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Block;

class ConfigInfo extends Info
{
    /**
     * @var \Magento\Payment\Gateway\ConfigInterface
     */
    protected $_config;

    public function __construct(
        \Magento\Payment\Gateway\ConfigInterface $config,
        \Mygento\Payment\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($helper, $context, $data);
        $this->_config = $config;
    }

    protected function getConfig()
    {
        return $this->_config;
    }
}
