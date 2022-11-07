<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Auth extends BaseConfig
{
    // Google Sign In Application
    public $googleApp = [
        'client_id' => '751484543020-kpcvh5rdi661dcaqmee3a7p8ca5p5r64.apps.googleusercontent.com',
    ];

    // Facebook Sign In Application
    public $facebookApp = [
        'app_id' => '1160545601517955',
        'app_secret' => '33da4d7d6cfdfaff3c52c7224e140161',
    ];


}