<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Repository\CancionRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/buscar', name: 'buscar_')]
class BusquedaController extends AbstractController
{
    #[Route('/general', name: 'general', methods: ['GET'])]
    public function buscarGeneral(
        Request $request,
        CancionRepository $cancionRepository,
        PlaylistRepository $playlistRepository
    ): JsonResponse {
        $query = $request->query->get('q');

        if (!$query) {
            return $this->json(['error' => 'Debe proporcionar un término de búsqueda'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Buscar en Canciones (por título y artista)
        $canciones = $cancionRepository->buscarPorPatron($query);
        $cancionesArray = array_map(fn($c) => [
            'id' => $c->getId(),
            'titulo' => $c->getTitulo(),
            'autor' => $c->getAutor() ?? 'Desconocido'
        ], $canciones);

        // Buscar en Playlists (por nombre)
        $playlists = $playlistRepository->buscarPorPatron($query);
        $playlistsArray = array_map(fn($p) => [
            'id' => $p->getId(),
            'nombre' => $p->getNombre()
        ], $playlists);

        return $this->json([
            'canciones' => $cancionesArray,
            'playlists' => $playlistsArray,
        ]);
    }
    
    #[Route('/resultados', name: 'resultados', methods: ['GET'])]
    public function mostrarResultados(
        Request $request,
        CancionRepository $cancionRepository,
        PlaylistRepository $playlistRepository
    ): Response {
        $query = $request->query->get('q');

        if (!$query) {
            $this->addFlash('error', 'Debe introducir un término de búsqueda.');
            return $this->redirectToRoute('home');
        }

        // Buscar en canciones
        $canciones = $cancionRepository->buscarPorPatron($query);

        // Buscar en playlists y calcular datos adicionales
        $playlists = $playlistRepository->buscarPorPatron($query);
        $playlistsConDatos = [];

        foreach ($playlists as $playlist) {
            $totalCanciones = count($playlist->getCanciones());
            $totalDuracion = array_sum(array_map(fn($c) => $c->getDuracion(), $playlist->getCanciones()));

            $playlistsConDatos[] = [
                'playlist' => $playlist,
                'totalCanciones' => $totalCanciones,
                'totalDuracion' => $totalDuracion
            ];
        }

        return $this->render('busqueda/resultados.html.twig', [
            'query' => $query,
            'canciones' => $canciones,
            'playlists' => $playlistsConDatos,
        ]);
    }
}
