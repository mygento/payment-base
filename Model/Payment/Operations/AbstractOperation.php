<?php
/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Model\Payment\Operations;

use Mygento\Payment\Helper\Data as Helper;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Sales\Model\Order\Payment\State\CommandInterface;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use Magento\Sales\Model\Order\Payment\Transaction\ManagerInterface;


class AbstractOperation extends \Magento\Sales\Model\Order\Payment\Operations\AbstractOperation
{

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @param Helper           $helper
     * @param CommandInterface $stateCommand
     * @param BuilderInterface $transactionBuilder
     * @param ManagerInterface $transactionManager
     * @param EventManagerInterface $eventManager
     */
    public function __construct(
        Helper $helper,
        CommandInterface $stateCommand,
        BuilderInterface $transactionBuilder,
        ManagerInterface $transactionManager,
        EventManagerInterface $eventManager
    ) {
        parent::__construct($stateCommand, $transactionBuilder, $transactionManager, $eventManager);
        $this->_helper = $helper;
    }
}