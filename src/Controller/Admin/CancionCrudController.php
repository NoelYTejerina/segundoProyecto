<?php

namespace App\Controller\Admin;

use App\Entity\Cancion;
use App\Entity\Estilo;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;


class CancionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Cancion::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('titulo', 'Título'),
            TextField::new('autor', 'Autor'),
            IntegerField::new('duracion', 'Duración (segundos)'),
            TextField::new('album', 'Álbum'),
            
            IntegerField::new('anio', 'Año de Publicación'),
            ImageField::new('albumImagen', 'Imagen del Álbum')->setUploadDir('public/imagenes/'),
            TextField::new('archivo', 'Archivo de Audio')->setFormTypeOption('by_reference', false),

            AssociationField::new('genero', 'Estilo')
            ->setFormTypeOptions([
                'choice_label' => 'nombre',
                'by_reference' => false,
            ]),
        
        ];
    }
}
