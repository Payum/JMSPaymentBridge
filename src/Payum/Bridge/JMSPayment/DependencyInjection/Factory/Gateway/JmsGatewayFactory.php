<?php
namespace Payum\Bridge\JMSPayment\DependencyInjection\Factory\Gateway;

use Payum\Bundle\PayumBundle\DependencyInjection\Factory\Gateway\AbstractGatewayFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @deprecated since 1.0 and will be removed in 2.0
 */
class JmsGatewayFactory extends AbstractGatewayFactory
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'jms_payment_plugin';
    }

    /**
     * {@inheritDoc}
     */
    protected function getPayumGatewayFactoryClass()
    {
        return 'Payum\Bridge\JMSPayment\JmsGatewayFactory';
    }

    /**
     * {@inheritDoc}
     */
    protected function getComposerPackage()
    {
        return 'payum/jms-payment-bridge';
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
     * @param ContainerBuilder $container
     * @param $gatewayName
     * @param array $config
     *
     * @return Definition
     */
    protected function createGateway(ContainerBuilder $container, $gatewayName, array $config)
    {
        $config['payum.api'] = new Reference($config['plugin_controller_service']);

        return parent::createGateway($container, $gatewayName, $config);
    }
}