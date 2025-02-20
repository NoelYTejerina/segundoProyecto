<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/playlist', name: 'playlist_')]
class PlaylistController extends AbstractController
{
    /**
     * Lista todas las playlists.
     */
    #[Route('/', name: 'listar_playlists', methods: ['GET'])]
    public function listarPlaylists(PlaylistRepository $playlistRepository): JsonResponse
    {
        $playlists = $playlistRepository->findAll();
        return $this->json($playlists);
    }

    /**
     * Obtiene los detalles de una playlist específica.
     */
    #[Route('/{id}', name: 'ver_playlist', methods: ['GET'])]
    public function verPlaylist(int $id, PlaylistRepository $playlistRepository): JsonResponse
    {
        $playlist = $playlistRepository->find($id);
        if (!$playlist) {
            return $this->json(['message' => 'Playlist no encontrada'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($playlist);
    }

    /**
     * Crea una nueva playlist.
     */
    #[Route('/crear', name: 'crear_playlist', methods: ['POST'])]
    public function crearPlaylist(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data['nombre'])) {
            return $this->json(['message' => 'El nombre de la playlist es obligatorio'], Response::HTTP_BAD_REQUEST);
        }

        $playlist = new Playlist();
        $playlist->setNombre($data['nombre']);
        $playlist->setVisibilidad($data['visibilidad'] ?? 'publica');

        $em->persist($playlist);
        $em->flush();

        return $this->json(['message' => 'Playlist creada correctamente'], Response::HTTP_CREATED);
    }

    /**
     * Edita una playlist existente.
     */
    #[Route('/editar/{id}', name: 'editar_playlist', methods: ['PUT'])]
    public function editarPlaylist(int $id, Request $request, EntityManagerInterface $em, PlaylistRepository $playlistRepository): JsonResponse
    {
        $playlist = $playlistRepository->find($id);
        if (!$playlist) {
            return $this->json(['message' => 'Playlist no encontrada'], Response::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);

        $playlist->setNombre($data['nombre'] ?? $playlist->getNombre());
        $playlist->setVisibilidad($data['visibilidad'] ?? $playlist->getVisibilidad());

        $em->flush();
        return $this->json(['message' => 'Playlist actualizada correctamente']);
    }

    /**
     * Elimina una playlist.
     */
    #[Route('/eliminar/{id}', name: 'eliminar_playlist', methods: ['DELETE'])]
    public function eliminarPlaylist(int $id, EntityManagerInterface $em, PlaylistRepository $playlistRepository): JsonResponse
    {
        $playlist = $playlistRepository->find($id);
        if (!$playlist) {
            return $this->json(['message' => 'Playlist no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($playlist);
        $em->flush();

        return $this->json(['message' => 'Playlist eliminada correctamente']);
    }

    /**
     * Obtiene todas las playlists creadas por un usuario específico.
     */
    #[Route('/usuario/{usuarioId}', name: 'playlists_por_usuario', methods: ['GET'])]
    public function playlistsPorUsuario(int $usuarioId, PlaylistRepository $playlistRepository): JsonResponse
    {
        $playlists = $playlistRepository->findBy(['propietario' => $usuarioId]);
        return $this->json($playlists);
    }

    /**
     * Obtiene todas las playlists recientes.
     */
    #[Route('/recientes/{limit}', name: 'playlists_recientes', methods: ['GET'])]
    public function playlistsRecientes(int $limit, PlaylistRepository $playlistRepository): JsonResponse
    {
        $playlists = $playlistRepository->findRecentPlaylists($limit);
        return $this->json($playlists);
    }

    /**
     * Obtiene todas las canciones dentro de una playlist.
     */
    #[Route('/{playlistId}/canciones', name: 'canciones_por_playlist', methods: ['GET'])]
    public function cancionesPorPlaylist(int $playlistId, PlaylistRepository $playlistRepository): JsonResponse
    {
        $playlist = $playlistRepository->find($playlistId);
        if (!$playlist) {
            return $this->json(['message' => 'Playlist no encontrada'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($playlist->getCanciones());
    }

    // Obtiene las playlists con más "likes"
    #[Route('/top-likes/{limit}', name: 'top_playlists_mas_likes', methods: ['GET'])]
    public function topPlaylistsMasLikes(int $limit, PlaylistRepository $playlistRepository): JsonResponse
    {
        return $this->json($playlistRepository->findTopLikedPlaylists($limit));
    }

    // Muestra las playlists escuchadas por un usuario específico
    #[Route('/escuchadas/{usuarioId}', name: 'playlists_escuchadas_por_usuario', methods: ['GET'])]
    public function playlistsEscuchadasPorUsuario(int $usuarioId, PlaylistRepository $playlistRepository): JsonResponse
    {
        return $this->json($playlistRepository->findPlaylistsEscuchadasByUsuario($usuarioId));
    }
}
