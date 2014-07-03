<?php
namespace Payum\Bridge\JMSPayment\Action;

use JMS\Payment\CoreBundle\Model\PaymentInterface;
use JMS\Payment\CoreBundle\Plugin\Exception\Action\VisitUrl;
use JMS\Payment\CoreBundle\Plugin\Exception\ActionRequiredException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\CaptureRequest;
use Payum\Core\Request\Http\RedirectUrlInteractiveRequest;

class CaptureAction extends BaseApiAwareAction
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        if (false == $this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();

        $result = $this->pluginController->approveAndDeposit($payment->getId(), $payment->getTargetAmount());

        if ($ex = $result->getPluginException()) {
            if ($ex instanceof ActionRequiredException) {
                $action = $ex->getAction();
                if ($action instanceof VisitUrl) {
                    throw new RedirectUrlInteractiveRequest($action->getUrl());
                }
            }

            throw $ex;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof CaptureRequest &&
            $request->getModel() instanceof PaymentInterface
        ;
    }
}