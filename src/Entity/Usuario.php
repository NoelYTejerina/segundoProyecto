<?php

namespace App\Entity;

use App\Enum\RolUsuario;
use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $roles = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fechaNacimiento = null;

 

    #[ORM\OneToOne(mappedBy: 'usuario', cascade: ['persist', 'remove'])]
    private ?Perfil $perfil = null;

    #[ORM\OneToMany(targetEntity: Playlist::class, mappedBy: 'propietario')]
    private Collection $playlistsCreadas;

    #[ORM\OneToMany(targetEntity: Cancion::class, mappedBy: 'usuario')]
    private Collection $cancionesSubidas;

    #[ORM\ManyToMany(targetEntity: Playlist::class, inversedBy: "usuariosQueEscuchan")]
    #[ORM\JoinTable(name: "usuario_playlist_escuchadas")]
    private Collection $playlistsEscuchadas;

    #[ORM\OneToMany(mappedBy: "usuario", targetEntity: UsuarioCancion::class)]
    private Collection $cancionesReproducidas;

    #[ORM\OneToMany(targetEntity: UsuarioPlaylist::class, mappedBy: 'usuario', orphanRemoval: true)]
    private Collection $usuarioPlaylists;

    public function __construct()
    {
        $this->playlistsCreadas = new ArrayCollection();
        $this->cancionesSubidas = new ArrayCollection();
        $this->playlistsEscuchadas = new ArrayCollection();
        $this->cancionesReproducidas = new ArrayCollection();
        $this->usuarioPlaylists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
{
   
    return $this->roles;
}

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Si almacenas datos sensibles temporales en el usuario, límpialos aquí
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

    public function getFechaNacimiento(): ?\DateTimeInterface
    {
        return $this->fechaNacimiento;
    }

    public function setFechaNacimiento(\DateTimeInterface $fechaNacimiento): static
    {
        $this->fechaNacimiento = $fechaNacimiento;
        return $this;    }

    

    public function getPerfil(): ?Perfil
    {
        return $this->perfil;
    }

    public function setPerfil(Perfil $perfil): static
    {
        if ($perfil->getUsuario() !== $this) {
            $perfil->setUsuario($this);
        }
        $this->perfil = $perfil;
        return $this;
    }

    public function getPlaylistsCreadas(): Collection
    {
        return $this->playlistsCreadas;
    }

    public function addPlaylistCreada(Playlist $playlist): static
    {
        if (!$this->playlistsCreadas->contains($playlist)) {
            $this->playlistsCreadas->add($playlist);
            $playlist->setPropietario($this);
        }
        return $this;
    }

    public function removePlaylistCreada(Playlist $playlist): static
    {
        if ($this->playlistsCreadas->removeElement($playlist)) {
            if ($playlist->getPropietario() === $this) {
                $playlist->setPropietario(null);
            }
        }
        return $this;
    }

    public function getCancionesSubidas(): Collection
    {
        return $this->cancionesSubidas;
    }

    public function addCancionSubida(Cancion $cancion): static
    {
        if (!$this->cancionesSubidas->contains($cancion)) {
            $this->cancionesSubidas->add($cancion);
            $cancion->setUsuario($this);
        }
        return $this;
    }

    public function removeCancionSubida(Cancion $cancion): static
    {
        if ($this->cancionesSubidas->removeElement($cancion)) {
            if ($cancion->getUsuario() === $this) {
                $cancion->setUsuario(null);
            }
        }
        return $this;
    }

    public function getPlaylistsEscuchadas(): Collection
    {
        return $this->playlistsEscuchadas;
    }

    public function addPlaylistEscuchada(Playlist $playlist): static
    {
        if (!$this->playlistsEscuchadas->contains($playlist)) {
            $this->playlistsEscuchadas->add($playlist);
            $playlist->addUsuarioQueEscucha($this);
        }
        return $this;
    }

    public function removePlaylistEscuchada(Playlist $playlist): static
    {
        if ($this->playlistsEscuchadas->removeElement($playlist)) {
            $playlist->removeUsuarioQueEscucha($this);
        }
        return $this;
    }

    public function getCancionesReproducidas(): Collection
    {
        return $this->cancionesReproducidas;
    }

    public function addCancionReproducida(UsuarioCancion $usuarioCancion): static
    {
        if (!$this->cancionesReproducidas->contains($usuarioCancion)) {
            $this->cancionesReproducidas->add($usuarioCancion);
            $usuarioCancion->setUsuario($this);
        }
        return $this;
    }

    public function removeCancionReproducida(UsuarioCancion $usuarioCancion): static
    {
        $this->cancionesReproducidas->removeElement($usuarioCancion);
        return $this;
    }

    public function __toString(): string
    {
        return $this->nombre ?? 'Sin nombre';
    }

    public function getUsuarioPlaylists(): Collection
    {
        return $this->usuarioPlaylists;
    }

    public function addUsuarioPlaylist(UsuarioPlaylist $usuarioPlaylist): static
    {
        if (!$this->usuarioPlaylists->contains($usuarioPlaylist)) {
            $this->usuarioPlaylists->add($usuarioPlaylist);
            $usuarioPlaylist->setUsuario($this);
        }
        return $this;
    }

    public function removeUsuarioPlaylist(UsuarioPlaylist $usuarioPlaylist): static
    {
        if ($this->usuarioPlaylists->removeElement($usuarioPlaylist)) {
            if ($usuarioPlaylist->getUsuario() === $this) {
                $usuarioPlaylist->setUsuario(null);
            }
        }
        return $this;
    }
}