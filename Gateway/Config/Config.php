<?php

/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Config
 */
class Config extends \Magento\Payment\Gateway\Config\Config
{
    /* @var \Magento\Framework\Encryption\Encryptor */
    protected $_encryptor;

    public function __construct(
        \Magento\Framework\Encryption\Encryptor $encryptor,
        ScopeConfigInterface $scopeConfig,
        $methodCode = null,
        $pathPattern = \Magento\Payment\Gateway\Config\Config::DEFAULT_PATH_PATTERN
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
        $this->_encryptor = $encryptor;
    }
}
