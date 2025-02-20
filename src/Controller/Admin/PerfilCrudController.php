<?php

namespace App\Controller\Admin;

use App\Entity\Perfil;
use App\Enum\Genero;
use App\Enum\Pais;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class PerfilCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Perfil::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            ImageField::new('foto', 'Foto de Perfil')->setUploadDir('public/perfiles/'),
            TextField::new('descripcion', 'Descripción'),
            TextField::new('apellidos', 'Apellidos'),
            TextField::new('ocupacion', 'Ocupación'),

            ChoiceField::new('genero', 'Género')
                ->setChoices(array_combine(
                    array_map(fn($e) => $e->value, Genero::cases()), // Las claves ahora son strings
                    Genero::cases()
                )),

            ChoiceField::new('pais', 'País')
                ->setChoices(array_combine(
                    array_map(fn($e) => $e->value, Pais::cases()), // Claves en string
                    Pais::cases() // Valores como objetos Enum
                )),

            AssociationField::new('usuario', 'Usuario Asociado')->setRequired(true),
        ];
    }
}
