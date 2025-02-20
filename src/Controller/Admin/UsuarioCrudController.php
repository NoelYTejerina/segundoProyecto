<?php

namespace App\Controller\Admin;

use App\Entity\Usuario;
use App\Entity\Perfil;
use App\Enum\RolUsuario;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class UsuarioCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Usuario::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nombre', 'Nombre'),
            EmailField::new('email', 'Correo Electrónico'),
            TextField::new('password', 'Contraseña')->onlyOnForms(),
            DateField::new('fechaNacimiento', 'Fecha de Nacimiento'),

            ChoiceField::new('rol', 'Rol de Usuario')
                ->setChoices(array_combine(
                    array_map(fn($e) => $e->value, RolUsuario::cases()), 
                    RolUsuario::cases()
                ))
                ->setRequired(true),

            // Asociación del perfil (pero sin obligar a rellenarlo)
            AssociationField::new('perfil', 'Perfil')->hideOnForm(),

            // Relación con las Playlists creadas (solo para visualizar)
            CollectionField::new('playlistsCreadas', 'Playlists Creadas')->onlyOnDetail(),

            // Relación con las Canciones subidas (solo para visualizar)
            CollectionField::new('cancionesSubidas', 'Canciones Subidas')->onlyOnDetail(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Usuario) {
            parent::persistEntity($entityManager, $entityInstance);
            return;
        }

        // Si el usuario no tiene perfil, se crea uno vacío sin obligar a campos no nulos
        if ($entityInstance->getPerfil() === null) {
            $perfil = new Perfil();
            $perfil->setUsuario($entityInstance);
            $entityManager->persist($perfil);
            $entityInstance->setPerfil($perfil);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }
}
