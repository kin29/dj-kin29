<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ArtistNamesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('artistName1', null, [
                'required' => true,
                'label' => false,
                'attr' => ['placeholder' => 'artist_name1(*)'],
            ])
            ->add('artistName2', null, [
                'required' => false,
                'label' => false,
                'attr' => ['placeholder' => 'artist_name2'],
            ])
            ->add('artistName3', null, [
                'required' => false,
                'label' => false,
                'attr' => ['placeholder' => 'artist_name3'],
            ])
            ->add('artistName4', null, [
                'required' => false,
                'label' => false,
                'attr' => ['placeholder' => 'artist_name4'],
            ])
            ->add('artistName5', null, [
                'required' => false,
                'label' => false,
                'attr' => ['placeholder' => 'artist_name5'],
            ])
        ;
    }
}
