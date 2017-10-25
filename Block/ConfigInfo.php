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
    protected $config;

    /**
     * @var \Mygento\Base\Helper\Discount
     */
    protected $taxHelper;

    public function __construct(
        \Magento\Payment\Gateway\ConfigInterface $config,
        \Mygento\Payment\Helper\Data $helper,
        \Mygento\Base\Helper\Discount $taxHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($helper, $context, $data);
        $this->config = $config;
        $this->taxHelper = $taxHelper;
    }

    protected function getConfig()
    {
        return $this->config;
    }

    protected function getTaxHelper()
    {
        return $this->taxHelper;
    }
}
