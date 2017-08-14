<?php
/**
 * @author Mygento
 * @copyright See COPYING.txt for license details.
 * @package Mygento_Payment
 */

namespace Mygento\Payment\Model\Payment\Operations;

use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;


class Capture extends AbstractOperation
{
    /**
     * Registers capture notification.
     *
     * @param OrderPaymentInterface $payment
     * @param string|float $amount
     * @param bool|int $skipFraudDetection
     * @return OrderPaymentInterface
     */
    public function registerCaptureNotification(OrderPaymentInterface $payment, $amount, $transactionData = [], $skipFraudDetection = true)
    {

        $this->_helper->addLog('capture notification with amount: '. $amount);
        $this->_helper->addLog('transaction: '. $payment->getTrasactionId());
        $this->_helper->addLog($transactionData);

        /**
         * @var $payment Payment
         */
        $payment->setTransactionId(
            $this->transactionManager->generateTransactionId(
                $payment,
                Transaction::TYPE_CAPTURE,
                $payment->getAuthorizationTransaction()
            )
        );

        $order = $payment->getOrder();
        $amount = (double)$amount;
        $invoice = $this->getInvoiceForTransactionId($order, $payment->getTransactionId());

        // register new capture
        if (!$invoice) {
            $this->_helper->addLog($payment->getTransactionId().'-> no invoice found');
            if ($payment->isSameCurrency() && $payment->isCaptureFinal($amount)) {
                $invoice = $order->prepareInvoice()->register();
                $invoice->setOrder($order);
                $order->addRelatedObject($invoice);
                $payment->setCreatedInvoice($invoice);
                $payment->setShouldCloseParentTransaction(true);
            } else {
                $payment->setIsFraudDetected(!$skipFraudDetection);
                $this->updateTotals($payment, ['base_amount_paid_online' => $amount]);
            }
        }

        if (!$payment->getIsTransactionPending()) {
            $this->_helper->addLog($payment->getTransactionId().'-> is not pending');
            if ($invoice && Invoice::STATE_OPEN == $invoice->getState()) {
                $invoice->setOrder($order);
                $invoice->pay();
                $this->updateTotals($payment, ['base_amount_paid_online' => $amount]);
                $order->addRelatedObject($invoice);
            }
        }

        $message = $this->stateCommand->execute($payment, $amount, $order);
        $transaction = $payment->addTransaction(
            Transaction::TYPE_CAPTURE,
            $invoice,
            true
        );

        if (!empty($transactionData)) {
            $transaction->setAdditionalInformation(
                Transaction::RAW_DETAILS,
                $transactionData
            );
        }

        $message = $payment->prependMessage($message);
        $payment->addTransactionCommentsToOrder($transaction, $message);
        $this->_helper->addLog($payment->getTransactionId().' add message: '.$message);
        return $payment;
    }
}