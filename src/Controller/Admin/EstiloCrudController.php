<?php

namespace App\Controller\Admin;

use App\Entity\Estilo;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class EstiloCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Estilo::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nombre', 'Nombre del Estilo'),
            TextareaField::new('descripcion', 'Descripción'),
            TextareaField::new('subgeneros', 'Subgéneros'),
        ];
    }
}
