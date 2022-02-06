<?php

namespace App\Form;

use App\DTO\CreationForm;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('artistNames', Select2Type::class, [
                'required' => true,
                'attr' => ['placeholder' => 'artist_name1(*), artist_name2,...'],
                'multiple' => true,
                'choices' => [], //dummy
            ])
            ->add('playlistName', TextType::class, [
                'label' => 'Playlist name(*)',
                'data' => 'dj-kin29-'.(new DateTime())->format('YmdHi'),
            ])
            ->add('isPrivate', CheckboxType::class, [
                'label' => 'Private playlist',
                'required' => false,
                'attr' => [
                    'checked' => 'checked',
                ],
            ])
            ->add('createPlaylist', SubmitType::class, [])
        ;

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                /** @var CreationForm $data */
                $data = $event->getData();
                $event->getForm()->add('artistNames', Select2Type::class, [
                    'required' => true,
                    'attr' => ['placeholder' => 'artist_name1(*), artist_name2,...'],
                    'multiple' => true,
                    'choices' => $data['artistNames'],
                ]);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => CreationForm::class]
        );
    }
}
