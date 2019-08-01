<?php

namespace App\Form;

use App\Entity\MareeRestriction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MareeRestrictionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hauteurMax', IntegerType::class)
            ->add('hauteurMin', IntegerType::class)
            ->add('state',ChoiceType::class, array(
                'choices' => array('top'=>'top', 'OK'=>'OK', 'warn'=>'warn', 'KO'=>'KO')
            ))
            //->add('spot')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MareeRestriction::class,
        ]);
    }
}
