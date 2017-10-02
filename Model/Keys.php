<?php
/**
 * @author Mygento Team
 * @copyright Copyright 2017 Mygento (https://www.mygento.ru)
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Model;

class Keys extends \Magento\Framework\Model\AbstractModel
{

    protected function _construct()
    {
        $this->_init('Mygento\Payment\Model\ResourceModel\Keys');
    }
}
