<?php 
namespace App\Filters;

use App\Libraries\StripeApi;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class ChargeGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (ENVIRONMENT === 'production') {
            $session = session();
            if (!$session->get('is_paid'))
            {
                return redirect()->to('/subscription');
            }
        }
        
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}