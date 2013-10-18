# After purchase is done.

Have you noticed `purchase_done_paypal_via_jms_plugin`  the third parameter of `createCaptureToken` method?
It's the route of action where we are redirected after the capture is done. In that action we have to check a status.

```php
<?php
public function viewAction(Request $request)
{
    $token = $this->get('payum.security.http_request_verifier')->verify($request);

    $status = new BinaryMaskStatusRequest($token);

    $this->get('payum')->getPayment($token->getPaymentName())->execute($status);

    //do some stuff depends on status. for example show status and details

    return array(
        'status' => $status,
    );
}
```
