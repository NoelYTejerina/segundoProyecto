<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile')]
    public function index(): Response
    {
        // Asegúrate de que el usuario esté autenticado
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Obtén el usuario actual
        $user = $this->getUser();

        return $this->render('user_profile/index.html.twig', [
            'user' => $user,
        ]);
    }
}