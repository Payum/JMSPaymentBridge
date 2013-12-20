<?php
namespace Payum\Bridge\JMSPayment\DependencyInjection\Factory\Payment;

use Payum\Bundle\PayumBundle\DependencyInjection\Factory\Payment\AbstractPaymentFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class JMSPaymentPaymentFactory extends AbstractPaymentFactory
{
    /**
     * {@inheritDoc}
     */
    public function create(ContainerBuilder $container, $contextName, array $config)
    {
        $paymentId = parent::create($container, $contextName, $config);
        $paymentDefinition = $container->getDefinition($paymentId);

        $paymentDefinition->addMethodCall('addApi', array(new Reference($config['plugin_controller_service'])));

        $captureActionDefinition = new Definition;
        $captureActionDefinition->setClass('Payum\Bridge\JMSPayment\Action\CaptureAction');
        $captureActionId = 'payum.context.'.$contextName.'.action.capture';
        $container->setDefinition($captureActionId, $captureActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($captureActionId)));

        $statusActionDefinition = new Definition;
        $statusActionDefinition->setClass('Payum\Bridge\JMSPayment\Action\StatusAction');
        $statusActionId = 'payum.context.'.$contextName.'.action.status';
        $container->setDefinition($statusActionId, $statusActionDefinition);
        $paymentDefinition->addMethodCall('addAction', array(new Reference($statusActionId)));

        return $paymentId;
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        parent::addConfiguration($builder);

        $builder
            ->children()
                ->scalarNode('plugin_controller_service')
                    ->cannotBeEmpty()
                    ->defaultValue('payment.plugin_controller')
                ->end()
            ->end()
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'jms_payment_plugin';
    }
}