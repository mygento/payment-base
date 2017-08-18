<?php
/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Helper;

/**
 * Payment Data helper
 */
class Data extends \Mygento\Base\Helper\Data
{
    /* @var string */

    protected $_code = 'payment';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Mygento\Base\Model\Logger\LoggerFactory $loggerFactory,
        \Mygento\Base\Model\Logger\HandlerFactory $handlerFactory,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Magento\Framework\HTTP\Client\Curl $curl
    ) {

        parent::__construct(
            $context,
            $loggerFactory,
            $handlerFactory,
            $encryptor,
            $curl
        );
    }

    /**
     *
     * @param integer $orderId
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getLink($orderId)
    {
        return '';
    }

    /**
     *
     * @param string $configPath
     * @return mixed
     */
    public function getConfig($path)
    {
        return parent::getConfig('payment/' . $this->_code . '/' . $path);
    }

    public function genHash($orderId)
    {
        return strtr(base64_encode(microtime() . $orderId . rand(1, 1048576)), '+/=', '-_,');
    }
}
