<?php

namespace App\Entity;

use App\Enum\Genero;
use App\Enum\Pais;
use App\Repository\PerfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PerfilRepository::class)]
class Perfil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foto = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $apellidos = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ocupacion = null;

    #[ORM\Column(type: 'string', enumType: Genero::class, nullable: true)]
    private ?Genero $genero = null;

    #[ORM\Column(type: 'string', enumType: Pais::class, nullable: true)]
    private ?Pais $pais = null;

    #[ORM\OneToOne(inversedBy: 'perfil', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    /**
     * @var Collection<int, Estilo>
     */
    #[ORM\ManyToMany(targetEntity: Estilo::class, inversedBy: 'perfilesFavoritos')]
    private Collection $estilosFavoritos;
    // Aquí targetEntity es necesario porque al ser colecciones de múltiples entidades,
    // se requiere especificar explícitamente la relación.

    public function __construct()
    {
        $this->estilosFavoritos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto)
    {
        $this->foto = $foto;

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

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(?string $apellidos)
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getOcupacion(): ?string
    {
        return $this->ocupacion;
    }

    public function setOcupacion(?string $ocupacion)
    {
        $this->ocupacion = $ocupacion;

        return $this;
    }

    public function getGenero(): ?Genero
    {
        return $this->genero;
    }

    public function setGenero(?Genero $genero): self
    {
        $this->genero = $genero;
        return $this;
    }

    public function getPais(): ?Pais
    {
        return $this->pais;
    }

    public function setPais(?Pais $pais): self
    {
        $this->pais = $pais;
        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * @return Collection<int, Estilo>
     */
    public function getEstilosFavoritos(): Collection
    {
        return $this->estilosFavoritos;
    }

    public function addEstiloFavorito(Estilo $estiloFavorito): static
    {
        if (!$this->estilosFavoritos->contains($estiloFavorito)) {
            $this->estilosFavoritos->add($estiloFavorito);
        }

        return $this;
    }

    public function removeEstiloFavorito(Estilo $estiloFavorito): static
    {
        $this->estilosFavoritos->removeElement($estiloFavorito);

        return $this;
    }

    public function __toString(): string
    {
        return $this->usuario ? $this->usuario->getNombre() : 'Sin perfil';
    }
}
