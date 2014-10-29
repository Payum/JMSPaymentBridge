<?php
namespace Payum\Bridge\JMSPayment\Action;

use JMS\Payment\CoreBundle\Model\PaymentInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;

class StatusAction implements ActionInterface
{
    /**
     * {@inheritDoc}
     *
     * @param GetStatusInterface $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();

        if (in_array($payment->getState(), array(PaymentInterface::STATE_APPROVED, PaymentInterface::STATE_DEPOSITED))) {
            $request->markCaptured();

            return;
        }

        if (in_array($payment->getState(), array(PaymentInterface::STATE_DEPOSITING))) {
            $request->markPending();

            return;
        }

        if (in_array($payment->getState(), array(PaymentInterface::STATE_CANCELED))) {
            $request->markCanceled();

            return;
        }

        if (in_array($payment->getState(), array(PaymentInterface::STATE_EXPIRED))) {
            $request->markExpired();

            return;
        }

        if (in_array($payment->getState(), array(PaymentInterface::STATE_FAILED))) {
            $request->markFailed();

            return;
        }

        if (in_array($payment->getState(), array(PaymentInterface::STATE_NEW, PaymentInterface::STATE_APPROVING))) {
            $request->markNew();

            return;
        }

        $request->markUnknown();
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof PaymentInterface
        ;
    }
}