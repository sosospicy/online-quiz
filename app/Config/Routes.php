<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */
$routes->get('/', 'Charge::start');
$routes->get('/privacy', 'Signin::privacy');
$routes->get('/close-account', 'Signin::accountCloseDesc');
$routes->get('/sign-up', 'Signup::index');
$routes->post('/sign-up', 'Signup::register');
$routes->get('/verify-email', 'Signup::verify_email');

$routes->add('/sign-in', 'Signin::index');
$routes->add('/sign-in-google', 'Signin::auth/google');
$routes->add('/sign-in-facebook', 'Signin::auth/facebook');
$routes->add('/facebook-forward', 'Signin::forwardFacebook');
$routes->add('/sign-in-local', 'Signin::auth/local');
$routes->get('/sign-out', 'Signin::signout');

$routes->get('/export', 'Exam::export',['filter' => ['authGuard', 'chargeGuard']]);
$routes->get('/exam', 'Exam::index',['filter' => ['authGuard', 'chargeGuard']]);
$routes->get('/create', 'Exam::create',['filter' => ['authGuard', 'chargeGuard']]);
$routes->post('/create', 'Exam::store',['filter' => ['authGuard', 'chargeGuard']]);
$routes->get('/quiz/(:alphanum)', 'Quiz::index/$1',['filter' => 'authGuard']);
$routes->post('/quiz/hand-in', 'Quiz::handIn',['filter' => 'authGuard']);

$routes->get('/subscription', 'Charge::start');
$routes->get('/restore', 'Charge::restore',['filter' => 'authGuard']);
$routes->post('/purchase', 'Charge::purchase',['filter' => 'authGuard']);
$routes->get('/payment_success', 'Charge::purchase_success',['filter' => 'authGuard']);
$routes->get('/payment_cancel', 'Charge::purchase_cancel',['filter' => 'authGuard']);

$routes->post('/webhook', 'Webhook::index');



/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
