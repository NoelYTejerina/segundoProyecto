<?php
namespace App\EventListener;

use App\Service\LoggerActividadService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener
{
    private LoggerActividadService $logger;

    public function __construct(LoggerActividadService $logger)
    {
        $this->logger = $logger;
    }

    #[AsEventListener(event: LogoutEvent::class)]
    public function onLogout(LogoutEvent $event): void
    {
        $usuario = $event->getToken() ? $event->getToken()->getUser() : null;

        if ($usuario) {
            $this->logger->log($usuario->getUserIdentifier(), "Cierre de sesión");
        }
    }
}
