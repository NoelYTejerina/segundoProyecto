<?php
namespace App\Controller\Admin;

use App\Entity\Usuario;
use App\Entity\Perfil;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsuarioCrudController extends AbstractCrudController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

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

            ChoiceField::new('roles', 'Roles de Usuario')
                ->setChoices([
                    'Admin' => 'ROLE_ADMIN',
                    'Manager' => 'ROLE_MANAGER',
                    'User' => 'ROLE_USER',
                ])
                ->allowMultipleChoices()
                ->renderExpanded(), // Muestra los roles como checkboxes

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

        // Hashear la contraseña antes de persistir el usuario
        $hashedPassword = $this->passwordHasher->hashPassword(
            $entityInstance,
            $entityInstance->getPassword()
        );
        $entityInstance->setPassword($hashedPassword);

        // Si el usuario no tiene perfil, se crea uno vacío sin obligar a campos no nulos
        if ($entityInstance->getPerfil() === null) {
            $perfil = new Perfil();
            $perfil->setUsuario($entityInstance);
            $entityManager->persist($perfil);
            $entityInstance->setPerfil($perfil);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Usuario) {
            parent::updateEntity($entityManager, $entityInstance);
            return;
        }

        // Hashear la contraseña antes de actualizar el usuario
        if ($entityInstance->getPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $entityInstance,
                $entityInstance->getPassword()
            );
            $entityInstance->setPassword($hashedPassword);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}