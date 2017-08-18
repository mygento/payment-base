<?php

/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Gateway\Http\Client;

class Client implements \Magento\Payment\Gateway\Http\ClientInterface
{
    /** @var \Magento\Payment\Gateway\ConfigInterface */
    protected $_config;

    /** @var \Mygento\Payment\Helper\Data */
    protected $_helper;

    /** @var \Magento\Framework\HTTP\Client\Curl */
    protected $_curl;

    public function __construct(
        \Magento\Payment\Gateway\ConfigInterface $config,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Mygento\Payment\Helper\Data $helper
    ) {
        $this->_curl = $curl;
        $this->_config = $config;
        $this->_helper = $helper;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function placeRequest(\Magento\Payment\Gateway\Http\TransferInterface $transferObject)
    {
        return null;
    }

    /**
     * @param string $endpoint
     * @param array $params
     * @return string
     */
    protected function sendRequest($path, array $params = [])
    {
        $this->_curl->post($this->url . $path, $params);
        return $this->_curl->getBody();
    }
}
