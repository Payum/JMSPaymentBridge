<?php
namespace Payum\Bridge\JMSPayment\Action;

use JMS\Payment\CoreBundle\PluginController\PluginControllerInterface;
use Payum\Action\ActionInterface;
use Payum\ApiAwareInterface;
use Payum\Exception\UnsupportedApiException;

abstract class BaseApiAwareAction implements ActionInterface, ApiAwareInterface
{
    /**
     * @var PluginControllerInterface
     */
    protected $pluginController;

    /**
     * {@inheritdoc}
     */
    public function setApi($api)
    {
        if (false == $api instanceof PluginControllerInterface) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->pluginController = $api;
    }
}