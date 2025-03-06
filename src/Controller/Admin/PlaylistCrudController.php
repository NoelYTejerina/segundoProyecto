<?php

namespace App\Controller\Admin;

use App\Entity\Playlist;
use App\Entity\PlaylistCancion;
use App\Enum\VisibilidadPlaylist;
use App\Service\LoggerActividadService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class PlaylistCrudController extends AbstractCrudController
{
    private LoggerActividadService $logger;

    public function __construct(LoggerActividadService $logger)
    {
        $this->logger = $logger;
    }

    public static function getEntityFqcn(): string
    {
        return Playlist::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nombre', 'Nombre de la Playlist'),

            // Manejo correcto del Enum `VisibilidadPlaylist`
            ChoiceField::new('visibilidad', 'Visibilidad')->setChoices(array_combine(
                array_map(fn($e) => $e->value, VisibilidadPlaylist::cases()), 
                VisibilidadPlaylist::cases()
            )),

            IntegerField::new('likes', 'Número de Likes'),
            AssociationField::new('propietario', 'Propietario'),

            // ✅ PERMITE AÑADIR Y VER CANCIONES USANDO EL BOTÓN + NUEVO
            CollectionField::new('playlistCanciones', 'Canciones en la Playlist')
                ->useEntryCrudForm(PlaylistCancionCrudController::class) // Muestra el botón de agregar
                ->setFormTypeOptions(['by_reference' => false])
                ->setRequired(false),
        ];
    }

    /**
     * Registra la creación de una Playlist en EasyAdmin.
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Playlist) {
            parent::persistEntity($entityManager, $entityInstance);
            return;
        }

        parent::persistEntity($entityManager, $entityInstance);

        // 🔹 Registrar en el log la creación de la Playlist
        $usuario = $entityInstance->getPropietario();
        $nombrePlaylist = $entityInstance->getNombre();
        $this->logger->log($usuario ? $usuario->getUserIdentifier() : 'Administrador', "Creó la playlist desde EasyAdmin: $nombrePlaylist");
    }

    /**
     * Registra la edición de una Playlist en EasyAdmin.
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Playlist) {
            parent::updateEntity($entityManager, $entityInstance);
            return;
        }

        parent::updateEntity($entityManager, $entityInstance);

        // 🔹 Registrar en el log la edición de la Playlist
        $usuario = $entityInstance->getPropietario();
        $nombrePlaylist = $entityInstance->getNombre();
        $this->logger->log($usuario ? $usuario->getUserIdentifier() : 'Administrador', "Editó la playlist desde EasyAdmin: $nombrePlaylist");
    }

    /**
     * Registra la eliminación de una Playlist en EasyAdmin.
     */
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Playlist) {
            return;
        }

        // 🔹 Registrar en el log la eliminación de la Playlist
        $usuario = $entityInstance->getPropietario();
        $nombrePlaylist = $entityInstance->getNombre();
        $this->logger->log($usuario ? $usuario->getUserIdentifier() : 'Administrador', "Eliminó la playlist desde EasyAdmin: $nombrePlaylist");

        $entityManager->remove($entityInstance);
        $entityManager->flush();
    }
}
