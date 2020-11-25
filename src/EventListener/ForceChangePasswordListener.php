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

            if ($route_name!=null && $route_name != 'app_change_password') {

                $password_validity_days     = 180;

                $today                  = new \DateTime();
                $user = $this->security->getToken()->getUser();
                $days_since_last_change = $user->getPasswordChangedAt()->diff($today);

                if ($days_since_last_change->format('%a') >  $password_validity_days ) {

                    $this->session->getFlashBag()->add('error', "Votre mot de passe a expiré. Merci de le changer");
                    $response = new RedirectResponse($this->router->generate('app_change_password', ['id'=>$user->getId()]));
                    $event->setResponse($response);

                }

            }

        }
        return;
    }
    
}