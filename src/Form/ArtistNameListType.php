<?php


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ArtistNameListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('artistName1', TextType::class, $this->generateArtistNames('artist_name1', true))
            ->add('artistName2', TextType::class, $this->generateArtistNames('artist_name2'))
            ->add('artistName3', TextType::class, $this->generateArtistNames('artist_name3'))
            ->add('artistName4', TextType::class, $this->generateArtistNames('artist_name4'))
            ->add('artistName5', TextType::class, $this->generateArtistNames('artist_name5'))
            ->add('playlistName', TextType::class, [
                'attr' => [
                    'placeholder' => 'playlist name',
                ],
                'empty_data' => 'default name'
            ])
            ->add('public', CheckboxType::class, [
                'label' => 'public',
                'required' => true,
                'attr' => [
                    'checked' => 'checked',
                ],
            ])
            ->add('allowCookie', CheckboxType::class, [
                'label' => 'allow use cookie',
                'required' => true,
                'attr' => [
                    'checked' => 'checked',
                ],
            ])
            ->add('createPlaylist', SubmitType::class, [])
        ;
    }

    /**
     * @param string $placeHolder
     * @param bool $isRequired
     * @return array
     */
    public function generateArtistNames(string $placeHolder, bool $isRequired = false): array
    {
        return [
            'attr' => [
                'placeholder' => $placeHolder,
            ],
            'label' => false,
            'required' => $isRequired
        ];
    }
}
