<?php

namespace App\Controller;

use App\Repository\CancionRepository;
use App\Repository\PlaylistCancionRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        PlaylistRepository $playlistRepository,
        PlaylistCancionRepository $playlistCancionRepository,
        CancionRepository $cancionRepository // ✅ Agregar CancionRepository aquí
    ): Response {
        // Obtener las últimas 3 playlists añadidas
        $playlists = $playlistRepository->findRecentPlaylists(5);

        $playlistData = []; // ✅ Se cambia `$data` por `$playlistData` para evitar errores
        foreach ($playlists as $playlist) {
            $stats = $playlistCancionRepository->getPlaylistStats($playlist->getId());

            $playlistData[] = [
                'playlist' => $playlist,
                'totalCanciones' => $stats['totalCanciones'] ?? 0,
                'totalDuracion' => gmdate("i:s", $stats['totalDuracion'] ?? 0),
            ];
        }

        // ✅ Obtener las últimas 3 canciones añadidas correctamente
        $canciones = $cancionRepository->findRecentCanciones(5);

        return $this->render('main/index.html.twig', [
            'playlists' => $playlistData, 
            'canciones' => $canciones
        ]);
    }
}
