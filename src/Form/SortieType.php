<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', null,[
                'widget' => 'single_text'
            ])
            ->add('duree')
            ->add('dateLimiteInscription', null,[
                'widget' => 'single_text'
            ])
            ->add('nbInscriptionMax')
            ->add('infosSortie')
            ->add('etat', EntityType::class,[
                'class' => Etat::class,
                'choice_label' => 'libelle'
            ])
            ->add('Lieu', EntityType::class,[
                'class' => Lieu::class,
                'choice_label' => 'nom'
            ])
            ->add('Campus', EntityType::class,[
                'class' => Campus::class,
                'choice_label' => 'nom'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
