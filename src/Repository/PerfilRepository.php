<?php

namespace App\Repository;

use App\Entity\Perfil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Perfil>
 */
class PerfilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Perfil::class);
    }

    /**
     * Encuentra un perfil por el ID del usuario
     */
    public function findByUsuario(int $usuarioId): ?Perfil
    {
        return $this->createQueryBuilder('p')
            ->where('p.usuario = :usuarioId')
            ->setParameter('usuarioId', $usuarioId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Devuelve los perfiles que tienen un género específico
     */
    public function findByGenero(string $genero): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.genero = :genero')
            ->setParameter('genero', $genero)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Devuelve los perfiles de usuarios que tienen un estilo musical favorito específico
     */
    public function findByEstiloFavorito(int $estiloId): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.estilosFavoritos', 'e')
            ->where('e.id = :estiloId')
            ->setParameter('estiloId', $estiloId)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
    /**
     * Devuelve los usuarios que pertenecen a un país específico
     */
    public function findUsuariosByPais(string $pais): array
    {
        return $this->createQueryBuilder('p')
            ->select('u') // Seleccionamos la entidad Usuario asociada
            ->innerJoin('p.usuario', 'u') // Hacemos la relación entre Perfil y Usuario
            ->where('p.pais = :pais')
            ->setParameter('pais', $pais)
            ->orderBy('u.nombre', 'ASC') // Ordenamos por nombre de usuario
            ->getQuery()
            ->getResult();
    }
}
