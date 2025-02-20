<?php

namespace App\Entity;

use App\Enum\VisibilidadPlaylist;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaylistRepository::class)]
class Playlist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private string $nombre;

    #[ORM\Column(enumType: VisibilidadPlaylist::class)]
    private VisibilidadPlaylist $visibilidad;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $likes = 0;

    /**
     * @var Collection<int, PlaylistCancion>
     */
    #[ORM\OneToMany(targetEntity: PlaylistCancion::class, mappedBy: 'playlist', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $playlistCanciones;
    

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: "playlistsCreadas")]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?Usuario $propietario = null;
    // si el usuario se elimina sin borrar la playlist pasa a propiedad de la app

    /**
     * @var Collection<int, Usuario>
     */
    #[ORM\ManyToMany(targetEntity: Usuario::class, mappedBy: "playlistsEscuchadas")]
    private Collection $usuariosQueEscuchan;

    /**
     * @var Collection<int, UsuarioPlaylist>
     */
    #[ORM\OneToMany(targetEntity: UsuarioPlaylist::class, mappedBy: 'playlist', orphanRemoval: true)]
    private Collection $usuarioPlaylists;


    public function __construct()
    {
        $this->playlistCanciones = new ArrayCollection();
        $this->usuariosQueEscuchan = new ArrayCollection();
        $this->usuarioPlaylists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getVisibilidad(): VisibilidadPlaylist
    {
        return $this->visibilidad;
    }

    public function setVisibilidad(VisibilidadPlaylist $visibilidad): static
    {
        $this->visibilidad = $visibilidad;
        return $this;
    }

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): static
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * @return Collection<int, PlaylistCancion>
     */
    public function getPlaylistCanciones(): Collection
    {
        return $this->playlistCanciones;
    }

    public function addPlaylistCancion(PlaylistCancion $playlistCancion): static
    {
        if (!$this->playlistCanciones->contains($playlistCancion)) {
            $this->playlistCanciones->add($playlistCancion);
            $playlistCancion->setPlaylist($this);
        }

        return $this;
    }

    public function removePlaylistCancion(PlaylistCancion $playlistCancion): static
    {
        $this->playlistCanciones->removeElement($playlistCancion);
        return $this;
    }

    public function getPropietario(): ?Usuario
    {
        return $this->propietario;
    }

    public function setPropietario(?Usuario $propietario): static
    {
        $this->propietario = $propietario;

        return $this;
    }

    /**
     * @return Collection<int, Usuario>
     */
    public function getUsuariosQueEscuchan(): Collection
    {
        return $this->usuariosQueEscuchan;
    }

    public function addUsuarioQueEscucha(Usuario $usuario): static
    {
        if (!$this->usuariosQueEscuchan->contains($usuario)) {
            $this->usuariosQueEscuchan->add($usuario);
            $usuario->addPlaylistEscuchada($this);
        }

        return $this;
    }

    public function removeUsuarioQueEscucha(Usuario $usuario): static
    {
        if ($this->usuariosQueEscuchan->removeElement($usuario)) {
            $usuario->removePlaylistEscuchada($this);
        }

        return $this;
    }


    public function __toString(): string
    {
        return $this->nombre ?? 'Sin nombre';
    }

    /**
     * @return Collection<int, UsuarioPlaylist>
     */
    public function getUsuarioPlaylists(): Collection
    {
        return $this->usuarioPlaylists;
    }

    public function addUsuarioPlaylist(UsuarioPlaylist $usuarioPlaylist): static
    {
        if (!$this->usuarioPlaylists->contains($usuarioPlaylist)) {
            $this->usuarioPlaylists->add($usuarioPlaylist);
            $usuarioPlaylist->setPlaylist($this);
        }

        return $this;
    }

    public function removeUsuarioPlaylist(UsuarioPlaylist $usuarioPlaylist): static
    {
        if ($this->usuarioPlaylists->removeElement($usuarioPlaylist)) {
            // set the owning side to null (unless already changed)
            if ($usuarioPlaylist->getPlaylist() === $this) {
                $usuarioPlaylist->setPlaylist(null);
            }
        }

        return $this;
    }
}
