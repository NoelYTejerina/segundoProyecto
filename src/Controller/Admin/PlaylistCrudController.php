<?php

namespace App\Controller\Admin;

use App\Entity\Playlist;
use App\Entity\PlaylistCancion;
use App\Enum\VisibilidadPlaylist;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class PlaylistCrudController extends AbstractCrudController
{
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

            IntegerField::new('likes', 'Número de Likes')->setFormTypeOption('disabled', true),
            AssociationField::new('propietario', 'Propietario'),

            // ✅ PERMITE AÑADIR Y VER CANCIONES USANDO EL BOTÓN + NUEVO
            CollectionField::new('playlistCanciones', 'Canciones en la Playlist')
                ->useEntryCrudForm(PlaylistCancionCrudController::class) // Muestra el botón de agregar
                ->setFormTypeOptions(['by_reference' => false])
                ->setRequired(false),
        ];
    }
}
