<?php

namespace App\Entity;

use App\Repository\CancionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CancionRepository::class)]
class Cancion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $titulo;

    #[ORM\Column(type: Types::INTEGER)]
    private int $duracion;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $album = null;

    #[ORM\Column(length: 255)]
    private string $autor;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $likes = 0;

    /*
        options: ['default' => 0]
        Establece que el valor por defecto en la base de datos será 0
        si no se proporciona otro valor al crear un registro.

        private int $likes = 0;
        Inicializa la variable $likes con 0 para asegurarse de que siempre
        tenga un valor predeterminado.
    */

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $fechaCreacion;

    /*
        type: Types::DATETIME_IMMUTABLE:
        Define el campo como un datetime en la base de datos.
        Se usa \DateTimeImmutable en PHP, lo que significa que el objeto
        no se puede modificar después de crearse.

        options: ['default' => 'CURRENT_TIMESTAMP']:
        Indica que, si no se especifica un valor al crear un nuevo registro,
        la base de datos asignará automáticamente la fecha y hora actual.
    */

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $anio = null;

    #[ORM\ManyToOne(targetEntity: Estilo::class, inversedBy: "canciones")]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?Estilo $genero = null;
    //(nullable: true, onDelete: "SET NULL") porque queremos que, si se borra el estilo, genero quede en NULL

    /**
     * @var Collection<int, PlaylistCancion>
     */
    #[ORM\OneToMany(targetEntity: PlaylistCancion::class, mappedBy: 'cancion', orphanRemoval: true)]
    private Collection $playlistCanciones;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: "cancionesSubidas")]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?Usuario $usuario = null;

    /**
     * @var Collection<int, UsuarioCancion>
     */
    #[ORM\OneToMany(targetEntity: UsuarioCancion::class, mappedBy: 'cancion')]
    private Collection $usuariosQueReproducen;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $albumImagen = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $archivo = null;



    public function __construct()
    {
        $this->playlistCanciones = new ArrayCollection();
        $this->usuariosQueReproducen = new ArrayCollection();
        $this->fechaCreacion = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo)
    {
        $this->titulo = $titulo;
        return $this;
    }

    public function getDuracion(): int
    {
        return $this->duracion;
    }

    public function setDuracion(int $duracion)
    {
        $this->duracion = $duracion;
        return $this;
    }

    public function getAlbum(): ?string
    {
        return $this->album;
    }

    public function setAlbum(?string $album)
    {
        $this->album = $album;
        return $this;
    }

    public function getAutor(): string
    {
        return $this->autor;
    }

    public function setAutor(string $autor)
    {
        $this->autor = $autor;
        return $this;
    }

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function setLikes(int $likes)
    {
        $this->likes = $likes;
        return $this;
    }

    public function getFechaCreacion(): \DateTimeImmutable
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(\DateTimeImmutable $fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;
        return $this;
    }

    public function getAnio(): ?int
    {
        return $this->anio;
    }

    public function setAnio(?int $anio)
    {
        $this->anio = $anio;
        return $this;
    }

    public function getGenero(): ?Estilo
    {
        return $this->genero;
    }

    public function setGenero(?Estilo $genero): static
    {
        $this->genero = $genero;

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
            $playlistCancion->setCancion($this);
        }

        return $this;
    }

    public function removePlaylistCancion(PlaylistCancion $playlistCancion): static
    {
        $this->playlistCanciones->removeElement($playlistCancion);
        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;
        return $this;
    }

    /**
     * @return Collection<int, UsuarioCancion>
     */
    public function getUsuariosQueReproducen(): Collection
    {
        return $this->usuariosQueReproducen;
    }

    public function addUsuarioQueReproduce(UsuarioCancion $usuarioCancion): static
    {
        if (!$this->usuariosQueReproducen->contains($usuarioCancion)) {
            $this->usuariosQueReproducen->add($usuarioCancion);
            $usuarioCancion->setCancion($this);
        }

        return $this;
    }

    public function removeUsuarioQueReproduce(UsuarioCancion $usuarioCancion): static
    {
        $this->usuariosQueReproducen->removeElement($usuarioCancion);
        return $this;
    }

    // la idea es mantener el historial de reproducciones para estadisticas
    public function getAlbumImagen(): ?string
    {
        return $this->albumImagen;
    }
    
    public function setAlbumImagen(?string $albumImagen): static
    {
        $this->albumImagen = $albumImagen;
        return $this;
    }
    
    public function getArchivo(): ?string
    {
        return $this->archivo;
    }
    
    public function setArchivo(?string $archivo): static
    {
        $this->archivo = $archivo;
        return $this;
    }
    

    public function __toString(): string
    {
        return $this->titulo ?? 'Sin nombre';
    }
}
