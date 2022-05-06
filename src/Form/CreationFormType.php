<?php

namespace App\Form;

use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CreationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add($builder->create('artistNames', ArtistNamesType::class)
                ->addModelTransformer(new CallbackTransformer(
                    function ($namesAsString) {
                        return $namesAsString;
                    },
                    function ($namesAsArray) { // ['key1' => 'value1', 'key2' => null] → ['value1']
                        if (null === $namesAsArray) {
                            return null;
                        }

                        return array_values(array_filter($namesAsArray));
                    }
                ))
            )
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
    }
}
