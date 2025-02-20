<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Repository\PlaylistCancionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaylistCancionRepository::class)]
class PlaylistCancion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $reproducciones = 0;

    #[ORM\ManyToOne(targetEntity: Playlist::class, inversedBy: "playlistCanciones")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private Playlist $playlist;

    #[ORM\ManyToOne(targetEntity: Cancion::class, inversedBy: "playlistCanciones")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private Cancion $cancion;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPlaylist(): Playlist
    {
        return $this->playlist;
    }

    public function setPlaylist(Playlist $playlist): static
    {
        $this->playlist = $playlist;
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

    public function __toString(): string
    {
        return $this->cancion ? $this->cancion->getTitulo() : 'Sin título';
    }
    
}
