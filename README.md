ZarinPal Payment
================
Online Zarinpal Payment Extension For Yii2

Installation
==============
The preferred way to install this extension is through composer.

Either run
```
php composer.phar require mjm/zarinpal:"*"
```
or add
```
"mjm/zarinpal": "*"
```
to the require section of your ``composer.json`` file.
    
How to config payment component
===============================
Add the following code to your ``config/web.php`` ``components``

```
    'components' => [
         ....
        'zarinpal' => [
            'class' => 'mjm\zarinpal\Zarinpal',
            'merchant_id' => 'XXXXXXX-XXX-XXXX-XXXXXXXXXXXX',
            'callback_url' => 'http://site.com/payment/verify'
        ],
        .... 
    ]
        
```

How to use this component
=========================
For example, imagine that you have a controller called this PaymentController at first you need 2 actions,
one of them is for request payment and another is verify payment.

you need to use an storage to save your payments and payments status.

``PaymentController.php``
```
..... 

public function actionRequest()
{
    /** @var Zarinpal $zarinpal */
    $zarinpal = Yii::$app->zarinpal ;

    // Change callback url (optional)
    $zarinpal->callback_url = Url::to(['verify', 'id'=>123], true);

    if($zarinpal->request(100,'Test Payment description')->getStatus() == '100'){
        /*
        * You can save your payment request data to the database in here before rediract user
        * to get authority code you can use $zarinpal->getAuthority()
        */
        return $this->redirect($zarinpal->getRedirectUrl());
    }
    echo "Error !";
}


public function actionVerify($Authority, $Status){

    if($Status != "OK")
        return ; //Payment canceled by user 

    /** @var Zarinpal $zarinpal */
    $zarinpal = Yii::$app->zarinpal ;
    
    if($zarinpal->verify($Authority, 100)->getStatus() == '100'){
        //User payment successfully verified!
        echo "payment successfully with referrer code: ".$zarinpal->getRefID();
    }
    elseif($zarinpal->getStatus() == '101') {
        //User payment successfuly verified but user try to verified more than one 
        echo  "duplicated verify payment";
    } 
    else
        echo "payment error !";
}

.....
```
