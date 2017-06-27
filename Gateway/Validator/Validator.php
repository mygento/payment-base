<?php

/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Gateway\Validator;

class Validator extends \Magento\Payment\Gateway\Validator\AbstractValidator
{
    /**
     * @var \Magento\Payment\Gateway\ConfigInterface
     */
    protected $_helper;

    /**
     * @param ResultInterfaceFactory $resultFactory
     */
    public function __construct(
        \Mygento\Payment\Helper\Data $helper,
        \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultFactory
    ) {
        parent::__construct($resultFactory);
        $this->_helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $isValid = false;
        $errorMessages = [];
        return $this->createResult($isValid, $errorMessages);
    }
}
