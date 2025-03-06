<?php
namespace App\Controller;

use App\Repository\PlaylistRepository;
use App\Repository\CancionRepository;
use App\Repository\UsuarioRepository;
use App\Repository\EstiloRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_MANAGER')]
#[Route('/manager')]
final class EstadisticasController extends AbstractController
{
    #[Route('/estadisticas', name: 'estadisticas')]
    public function index(): Response
    {
        return $this->render('estadisticas/estadisticas.html.twig');
    }

    #[Route('/datos_likes', name: 'likes_datos')]
    public function obtenerDatosLikes(PlaylistRepository $repositorio): Response
    {
        return $this->json($repositorio->obtenerLikesPorPlaylist());
    }

    #[Route('/datos_reproducciones', name: 'estadisticas_datos')]
    public function obtenerDatosReproducciones(PlaylistRepository $repositorio): Response
    {
        return $this->json($repositorio->obtenerReproduccionesPorPlaylist());
    }

    #[Route('/datos_canciones_mas_reproducidas', name: 'canciones_reprod_datos')]
    public function obtenerCancionesMasReproducidas(CancionRepository $repositorio): Response
    {
        return $this->json($repositorio->obtenerCancionesMasReproducidas());
    }

    #[Route('/datos_edad', name: 'edad_datos')]
    public function clasificarFranjaEdad(UsuarioRepository $repositorio): Response
    {
        return $this->json($repositorio->clasificarUsuariosXedad());
    }

    #[Route('/datos_reproducciones_estilo', name: 'estilos_reprod_datos')]
    public function obtenerReproduccionesPorEstilo(EstiloRepository $repositorio): Response
    {
        return $this->json($repositorio->obtenerReproduccionesPorEstilo());
    }
}
