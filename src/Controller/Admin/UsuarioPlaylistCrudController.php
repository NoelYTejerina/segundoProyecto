<?php

namespace App\Controller\Admin;

use App\Entity\UsuarioPlaylist;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class UsuarioPlaylistCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UsuarioPlaylist::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('usuario', 'Usuario')->setRequired(true),
            AssociationField::new('playlist', 'Playlist Escuchada')->setRequired(true),
        ];
    }
}
