<?php

namespace App\Controller;

use App\Entity\Perfil;
use App\Repository\PerfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/perfil', name: 'perfil_')]
class PerfilController extends AbstractController
{
    /**
     * Muestra los detalles del perfil de un usuario.
     */
    #[Route('/{id}', name: 'ver_perfil', methods: ['GET'])]
    public function verPerfil(int $id, PerfilRepository $perfilRepository): JsonResponse
    {
        $perfil = $perfilRepository->find($id);

        if (!$perfil) {
            return $this->json(['message' => 'Perfil no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $perfil->getId(),
            'foto' => $perfil->getFoto(),
            'descripcion' => $perfil->getDescripcion(),
            'apellidos' => $perfil->getApellidos(),
            'ocupacion' => $perfil->getOcupacion(),
            'genero' => $perfil->getGenero(),
            'pais' => $perfil->getPais(),
            'usuario_id' => $perfil->getUsuario()->getId(),
        ]);
    }

    /**
     * Permite editar los datos del perfil de un usuario.
     */
    #[Route('/editar/{id}', name: 'editar_perfil', methods: ['PUT'])]
    public function editarPerfil(int $id, Request $request, EntityManagerInterface $em, PerfilRepository $perfilRepository): JsonResponse
    {
        $perfil = $perfilRepository->find($id);

        if (!$perfil) {
            return $this->json(['message' => 'Perfil no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $perfil->setDescripcion($data['descripcion'] ?? $perfil->getDescripcion());
        $perfil->setApellidos($data['apellidos'] ?? $perfil->getApellidos());
        $perfil->setOcupacion($data['ocupacion'] ?? $perfil->getOcupacion());
        $perfil->setGenero($data['genero'] ?? $perfil->getGenero());
        $perfil->setPais($data['pais'] ?? $perfil->getPais());

        $em->flush();

        return $this->json(['message' => 'Perfil actualizado correctamente']);
    }

    /**
     * Permite eliminar un perfil (opcional, ya que se elimina en cascada con el usuario).
     */
    #[Route('/eliminar/{id}', name: 'eliminar_perfil', methods: ['DELETE'])]
    public function eliminarPerfil(int $id, EntityManagerInterface $em, PerfilRepository $perfilRepository): JsonResponse
    {
        $perfil = $perfilRepository->find($id);

        if (!$perfil) {
            return $this->json(['message' => 'Perfil no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($perfil);
        $em->flush();

        return $this->json(['message' => 'Perfil eliminado correctamente']);
    }

    /**
     * Devuelve los usuarios que tienen un estilo como favorito.
     */
    #[Route('/usuarios-por-estilo/{estiloId}', name: 'usuarios_por_estilo', methods: ['GET'])]
    public function usuariosPorEstilo(int $estiloId, PerfilRepository $perfilRepository): JsonResponse
    {
        $perfiles = $perfilRepository->findByEstiloFavorito($estiloId);

        return $this->json(array_map(fn($perfil) => [
            'id' => $perfil->getId(),
            'usuario_id' => $perfil->getUsuario()->getId(),
            'nombre' => $perfil->getUsuario()->getNombre(),
            'pais' => $perfil->getPais(),
        ], $perfiles));
    }

    /**
     * Permite cambiar la foto de perfil de un usuario.
     */
    #[Route('/cambiar-foto/{id}', name: 'cambiar_foto_perfil', methods: ['POST'])]
    public function cambiarFotoPerfil(int $id, Request $request, EntityManagerInterface $em, PerfilRepository $perfilRepository): JsonResponse
    {
        $perfil = $perfilRepository->find($id);

        if (!$perfil) {
            return $this->json(['message' => 'Perfil no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $foto = $data['foto'] ?? null;

        if (!$foto) {
            return $this->json(['message' => 'Se requiere una URL de foto'], Response::HTTP_BAD_REQUEST);
        }

        $perfil->setFoto($foto);
        $em->flush();

        return $this->json(['message' => 'Foto de perfil actualizada correctamente']);
    }

    // Devuelve los usuarios que pertenecen a un país específico
    #[Route('/usuarios-pais/{pais}', name: 'usuarios_por_pais', methods: ['GET'])]
    public function usuariosPorPais(string $pais, PerfilRepository $perfilRepository): JsonResponse
    {
        return $this->json($perfilRepository->findUsuariosByPais($pais));
    }
}
