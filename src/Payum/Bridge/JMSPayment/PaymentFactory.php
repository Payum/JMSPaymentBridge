<?php
namespace Payum\Bridge\JMSPayment;

use Payum\Bridge\JMSPayment\Action\CaptureAction;
use Payum\Bridge\JMSPayment\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\PaymentFactoryInterface;
use Payum\Core\PaymentFactory as CorePaymentFactory;

class PaymentFactory implements PaymentFactoryInterface
{
    /**
     * @var PaymentFactoryInterface
     */
    protected $corePaymentFactory;

    /**
     * @var array
     */
    private $defaultConfig;

    /**
     * @param array $defaultConfig
     * @param PaymentFactoryInterface $corePaymentFactory
     */
    public function __construct(array $defaultConfig = array(), PaymentFactoryInterface $corePaymentFactory = null)
    {
        $this->corePaymentFactory = $corePaymentFactory ?: new CorePaymentFactory();
        $this->defaultConfig = $defaultConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $config = array())
    {
        return $this->corePaymentFactory->create($this->createConfig($config));
    }

    /**
     * {@inheritDoc}
     */
    public function createConfig(array $config = array())
    {
        $config = ArrayObject::ensureArrayObject($config);
        $config->defaults($this->defaultConfig);
        $config->defaults($this->corePaymentFactory->createConfig((array) $config));

        $config->defaults(array(
            'payum.factory_name' => 'jms_payment_plugin',
            'payum.factory_title' => 'JmsPayment',

            'payum.action.capture' => new CaptureAction(),
            'payum.action.fill_order_details' => new StatusAction(),
        ));

        return (array) $config;
    }
}