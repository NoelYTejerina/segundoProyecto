<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Entity\Cancion;
use App\Entity\UsuarioCancion;
use App\Repository\UsuarioCancionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/usuario-cancion', name: 'usuario_cancion_')]
class UsuarioCancionController extends AbstractController
{
    /**
     * Registra una reproducción de una canción por parte de un usuario
     */
    #[Route('/reproducir/{usuarioId}/{cancionId}', name: 'registrar_reproduccion', methods: ['POST'])]
    public function registrarReproduccion(int $usuarioId, int $cancionId, EntityManagerInterface $em): JsonResponse
    {
        $usuario = $em->getRepository(Usuario::class)->find($usuarioId);
        $cancion = $em->getRepository(Cancion::class)->find($cancionId);

        if (!$usuario || !$cancion) {
            return $this->json(['error' => 'Usuario o Canción no encontrados'], 404);
        }

        // Buscar si ya existe el registro de reproducción
        $usuarioCancionRepo = $em->getRepository(UsuarioCancion::class);
        $usuarioCancion = $usuarioCancionRepo->findOneBy([
            'usuario' => $usuario,
            'cancion' => $cancion
        ]);

        if ($usuarioCancion) {
            // Incrementar el contador de reproducciones
            $usuarioCancion->setReproducciones($usuarioCancion->getReproducciones() + 1);
        } else {
            // Crear nuevo registro de reproducción
            $usuarioCancion = new UsuarioCancion();
            $usuarioCancion->setUsuario($usuario);
            $usuarioCancion->setCancion($cancion);
            $usuarioCancion->setReproducciones(1);
            $em->persist($usuarioCancion);
        }

        $em->flush();

        return $this->json(['message' => 'Reproducción registrada con éxito']);
    }

    /**
     * Obtiene el historial de reproducciones de un usuario
     */
    #[Route('/historial/{usuarioId}', name: 'ver_historial_reproducciones', methods: ['GET'])]
    public function verHistorialReproducciones(int $usuarioId, UsuarioCancionRepository $repo): JsonResponse
    {
        $reproducciones = $repo->findBy(['usuario' => $usuarioId]);

        return $this->json($reproducciones);
    }

    /**
     * Devuelve las canciones más reproducidas
     */
    #[Route('/top-reproducidas/{limit}', name: 'top_canciones_reproducidas', methods: ['GET'])]
    public function topCancionesReproducidas(int $limit, UsuarioCancionRepository $repo): JsonResponse
    {
        return $this->json($repo->findTopReproducciones($limit));
    }
}
