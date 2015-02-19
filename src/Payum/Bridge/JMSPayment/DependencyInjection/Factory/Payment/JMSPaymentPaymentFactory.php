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
    public function getName()
    {
        return 'jms_payment_plugin';
    }

    /**
     * {@inheritDoc}
     */
    protected function getPayumPaymentFactoryClass()
    {
        return 'Payum\Bridge\JMSPayment\PaymentFactory';
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
     * @param $paymentName
     * @param array $config
     *
     * @return Definition
     */
    protected function createPayment(ContainerBuilder $container, $paymentName, array $config)
    {
        $config['payum.api'] = new Reference($config['plugin_controller_service']);

        return parent::createPayment($container, $paymentName, $config);
    }
}