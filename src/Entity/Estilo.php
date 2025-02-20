<?php

namespace App\Entity;

use App\Repository\EstiloRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EstiloRepository::class)]
class Estilo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private string $nombre;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $subgeneros = null;

    /**
     * @var Collection<int, Perfil>
     */
    #[ORM\ManyToMany(targetEntity: Perfil::class, mappedBy: 'estilosFavoritos')]
    private Collection $perfilesFavoritos;

    /**
     * @var Collection<int, Cancion>
     */
    #[ORM\OneToMany(mappedBy: "genero", targetEntity: Cancion::class)]
    private Collection $canciones;

    public function __construct()
    {
        $this->perfilesFavoritos = new ArrayCollection();
        $this->canciones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getSubgeneros(): ?string
    {
        return $this->subgeneros;
    }

    public function setSubgeneros(?string $subgeneros)
    {
        $this->subgeneros = $subgeneros;

        return $this;
    }

    /**
     * @return Collection<int, Perfil>
     */
    public function getPerfilesFavoritos(): Collection
    {
        return $this->perfilesFavoritos;
    }

    public function addPerfilFavorito(Perfil $perfilFavorito): static
    {
        if (!$this->perfilesFavoritos->contains($perfilFavorito)) {
            $this->perfilesFavoritos->add($perfilFavorito);
            $perfilFavorito->addEstiloFavorito($this);
        }

        return $this;
    }

    public function removePerfilFavorito(Perfil $perfilFavorito): static
    {
        if ($this->perfilesFavoritos->removeElement($perfilFavorito)) {
            $perfilFavorito->removeEstiloFavorito($this);
        }

        return $this;
    }
    /* 
    📌 ¿Cómo funciona esta sincronización?
    ✅ Cuando eliminas un estilo favorito en Perfil, el método removeEstiloFavorito()
      llama a removePerfilFavorito() en Estilo.
    ✅ Cuando eliminas un perfil en Estilo, el método removePerfilFavorito() llama a 
      removeEstiloFavorito() en Perfil.
    ✅ Doctrine mantiene esta coherencia y elimina automáticamente la relación en la 
      base de dato    
    */

    /**
     * @return Collection<int, Cancion>
     */
    public function getCanciones(): Collection
    {
        return $this->canciones;
    }
    
    public function addCancion(Cancion $cancion): static
    {
        if (!$this->canciones->contains($cancion)) {
            $this->canciones->add($cancion);
            $cancion->setGenero($this);
        }
    
        return $this;
    }
    
    public function removeCancion(Cancion $cancion): static
    {
        if ($this->canciones->removeElement($cancion)) {
            if ($cancion->getGenero() === $this) {
                $cancion->setGenero(null);
            }// esto hace que si se borra un estilo , el campo genero de cancion pase a Null
        }
    
        return $this;
    }

    public function __toString(): string
    {
        return $this->nombre ?? 'Sin nombre';  
    }

}
