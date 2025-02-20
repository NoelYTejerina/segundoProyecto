<?php

namespace App\Controller;

use App\Repository\PlaylistRepository;
use App\Repository\PlaylistCancionRepository;
use App\Repository\UsuarioRepository;
use App\Repository\CancionRepository;
use App\Repository\UsuarioCancionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EstadisticasController extends AbstractController
{
    #[Route('/estadisticas', name: 'estadisticas')]
    public function index(): Response
    {
        return $this->render('estadisticas/index.html.twig');
    }

    #[Route('/estadisticas/playlists-likes', name: 'estadisticas_playlists_likes')]
    public function playlistsConMasLikes(PlaylistRepository $playlistRepository): JsonResponse
    {
        $datos = $playlistRepository->findMostPopular(10);
        return $this->json($datos);
    }

    #[Route('/estadisticas/playlists-reproducciones', name: 'estadisticas_playlists_reproducciones')]
    public function playlistsConMasReproducciones(PlaylistCancionRepository $playlistCancionRepository): JsonResponse
    {
        $datos = $playlistCancionRepository->findMostPlayed(10);
        return $this->json($datos);
    }

    #[Route('/estadisticas/usuarios-edades', name: 'estadisticas_usuarios_edades')]
    public function distribucionEdadesUsuarios(UsuarioRepository $usuarioRepository): JsonResponse
    {
        $datos = $usuarioRepository->obtenerDistribucionEdades();
        return $this->json($datos);
    }

    #[Route('/estadisticas/canciones-reproducidas', name: 'estadisticas_canciones_reproducidas')]
    public function cancionesMasReproducidas(UsuarioCancionRepository $usuarioCancionRepository): JsonResponse
    {
        $datos = $usuarioCancionRepository->findTopReproductionsByUsuario(10, 10);
        return $this->json($datos);
    }

    #[Route('/estadisticas/estilos-reproducciones', name: 'estadisticas_estilos_reproducciones')]
    public function distribucionReproduccionesEstilos(PlaylistCancionRepository $playlistCancionRepository): JsonResponse
    {
        $datos = $playlistCancionRepository->obtenerReproduccionesPorEstilo();
        return $this->json($datos);
    }
}
