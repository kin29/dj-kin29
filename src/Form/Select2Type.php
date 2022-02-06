<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Select2Type extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attr = $view->vars['attr'];
        $class = isset($attr['class']) ? $attr['class'].' ' : '';
        $class .= 'select2';
        $attr['class'] = $class;
        $view->vars['attr'] = $attr;
    }

    public function getParent() {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'attr' => [
                'class' => 'select2',
            ]
        ]);
    }
}
