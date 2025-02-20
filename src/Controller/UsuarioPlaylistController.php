<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Entity\Playlist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/usuario-playlist', name: 'usuario_playlist_')]
class UsuarioPlaylistController extends AbstractController
{
    /**
     * Registra que un usuario ha escuchado una playlist
     */
    #[Route('/escuchar/{usuarioId}/{playlistId}', name: 'marcar_playlist_escuchada', methods: ['POST'])]
    public function marcarPlaylistComoEscuchada(int $usuarioId, int $playlistId, EntityManagerInterface $em): JsonResponse
    {
        $usuario = $em->getRepository(Usuario::class)->find($usuarioId);
        $playlist = $em->getRepository(Playlist::class)->find($playlistId);

        if (!$usuario || !$playlist) {
            return $this->json(['error' => 'Usuario o Playlist no encontrados'], 404);
        }

        $usuario->addPlaylistsEscuchada($playlist);
        $em->flush();

        return $this->json(['message' => 'Playlist marcada como escuchada']);
    }

    /**
     * Muestra las playlists que ha escuchado un usuario
     */
    #[Route('/escuchadas/{usuarioId}', name: 'ver_playlists_escuchadas', methods: ['GET'])]
    public function verPlaylistsEscuchadas(int $usuarioId, EntityManagerInterface $em): JsonResponse
    {
        $usuario = $em->getRepository(Usuario::class)->find($usuarioId);

        if (!$usuario) {
            return $this->json(['error' => 'Usuario no encontrado'], 404);
        }

        return $this->json($usuario->getPlaylistsEscuchadas());
    }

    /**
     * Muestra los usuarios que han escuchado una playlist
     */
    #[Route('/usuarios-que-escucharon/{playlistId}', name: 'ver_usuarios_que_escucharon_playlist', methods: ['GET'])]
    public function verUsuariosQueEscucharonPlaylist(int $playlistId, EntityManagerInterface $em): JsonResponse
    {
        $playlist = $em->getRepository(Playlist::class)->find($playlistId);

        if (!$playlist) {
            return $this->json(['error' => 'Playlist no encontrada'], 404);
        }

        return $this->json($playlist->getUsuariosQueEscuchan());
    }
}
