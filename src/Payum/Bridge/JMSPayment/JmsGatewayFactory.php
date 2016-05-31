<?php
namespace Payum\Bridge\JMSPayment;

use Payum\Bridge\JMSPayment\Action\AuthorizeAction;
use Payum\Bridge\JMSPayment\Action\CaptureAction;
use Payum\Bridge\JMSPayment\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class JmsGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'jms_payment_plugin',
            'payum.factory_title' => 'JmsPayment',
            
            'payum.action.capture' => new CaptureAction(),
            'payum.action.authorize' => new AuthorizeAction(),
            'payum.action.status' => new StatusAction(),

            'payum.api' => '@payment.plugin_controller'
        ]);
    }
}