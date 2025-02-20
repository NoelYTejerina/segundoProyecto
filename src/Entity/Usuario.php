<?php

namespace App\Entity;

use App\Enum\RolUsuario;
use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // indica que es autoincremental, symfony lo hace automatico para campos con valor iD
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)] // para que no pueda haber 2 usuarios con el mismo email
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fechaNacimiento = null;

    #[ORM\Column(enumType: RolUsuario::class)]
    private RolUsuario $rol;

    #[ORM\OneToOne(mappedBy: 'usuario', cascade: ['persist', 'remove'])]
    private ?Perfil $perfil = null;

    /**
     * @var Collection<int, Playlist>
     */
    #[ORM\OneToMany(targetEntity: Playlist::class, mappedBy: 'propietario')]
    private Collection $playlistsCreadas;

    /**
     * @var Collection<int, Cancion>
     */
    #[ORM\OneToMany(targetEntity: Cancion::class, mappedBy: 'usuario')]
    private Collection $cancionesSubidas;

    /**
     * @var Collection<int, Playlist>
     */
    #[ORM\ManyToMany(targetEntity: Playlist::class, inversedBy: "usuariosQueEscuchan")]
    #[ORM\JoinTable(name: "usuario_playlist_escuchadas")] // le asigna nombre a la tabla intermedia
    private Collection $playlistsEscuchadas;

    /**
     * @var Collection<int, UsuarioCancion>
     */
    #[ORM\OneToMany(mappedBy: "usuario", targetEntity: UsuarioCancion::class)]
    private Collection $cancionesReproducidas;

    /**
     * @var Collection<int, UsuarioPlaylist>
     */
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

    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getFechaNacimiento(): ?\DateTimeInterface
    {
        return $this->fechaNacimiento;
    }

    public function setFechaNacimiento(\DateTimeInterface $fechaNacimiento)
    {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }

    public function getRol(): RolUsuario // al ser una clase devuelve un valor tipo RolUsuario
    {
        return $this->rol;
    }
    /* Ejemplo de uso de getRol()
            $usuario = new Usuario();
            $rol = $usuario->getRol(); // Esto devolverá el rol actual del usuario
            echo $rol->value; // Mostrará "usuario", "admin" o "manager" dependiendo del rol asignado    
    */

    public function setRol(RolUsuario $rol): self //: self: Indica que este método devuelve una instancia de la misma clase (Usuario). Esto se hace para permitir encadenamiento de métodos (method chaining).
    {
        $this->rol = $rol;
        return $this;
    }
    /* Ejemplo de uso de setRol()
            $usuario = new Usuario();
            $usuario->setRol(RolUsuario::MANAGER); // Asigna el rol "manager" al usuario

            echo $usuario->getRol()->value; // Mostrará "manager"    
    */

    public function getPerfil(): ?Perfil
    {
        return $this->perfil;
    }

    public function setPerfil(Perfil $perfil): static
    {
        // set the owning side of the relation if necessary
        if ($perfil->getUsuario() !== $this) {
            $perfil->setUsuario($this);
        }

        $this->perfil = $perfil;

        return $this;
    }

    /**
     * @return Collection<int, Playlist>
     */
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

    /**
     * @return Collection<int, Cancion>
     */
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

    /**
     * @return Collection<int, Playlist>
     */
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
            $usuarioPlaylist->setUsuario($this);
        }

        return $this;
    }

    public function removeUsuarioPlaylist(UsuarioPlaylist $usuarioPlaylist): static
    {
        if ($this->usuarioPlaylists->removeElement($usuarioPlaylist)) {
            // set the owning side to null (unless already changed)
            if ($usuarioPlaylist->getUsuario() === $this) {
                $usuarioPlaylist->setUsuario(null);
            }
        }

        return $this;
    }
}
