# How to capture?

```php
<?php
public function prepareAction(Request $request)
{
    $paymentName = 'paypal_express_checkout_via_jms_plugin';

    $paymentInstruction = new PaymentInstruction(
        $data['amount'],
        $data['currency'],
        'paypal_express_checkout'
    );
    $paymentInstruction->setState(PaymentInstruction::STATE_VALID);

    $payment = new Payment($paymentInstruction, $data['amount']);

    $this->getDoctrine()->getManager()->persist($paymentInstruction);
    $this->getDoctrine()->getManager()->persist($payment);
    $this->getDoctrine()->getManager()->flush();

    $captureToken = $this->get('payum.security.token_factory')->createCaptureToken(
        $paymentName,
        $payment,
        'purchase_done_paypal_via_jms_plugin'
    );

    $payment->getPaymentInstruction()->getExtendedData()->set(
        'return_url',
        $captureToken->getTargetUrl()
    );
    $payment->getPaymentInstruction()->getExtendedData()->set(
        'cancel_url',
        $captureToken->getTargetUrl()
    );

    //the state manipulations  is needed for saving changes in extended data.
    $oldState = $payment->getPaymentInstruction()->getState();
    $payment->getPaymentInstruction()->setState(PaymentInstruction::STATE_INVALID);

    $this->getDoctrine()->getManager()->persist($paymentInstruction);
    $this->getDoctrine()->getManager()->persist($payment);
    $this->getDoctrine()->getManager()->flush();

    $payment->getPaymentInstruction()->setState($oldState);

    $this->getDoctrine()->getManager()->persist($paymentInstruction);
    $this->getDoctrine()->getManager()->flush();

    return $this->redirect($captureToken->getTargetUrl());
}
```
