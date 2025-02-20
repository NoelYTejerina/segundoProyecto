<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Repository\UsuarioCancionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioCancionRepository::class)]
class UsuarioCancion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: "cancionesReproducidas")]
    #[ORM\JoinColumn(nullable: false)]
    private Usuario $usuario;

    #[ORM\ManyToOne(targetEntity: Cancion::class, inversedBy: "usuariosQueReproducen")]
    #[ORM\JoinColumn(nullable: false)]
    private Cancion $cancion;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $reproducciones = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getCancion(): Cancion
    {
        return $this->cancion;
    }

    public function setCancion(Cancion $cancion): static
    {
        $this->cancion = $cancion;

        return $this;
    }

    public function getReproducciones(): int
    {
        return $this->reproducciones;
    }

    public function setReproducciones(int $reproducciones): static
    {
        $this->reproducciones = $reproducciones;
        return $this;
    }

    public function incrementarReproducciones(): static
    {
        $this->reproducciones++;
        return $this;
    }


    public function __toString(): string
    {
        return $this->nombre ?? 'Sin nombre';
    }
}
