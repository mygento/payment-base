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

    /**
     * @var \Mygento\Payment\Model\KeysFactory
     */
    protected $modelKeys;

    /**
     * Data constructor.
     * @param \Mygento\Payment\Model\KeysFactory $modelKeys
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Mygento\Base\Model\Logger\LoggerFactory $loggerFactory
     * @param \Mygento\Base\Model\Logger\HandlerFactory $handlerFactory
     * @param \Magento\Framework\Encryption\Encryptor $encryptor
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Mygento\Payment\Model\KeysFactory $modelKeys,
        \Magento\Framework\App\Helper\Context $context,
        \Mygento\Base\Model\Logger\LoggerFactory $loggerFactory,
        \Mygento\Base\Model\Logger\HandlerFactory $handlerFactory,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->modelKeys = $modelKeys;
        parent::__construct(
            $context,
            $loggerFactory,
            $handlerFactory,
            $encryptor,
            $curl,
            $productRepository
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
        $collection = $this->modelKeys->create()->getCollection();
        $collection->addFieldToFilter('order_id', $orderId);
        $collection->addFieldToFilter('code', $this->_code);
        if ($collection->getSize() > 0) {
            $item = $collection->getFirstItem();
            return $this->_urlBuilder->getUrl($this->_code.'/payment/capture/', [
                '_secure' => true,
                'order' => $item->getHkey()
            ]);
        }
        $key = $this->genHash($orderId);
        $newKeyModel = $this->modelKeys->create();
        $newKeyModel->setData([
            'hkey' => $key,
            'code' => $this->_code,
            'order_id' => $orderId
        ]);
        $newKeyModel->save();
        return $this->_urlBuilder->getUrl($this->_code.'/payment/capture/', [
            '_secure' => true,
            '_nosid' => true,
            'order' => $key
        ]);
    }

    /**
     * @param string $link
     * @return int|bool
     */
    public function decodeId($link)
    {
        $keysModel = $this->modelKeys->create();
        $collection = $keysModel->getCollection();
        $collection->addFieldToFilter('hkey', $link);
        $collection->addFieldToFilter('code', $this->_code);
        if ($collection->getSize() == 0) {
            return false;
        }
        $item = $collection->getFirstItem();
        return $item->getOrderId();
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function getConfig($path)
    {
        $scope = $this->_code === 'payment' ? 'mygento' : 'payment';
        return parent::getConfig($scope. '/' . $this->_code . '/' . $path);
    }

    protected function getDebugConfigPath()
    {
        return 'debug';
    }

    /**
     * @param string $orderId
     * @return string
     */
    public function genHash($orderId)
    {
        return strtr(base64_encode(microtime() . $orderId . rand(1, 1048576)), '+/=', '-_,');
    }
}
