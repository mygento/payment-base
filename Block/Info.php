<?php
/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Block;

use Magento\Sales\Model\Order;

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

    /**
     * @return bool
     */
    public function isPaid()
    {
        $order = $this->getOrder();
        return $order->hasInvoices() && !$order->hasCreditmemos();
    }

    /**
     * @return bool
     */
    public function isComplete()
    {
        $state = $this->getOrder()->getState();
        if ($this->getOrder()->isCanceled() ||
            $state === Order::STATE_COMPLETE ||
            $state === Order::STATE_CLOSED
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function canShowPayLink()
    {
        if ($this->isPaid() || $this->isAuthorized()) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isAuthorized()
    {
        $payment = $this->getOrder()->getPayment();
        return $payment->getAuthorizationTransaction() &&
            (bool)$payment->getAmountAuthorized() &&
            !(bool)$payment->getAmountPaid() &&
            !(int)$payment->getAuthorizationTransaction()->getIsClosed();
    }
}
