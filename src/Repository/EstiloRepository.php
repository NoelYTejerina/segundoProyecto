<?php

namespace App\Repository;


use App\Entity\Estilo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Estilo>
 */
class EstiloRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Estilo::class);
    }

    /**
     * Encuentra un estilo por su nombre
     */
    public function findByNombre(string $nombre): ?Estilo
    {
        return $this->createQueryBuilder('e')
            ->where('e.nombre = :nombre')
            ->setParameter('nombre', $nombre)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Devuelve los estilos musicales más populares según la cantidad de perfiles que los tienen como favoritos
     */
    public function findMostPopular(int $limit): array
    {
        return $this->createQueryBuilder('e')
            ->select('e, COUNT(p.id) as numFavoritos')
            ->leftJoin('e.perfilesFavoritos', 'p')
            ->groupBy('e.id')
            ->orderBy('numFavoritos', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function obtenerReproduccionesPorEstilo(): array
    {
        return $this->createQueryBuilder('e')
            ->select('e.nombre AS estilo, SUM(c.reproducciones) AS totalReproducciones')
            ->join('e.canciones', 'c')
            ->groupBy('e.id')
            ->orderBy('totalReproducciones', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
