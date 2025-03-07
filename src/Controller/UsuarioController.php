<?php

namespace App\Controller;

use App\Service\LoggerActividadService;
use Symfony\Bundle\SecurityBundle\Security;

use App\Entity\Usuario;
use App\Entity\Perfil;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/usuario', name: 'usuario_')]
class UsuarioController extends AbstractController
{
    /**
     * Lista todos los usuarios.
     */
    #[Route('/', name: 'listar_usuarios', methods: ['GET'])]
    public function listarUsuarios(UsuarioRepository $usuarioRepository): JsonResponse
    {
        $usuarios = $usuarioRepository->findAll();
        return $this->json(array_map(fn($usuario) => [
            'id' => $usuario->getId(),
            'nombre' => $usuario->getNombre(),
            'email' => $usuario->getEmail(),
            'roles' => $usuario->getRoles() // Usamos getRoles() en lugar de getRol()
        ], $usuarios));
    }

    /**
     * Busca un usuario por su ID.
     */
    #[Route('/buscar/id/{id}', name: 'buscar_usuario_id', methods: ['GET'])]
    public function buscarUsuarioPorId(int $id, UsuarioRepository $usuarioRepository): JsonResponse
    {
        $usuario = $usuarioRepository->find($id);
        if (!$usuario) {
            return $this->json(['message' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }
        return $this->json([
            'id' => $usuario->getId(),
            'nombre' => $usuario->getNombre(),
            'email' => $usuario->getEmail(),
            'roles' => $usuario->getRoles() // Usamos getRoles() en lugar de getRol()
        ]);
    }

    /**
     * Busca usuarios por nombre (búsqueda parcial).
     */
    #[Route('/buscar/nombre/{nombre}', name: 'buscar_usuario_nombre', methods: ['GET'])]
    public function buscarUsuarioPorNombre(string $nombre, UsuarioRepository $usuarioRepository): JsonResponse
    {
        $usuarios = $usuarioRepository->findByNombre($nombre);
        return $this->json(array_map(fn($usuario) => [
            'id' => $usuario->getId(),
            'nombre' => $usuario->getNombre(),
            'email' => $usuario->getEmail(),
            'roles' => $usuario->getRoles() // Usamos getRoles() en lugar de getRol()
        ], $usuarios));
    }

    public function registrarUsuario(Request $request, EntityManagerInterface $em, UsuarioRepository $usuarioRepository, LoggerActividadService $logger): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
    
        if (empty($email) || empty($data['password']) || empty($data['nombre'])) {
            return $this->json(['message' => 'Los campos email, password y nombre son obligatorios'], Response::HTTP_BAD_REQUEST);
        }
    
        if ($usuarioRepository->findOneBy(['email' => $email])) {
            return $this->json(['message' => 'Este email ya está registrado'], Response::HTTP_CONFLICT);
        }
    
        $usuario = new Usuario();
        $usuario->setEmail($email);
        $usuario->setNombre($data['nombre']);
        $usuario->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        $usuario->setRoles([$data['roles'] ?? 'ROLE_USER']);
    
        $perfil = new Perfil();
        $perfil->setUsuario($usuario);
    
        $em->persist($usuario);
        $em->persist($perfil);
        $em->flush();
    
        // 🔹 Registrar en el log
        $logger->log($email, "Usuario registrado exitosamente");
    
        return $this->json(['message' => 'Usuario registrado correctamente'], Response::HTTP_CREATED);
    }
    

    /**
     * Edita un usuario existente.
     */
    #[Route('/editar/{id}', name: 'editar_usuario', methods: ['PUT'])]
    public function editarUsuario(int $id, Request $request, EntityManagerInterface $em, UsuarioRepository $usuarioRepository): JsonResponse
    {
        $usuario = $usuarioRepository->find($id);

        if (!$usuario) {
            return $this->json(['message' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $usuario->setNombre($data['nombre'] ?? $usuario->getNombre());
        $usuario->setFechaNacimiento(!empty($data['fecha_nacimiento']) ? new \DateTime($data['fecha_nacimiento']) : $usuario->getFechaNacimiento());

        if (isset($data['roles'])) {
            $usuario->setRoles($data['roles']); // Actualizamos los roles si se proporcionan
        }

        $em->flush();

        return $this->json(['message' => 'Usuario actualizado correctamente']);
    }

    /**
 * Elimina un usuario.
 */
#[Route('/eliminar/{id<\d+>}', name: 'eliminar_usuario', methods: ['DELETE'])]
public function eliminarUsuario(int $id, EntityManagerInterface $em, UsuarioRepository $usuarioRepository, LoggerActividadService $logger): JsonResponse
{
    $usuario = $usuarioRepository->find($id);

    if (!$usuario) {
        return $this->json(['message' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
    }

    // 🔹 Registrar en el log antes de eliminar
    $logger->log($usuario->getEmail(), "Eliminó su cuenta");

    $em->remove($usuario);
    $em->flush();

    return $this->json(['message' => 'Usuario eliminado correctamente']);
}

    

     /**
     * Obtiene los detalles de un usuario, incluyendo sus relaciones.
     */
    #[Route('/{id<\d+>}', name: 'detalle_usuario', methods: ['GET'])]
    public function verDetalleUsuario(int $id, UsuarioRepository $usuarioRepository): JsonResponse
    {
        $usuario = $usuarioRepository->find($id);

        if (!$usuario) {
            return $this->json(['message' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $usuario->getId(),
            'nombre' => $usuario->getNombre(),
            'email' => $usuario->getEmail(),
            'fecha_nacimiento' => $usuario->getFechaNacimiento() ? $usuario->getFechaNacimiento()->format('Y-m-d') : null,
            'roles' => $usuario->getRoles(),
            'perfil' => $usuario->getPerfil() ? [
                'id' => $usuario->getPerfil()->getId(),
                'foto' => $usuario->getPerfil()->getFoto(),
                'descripcion' => $usuario->getPerfil()->getDescripcion(),
            ] : null,
            'playlistCreadas' => array_map(fn($p) => [
                'id' => $p->getId(),
                'nombre' => $p->getNombre(),
                'visibilidad' => $p->getVisibilidad(),
            ], $usuario->getPlaylistCreadas()->toArray()),
            'cancionesSubidas' => array_map(fn($c) => [
                'id' => $c->getId(),
                'titulo' => $c->getTitulo(),
                'autor' => $c->getAutor(),
            ], $usuario->getCancionesSubidas()->toArray()),
            'favoritosPlaylist' => array_map(fn($p) => [
                'id' => $p->getId(),
                'nombre' => $p->getNombre(),
            ], $usuario->getFavoritosPlaylist()->toArray()),
            'favoritosCancion' => array_map(fn($c) => [
                'id' => $c->getId(),
                'titulo' => $c->getTitulo(),
            ], $usuario->getFavoritosCancion()->toArray()),
        ]);
    }

    // Busca un usuario por su correo electrónico
    #[Route('/email/{email}', name: 'buscar_por_email', methods: ['GET'])]
    public function buscarUsuarioPorEmail(string $email, UsuarioRepository $usuarioRepository): JsonResponse
    {
        $usuario = $usuarioRepository->findByEmail($email);
        return $this->json($usuario);
    }

    // Obtiene los usuarios con más playlists creadas
    #[Route('/top-creadores/{limit}', name: 'top_creadores', methods: ['GET'])]
    public function topCreadores(int $limit, UsuarioRepository $usuarioRepository): JsonResponse
    {
        return $this->json($usuarioRepository->findTopCreators($limit));
    }

    // Obtiene los usuarios con más canciones subidas
    #[Route('/top-uploaders/{limit}', name: 'top_uploaders', methods: ['GET'])]
    public function topUploaders(int $limit, UsuarioRepository $usuarioRepository): JsonResponse
    {
        return $this->json($usuarioRepository->findTopUploaders($limit));
    }

    // Obtiene los usuarios con más reproducciones en canciones
    #[Route('/top-listeners/{limit}', name: 'top_listeners', methods: ['GET'])]
    public function topListeners(int $limit, UsuarioRepository $usuarioRepository): JsonResponse
    {
        return $this->json($usuarioRepository->findTopListeners($limit));
    }

    // Encuentra el usuario que subió una canción específica
    #[Route('/uploader/{cancionId}', name: 'uploader_por_cancion', methods: ['GET'])]
    public function findUploaderByCancion(int $cancionId, UsuarioRepository $usuarioRepository): JsonResponse
    {
        return $this->json($usuarioRepository->findUploaderByCancion($cancionId));
    }

    // Lista todas las canciones subidas por un usuario específico
    #[Route('/canciones/{usuarioId}', name: 'canciones_por_usuario', methods: ['GET'])]
    public function findCancionesByUsuario(int $usuarioId, UsuarioRepository $usuarioRepository): JsonResponse
    {
        return $this->json($usuarioRepository->findCancionesByUsuario($usuarioId));
    }

    // Muestra las playlists escuchadas por un usuario
    #[Route('/playlists-escuchadas/{usuarioId}', name: 'playlists_escuchadas', methods: ['GET'])]
    public function findPlaylistsEscuchadasByUsuario(int $usuarioId, UsuarioRepository $usuarioRepository): JsonResponse
    {
        return $this->json($usuarioRepository->findPlaylistsEscuchadasByUsuario($usuarioId));
    }

    #[Route('/test-log', name: 'test_log', methods: ['GET'])]
    public function testLog(LoggerActividadService $logger, Security $security): JsonResponse
    {
        $usuario = $security->getUser();
        
        if (!$usuario) {
            return $this->json(['message' => 'Debes iniciar sesión para probar el log'], Response::HTTP_UNAUTHORIZED);
        }

        $logger->log($usuario->getUserIdentifier(), "Probando el log en Symfony");

        return $this->json(['message' => 'Log registrado con éxito, revisa var/log/actividad_usuario.log']);
    }

}
