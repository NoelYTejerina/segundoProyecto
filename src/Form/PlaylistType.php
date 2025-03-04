<?php
namespace App\Form;

use App\Enum\VisibilidadPlaylist;


use App\Entity\Playlist;
use App\Entity\Cancion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaylistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre de la playlist',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('visibilidad', ChoiceType::class, [
                'label' => 'Visibilidad',
                'choices' => [
                    'Pública' => VisibilidadPlaylist::PUBLICA, // Usar ENUM directamente
                    'Privada' => VisibilidadPlaylist::PRIVADA,
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('canciones', EntityType::class, [
                'label' => 'Canciones',
                'class' => Cancion::class,
                'choice_label' => 'titulo',
                'multiple' => true, // Permite seleccionar varias canciones
                'expanded' => true, // Muestra checkboxes en lugar de un select múltiple
                'attr' => ['class' => 'form-check-input'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Playlist::class,
        ]);
    }
}