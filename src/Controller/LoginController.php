<?php

namespace App\Controller;

use App\Service\LoggerActividadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\SecurityBundle\Security;

class LoginController extends AbstractController
{
    private LoggerActividadService $logger;
    private Security $security;

    public function __construct(LoggerActividadService $logger, Security $security)
    {
        $this->logger = $logger;
        $this->security = $security;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Obtener error de autenticación si existe
        $error = $authenticationUtils->getLastAuthenticationError();
        // Último usuario ingresado
        $lastUsername = $authenticationUtils->getLastUsername();

        // Si el usuario ya está autenticado, registrar el inicio de sesión
        if ($this->getUser()) {
            $this->logger->log($this->getUser()->getUserIdentifier(), "Inicio de sesión");
            return $this->redirectToRoute('homepage'); // Redirigir a la página principal
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Registrar cierre de sesión
        $usuario = $this->security->getUser();
        if ($usuario) {
            $this->logger->log($usuario->getUserIdentifier(), "Cierre de sesión");
        }

        throw new \LogicException('Este método será interceptado por Symfony y nunca se ejecutará.');
    }
}
