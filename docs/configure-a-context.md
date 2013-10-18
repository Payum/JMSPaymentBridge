# Configure a context

Once you added the factory you can configure payum context.

```yml
jms_payment_core:
    secret:                                               %kernel.secret%

jms_payment_paypal:
    username:                                             %paypal.express_checkout.username%
    password:                                             %paypal.express_checkout.password%
    signature:                                            %paypal.express_checkout.signature%
    debug:                                                true

payum:
    security:
        token_storage:
            Acme\PaymentBundle\Entity\PayumSecurityToken:
                doctrine:
                    driver: orm

    contexts:
        paypal_express_checkout_via_jms_plugin:
            jms_payment_plugin: ~
            storages:
                JMS\Payment\CoreBundle\Entity\Payment:
                    doctrine:
                        driver: orm
```

Not so hard so far, let's continue.
