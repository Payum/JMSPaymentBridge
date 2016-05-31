<?php
namespace Payum\Bridge\JMSPayment\Action;

use JMS\Payment\CoreBundle\Model\PaymentInterface;
use JMS\Payment\CoreBundle\Plugin\Exception\Action\VisitUrl;
use JMS\Payment\CoreBundle\Plugin\Exception\ActionRequiredException;
use JMS\Payment\CoreBundle\PluginController\PluginControllerInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Authorize;
use Payum\Core\Request\Capture;

/**
 * @property PluginControllerInterface $api
 */
class AuthorizeAction implements ActionInterface, ApiAwareInterface
{
    use ApiAwareTrait;
    
    public function __construct()
    {
        $this->apiClass = PluginControllerInterface::class;
    }

    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();

        $result = $this->api->approve($payment->getId(), $payment->getTargetAmount());

        if ($exception = $result->getPluginException()) {
            if ($exception instanceof ActionRequiredException) {
                if ($exception->getAction() instanceof VisitUrl) {
                    throw new HttpRedirect($exception->getAction()->getUrl());
                }

                throw new \LogicException(sprintf(
                    'The given action required exception %s could not be converted to payum\'s reply.',
                    get_class($exception)
                ), null, $exception);
            }

            throw $exception;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Authorize &&
            $request->getModel() instanceof PaymentInterface
        ;
    }
}