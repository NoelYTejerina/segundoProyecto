<?php

namespace App\Controller;

use App\Form\PlaylistType;
use App\Entity\Playlist;
use App\Entity\PlaylistCancion;
use App\Entity\Cancion;
use App\Enum\VisibilidadPlaylist;
use App\Repository\PlaylistRepository;
use App\Repository\CancionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/playlist', name: 'playlist_')]
class PlaylistController extends AbstractController
{
    
    /**
     * Muestra el formulario para crear una playlist.
     */
    #[Route('/crear', name: 'crear_playlist_form', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function mostrarFormularioCrearPlaylist(Request $request): Response
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
    
        return $this->render('playlist/crear_playlist.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Procesa el formulario y crea una nueva playlist.
     */
    #[Route('/crear', name: 'crear_playlist', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function crearPlaylist(Request $request, EntityManagerInterface $em, CancionRepository $cancionRepository): Response
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $usuario = $this->getUser();
            $playlist->setPropietario($usuario);
    
            $em->persist($playlist);
    
            // Agregar canciones seleccionadas
            $cancionesSeleccionadas = $form->get('canciones')->getData();
            foreach ($cancionesSeleccionadas as $cancion) {
                $playlistCancion = new PlaylistCancion();
                $playlistCancion->setPlaylist($playlist);
                $playlistCancion->setCancion($cancion);
                $em->persist($playlistCancion);
            }
    
            $em->flush();
    
            // Agregar mensaje flash
            $this->addFlash('success', '🎵 Playlist creada correctamente.');
    
            return $this->redirectToRoute('playlist_mis_playlists'); // Nueva ruta para ver playlists del usuario
        }
    
        return $this->render('playlist/crear_playlist.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    

    /**
     * Búsqueda por patrón en Playlists y Canciones.
     */
    #[Route('/buscar', name: 'buscar_playlist', methods: ['GET'])]
    public function buscarPlaylist(
        Request $request,
        PlaylistRepository $playlistRepository,
        CancionRepository $cancionRepository
    ): JsonResponse {
        $query = $request->query->get('q');
        if (!$query) {
            return $this->json(['message' => 'Debe proporcionar un término de búsqueda'], Response::HTTP_BAD_REQUEST);
        }

        // Buscar en playlists y canciones
        $playlists = $playlistRepository->buscarPorPatron($query);
        $canciones = $cancionRepository->buscarPorPatron($query);
        
        return $this->json([
            'playlists' => $playlists,
            'canciones' => $canciones
        ]);
    }

    /**
     * Vista principal con:
     * - Playlists generales
     * - Canciones disponibles
     * - Playlists del usuario autenticado
     */
    #[Route('/vista-principal', name: 'vista_principal', methods: ['GET'])]
    public function vistaPrincipal(
        PlaylistRepository $playlistRepository,
        CancionRepository $cancionRepository
    ): JsonResponse {
        $usuario = $this->getUser();

        return $this->json([
            'playlists_generales' => $playlistRepository->findPublicPlaylists(),
            'canciones' => $cancionRepository->findAll(),
            'mis_playlists' => $usuario ? $playlistRepository->findBy(['propietario' => $usuario]) : []
        ]);
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
     * Lista todas las playlists.
     */
    #[Route('/listar', name: 'listar_playlists', methods: ['GET'])]
    public function listarPlaylists(PlaylistRepository $playlistRepository): JsonResponse
    {
        $playlists = $playlistRepository->findAll();
        return $this->json($playlists);
    }
/**
 * Muestra los detalles de una playlist específica.
 */
#[Route('/{id}', name: 'ver_playlist', methods: ['GET'], requirements: ['id' => '\d+'])]
public function verPlaylist(int $id, PlaylistRepository $playlistRepository): Response
{
    $playlist = $playlistRepository->find($id);
    
    if (!$playlist) {
        throw $this->createNotFoundException('Playlist no encontrada.');
    }

    return $this->render('playlist/ver_playlist.html.twig', [
        'playlist' => $playlist,
    ]);
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

        // Obtener las canciones de la playlist
        $canciones = $playlist->getCanciones();

        // Formatear la respuesta JSON
        $response = [];
        foreach ($canciones as $cancion) {
            $response[] = [
                'id' => $cancion->getId(),
                'titulo' => $cancion->getTitulo(),
                'archivo' => $cancion->getArchivo(),
            ];
        }

        return $this->json($response);
    }

    /**
     * Obtiene las playlists con más "likes".
     */
    #[Route('/top-likes/{limit}', name: 'top_playlists_mas_likes', methods: ['GET'])]
    public function topPlaylistsMasLikes(int $limit, PlaylistRepository $playlistRepository): JsonResponse
    {
        return $this->json($playlistRepository->findTopLikedPlaylists($limit));
    }

    /**
     * Muestra las playlists escuchadas por un usuario específico.
     */
    #[Route('/escuchadas/{usuarioId}', name: 'playlists_escuchadas_por_usuario', methods: ['GET'])]
    public function playlistsEscuchadasPorUsuario(int $usuarioId, PlaylistRepository $playlistRepository): JsonResponse
    {
        return $this->json($playlistRepository->findPlaylistsEscuchadasByUsuario($usuarioId));
    }

    /**
     * Playlist de usuario autenticado
     */
    #[Route('/mis', name: 'mis_playlists', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function misPlaylists(PlaylistRepository $playlistRepository): Response
    {
        $usuario = $this->getUser();
        $playlists = $playlistRepository->findBy(['propietario' => $usuario]);
    
        //dump($playlists); die; // 🔍 Verificar que obtenemos datos
    
        return $this->render('playlist/mis_playlists.html.twig', [
            'playlists' => $playlists,
        ]);
    }
    

}