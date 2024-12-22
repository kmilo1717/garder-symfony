<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthSubscriber implements EventSubscriberInterface
{
    private $router;
    private $session;

    public function __construct(RouterInterface $router, SessionInterface $session)
    {
        $this->router = $router;
        $this->session = $session;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route');

        // Rutas públicas que no requieren autenticación
        $publicRoutes = ['login', 'register'];

        // Si la ruta no es pública y el usuario no está autenticado, redirige al login
        if (!in_array($currentRoute, $publicRoutes) && !$this->session->has('user')) {
            $event->setController(function () {
                return new RedirectResponse($this->router->generate('login'));
            });
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
