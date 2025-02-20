<?php

namespace App\Repository;

use App\Entity\Cancion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cancion>
 */
class CancionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cancion::class);
    }

    /**
     * Devuelve las canciones más reproducidas (top N)
     */
    public function findTopReproducciones(int $limit): array
    {
        return $this->createQueryBuilder('c')
            ->select('c, SUM(uc.reproducciones) as totalReproducciones')
            ->leftJoin('c.usuarioCancion', 'uc')
            ->groupBy('c.id')
            ->orderBy('totalReproducciones', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Devuelve las canciones de un estilo específico
     */
    public function findByEstilo(int $estiloId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.genero = :estiloId')
            ->setParameter('estiloId', $estiloId)
            ->orderBy('c.titulo', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Devuelve las últimas canciones agregadas al sistema
     */
    public function findRecentCanciones(int $limit): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.fechaCreacion', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Busca canciones por título (búsqueda parcial)
     */
    public function findByTitulo(string $titulo): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.titulo LIKE :titulo')
            ->setParameter('titulo', '%' . $titulo . '%')
            ->orderBy('c.titulo', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Busca canciones por autor
     */
    public function findByAutor(string $autor): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.autor LIKE :autor')
            ->setParameter('autor', '%' . $autor . '%')
            ->orderBy('c.autor', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Devuelve las canciones con más "likes"
     */
    public function findMostLiked(int $limit): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.likes', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Devuelve todas las canciones de un álbum específico
     */
    public function findByAlbum(string $album): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.album LIKE :album')
            ->setParameter('album', '%' . $album . '%')
            ->orderBy('c.titulo', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
