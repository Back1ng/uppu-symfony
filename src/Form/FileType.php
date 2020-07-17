<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType as File;
use Symfony\Component\Form\FormBuilderInterface;

class FileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("file", File::class, [
                'attr' => [
                    'class' => 'form-control-file m-1',
                ],
                'label' => false,
            ])
            ->add("submit", SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary m-1']
            ]);
    }
}
