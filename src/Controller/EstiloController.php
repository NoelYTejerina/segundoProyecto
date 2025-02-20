<?php

namespace App\Controller;

use App\Entity\Estilo;
use App\Repository\EstiloRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/estilo', name: 'estilo_')]
class EstiloController extends AbstractController
{
    /**
     * Lista todos los estilos musicales.
     */
    #[Route('/', name: 'listar_estilos', methods: ['GET'])]
    public function listarEstilos(EstiloRepository $estiloRepository): JsonResponse
    {
        $estilos = $estiloRepository->findAll();
        return $this->json(array_map(fn($estilo) => [
            'id' => $estilo->getId(),
            'nombre' => $estilo->getNombre(),
            'descripcion' => $estilo->getDescripcion(),
            'subgeneros' => $estilo->getSubgeneros()
        ], $estilos));
    }

    /**
     * Obtiene la información de un estilo específico.
     */
    #[Route('/{id}', name: 'ver_estilo', methods: ['GET'])]
    public function verEstilo(int $id, EstiloRepository $estiloRepository): JsonResponse
    {
        $estilo = $estiloRepository->find($id);
        if (!$estilo) {
            return $this->json(['message' => 'Estilo no encontrado'], Response::HTTP_NOT_FOUND);
        }
        return $this->json([
            'id' => $estilo->getId(),
            'nombre' => $estilo->getNombre(),
            'descripcion' => $estilo->getDescripcion(),
            'subgeneros' => $estilo->getSubgeneros()
        ]);
    }

    /**
     * Crea un nuevo estilo musical.
     */
    #[Route('/crear', name: 'crear_estilo', methods: ['POST'])]
    public function crearEstilo(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data['nombre'])) {
            return $this->json(['message' => 'El nombre del estilo es obligatorio'], Response::HTTP_BAD_REQUEST);
        }
        $estilo = new Estilo();
        $estilo->setNombre($data['nombre']);
        $estilo->setDescripcion($data['descripcion'] ?? null);
        $estilo->setSubgeneros($data['subgeneros'] ?? null);

        $em->persist($estilo);
        $em->flush();
        return $this->json(['message' => 'Estilo creado correctamente'], Response::HTTP_CREATED);
    }

    /**
     * Edita un estilo existente.
     */
    #[Route('/editar/{id}', name: 'editar_estilo', methods: ['PUT'])]
    public function editarEstilo(int $id, Request $request, EntityManagerInterface $em, EstiloRepository $estiloRepository): JsonResponse
    {
        $estilo = $estiloRepository->find($id);
        if (!$estilo) {
            return $this->json(['message' => 'Estilo no encontrado'], Response::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);
        $estilo->setNombre($data['nombre'] ?? $estilo->getNombre());
        $estilo->setDescripcion($data['descripcion'] ?? $estilo->getDescripcion());
        $estilo->setSubgeneros($data['subgeneros'] ?? $estilo->getSubgeneros());

        $em->flush();
        return $this->json(['message' => 'Estilo actualizado correctamente']);
    }

    /**
     * Elimina un estilo musical.
     */
    #[Route('/eliminar/{id}', name: 'eliminar_estilo', methods: ['DELETE'])]
    public function eliminarEstilo(int $id, EntityManagerInterface $em, EstiloRepository $estiloRepository): JsonResponse
    {
        $estilo = $estiloRepository->find($id);
        if (!$estilo) {
            return $this->json(['message' => 'Estilo no encontrado'], Response::HTTP_NOT_FOUND);
        }
        $em->remove($estilo);
        $em->flush();
        return $this->json(['message' => 'Estilo eliminado correctamente']);
    }

    /**
     * Devuelve todas las canciones asociadas a un estilo.
     */
    #[Route('/{id}/canciones', name: 'canciones_por_estilo', methods: ['GET'])]
    public function cancionesPorEstilo(int $id, EstiloRepository $estiloRepository): JsonResponse
    {
        $canciones = $estiloRepository->findCancionesByEstilo($id);
        return $this->json($canciones);
    }

    /**
     * Devuelve todas las playlists asociadas a un estilo.
     */
    #[Route('/{id}/playlists', name: 'playlists_por_estilo', methods: ['GET'])]
    public function playlistsPorEstilo(int $id, EstiloRepository $estiloRepository): JsonResponse
    {
        $playlists = $estiloRepository->findPlaylistsByEstilo($id);
        return $this->json($playlists);
    }

    // Devuelve todos los usuarios que tienen un estilo como favorito
    #[Route('/usuarios/{estiloId}', name: 'usuarios_por_estilo_favorito', methods: ['GET'])]
    public function usuariosPorEstiloFavorito(int $estiloId, EstiloRepository $estiloRepository): JsonResponse
    {
        return $this->json($estiloRepository->findUsuariosByEstilo($estiloId));
    }
}
