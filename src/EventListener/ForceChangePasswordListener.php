<?php

namespace App\EventListener;

use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;


class ForceChangePasswordListener
{
    
    private $security;
    private $router;
    private $session;

    public function __construct(Router $router, Security $security, Session $session)
    {
        $this->security = $security;
        $this->router 		= $router;
        $this->session 		= $session;
        
    }
    
    public function onPasswordExpired(RequestEvent $event)
    {

        if ( ($this->security->getToken() ) && ( $this->security->isGranted('IS_AUTHENTICATED_FULLY') ) ) {
            
            $route_name = $event->getRequest()->get('_route');
            
            if ($route_name != 'force_change_password') {

                $password_validity_days     = 90;
                
                $today                  = new \DateTime();
                $days_since_last_change = $this->security->getToken()->getUser()->getPasswordChangedAt()->diff($today);

                if ($days_since_last_change->format('%a') >  $password_validity_days ) {

                    $response = new RedirectResponse($this->router->generate('force_change_password'));
                    $this->session->setFlash('error', "Votre mot de passe a expiré. Merci de le changer");
                    $event->setResponse($response);                

                }
                
            }
            
        }

    }
    
}