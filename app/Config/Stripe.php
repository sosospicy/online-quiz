<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Stripe extends BaseConfig
{
    // your api key
    public $APIKEY = 'sk_test_51Lu8rKIc4BMj7j22K18z8tWidE7Wbkl74XMTRSR7KysIq0POrXBP99J0B398Doz9wFNgNzUb6LpSMMKmFnahwxto00rdZXFpbW';

    // your price id, like price_******
    public $price_id = 'price_1LvEygIc4BMj7j2211P1yP56';

    // trial days
    public $trial_days = 1;

    // pay methods 
    // manage your payment methods from the Stripe Dashboard
    // public $pay_method_types = ['card'];

    // webhook
    public $endpoint_secret = 'whsec_514bf9df115f8a4869b26e3f226fe02c4633932224d9564a92fee370a0412e27';
    
}