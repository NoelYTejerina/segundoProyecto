<?php
namespace App\EventListener;

use App\Service\LoggerActividadService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginListener
{
    private LoggerActividadService $logger;

    public function __construct(LoggerActividadService $logger)
    {
        $this->logger = $logger;
    }

    #[AsEventListener(event: LoginSuccessEvent::class)]
    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $usuario = $event->getUser();

        if ($usuario) {
            $this->logger->log($usuario->getUserIdentifier(), "Inicio de sesión");
        }
    }
}
