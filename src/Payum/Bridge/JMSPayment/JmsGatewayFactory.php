<?php
namespace Payum\Bridge\JMSPayment;

use Payum\Bridge\JMSPayment\Action\CaptureAction;
use Payum\Bridge\JMSPayment\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactoryInterface;
use Payum\Core\GatewayFactory as CoreGatewayFactory;

class JmsGatewayFactory implements GatewayFactoryInterface
{
    /**
     * @var GatewayFactoryInterface
     */
    protected $coreGatewayFactory;

    /**
     * @var array
     */
    private $defaultConfig;

    /**
     * @param array $defaultConfig
     * @param GatewayFactoryInterface $coreGatewayFactory
     */
    public function __construct(array $defaultConfig = array(), GatewayFactoryInterface $coreGatewayFactory = null)
    {
        $this->coreGatewayFactory = $coreGatewayFactory ?: new CoreGatewayFactory();
        $this->defaultConfig = $defaultConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $config = array())
    {
        return $this->coreGatewayFactory->create($this->createConfig($config));
    }

    /**
     * {@inheritDoc}
     */
    public function createConfig(array $config = array())
    {
        $config = ArrayObject::ensureArrayObject($config);
        $config->defaults($this->defaultConfig);
        $config->defaults($this->coreGatewayFactory->createConfig((array) $config));

        $config->defaults(array(
            'payum.factory_name' => 'jms_payment_plugin',
            'payum.factory_title' => 'JmsPayment',

            'payum.action.capture' => new CaptureAction(),
            'payum.action.fill_order_details' => new StatusAction(),
        ));

        return (array) $config;
    }
}