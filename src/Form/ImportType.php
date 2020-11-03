<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('csv', FileType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Merci de transmettre un fichier (format CSV)'
                    ]),
                    new Assert\File([
                        'mimeTypes' => [
                            'text/plain',
                            'text/CSV'
                        ],
                        'mimeTypesMessage' => 'Merci de transmettre un fichier au format CSV',
                    ])
                ]
            ])
        ;
    }
}