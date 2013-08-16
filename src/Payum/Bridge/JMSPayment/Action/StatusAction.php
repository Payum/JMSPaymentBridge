<?php
namespace Payum\Bridge\JMSPayment\Action;

use JMS\Payment\CoreBundle\Model\PaymentInterface;
use JMS\Payment\CoreBundle\Plugin\Exception\Action\VisitUrl;
use JMS\Payment\CoreBundle\Plugin\Exception\ActionRequiredException;
use Payum\Action\ActionInterface;
use Payum\Exception\RequestNotSupportedException;
use Payum\Request\CaptureRequest;
use Payum\Request\RedirectUrlInteractiveRequest;
use Payum\Request\StatusRequestInterface;

class StatusAction implements ActionInterface
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request StatusRequestInterface */
        if (false == $this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();

        if (in_array($payment->getState(), array(PaymentInterface::STATE_APPROVED, PaymentInterface::STATE_DEPOSITED))) {
            $request->markSuccess();

            return;
        }

        if (in_array($payment->getState(), array(PaymentInterface::STATE_APPROVING, PaymentInterface::STATE_DEPOSITING))) {
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

        if (in_array($payment->getState(), array(PaymentInterface::STATE_NEW))) {
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
            $request instanceof StatusRequestInterface &&
            $request->getModel() instanceof PaymentInterface
        ;
    }
}