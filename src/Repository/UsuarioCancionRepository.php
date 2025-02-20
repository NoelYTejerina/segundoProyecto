<?php

namespace App\Repository;

use App\Entity\UsuarioCancion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UsuarioCancion>
 */
class UsuarioCancionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsuarioCancion::class);
    }

    /**
     * Devuelve todas las canciones reproducidas por un usuario específico
     */
    public function findByUsuario(int $usuarioId): array
    {
        return $this->createQueryBuilder('uc')
            ->where('uc.usuario = :usuarioId')
            ->setParameter('usuarioId', $usuarioId)
            ->orderBy('uc.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Devuelve todos los usuarios que han reproducido una canción específica
     */
    public function findByCancion(int $cancionId): array
    {
        return $this->createQueryBuilder('uc')
            ->where('uc.cancion = :cancionId')
            ->setParameter('cancionId', $cancionId)
            ->orderBy('uc.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Devuelve las canciones más reproducidas por un usuario específico
     */
    public function findTopReproductionsByUsuario(int $usuarioId, int $limit): array
    {
        return $this->createQueryBuilder('uc')
            ->select('uc, SUM(uc.reproducciones) as totalReproducciones')
            ->where('uc.usuario = :usuarioId')
            ->setParameter('usuarioId', $usuarioId)
            ->groupBy('uc.cancion')
            ->orderBy('totalReproducciones', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
