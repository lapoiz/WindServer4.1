<?php

namespace App\Form;

use App\Entity\Spot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,  [
                'label' => 'Nom du spot'
            ])
            ->add('description', TextareaType::class, array('attr' => array('class' => 'ckeditor')))
            ->add('desc_route')
            ->add('desc_maree')
            ->add('time_from_paris')
            ->add('km_from_paris')
            ->add('km_autoroute_from_paris')
            ->add('price_autoroute_from_paris')
            ->add('gps_lat')
            ->add('gps_long')

            ->add('windOrientation', CollectionType::class, [
                'entry_type' => WindOrientationType::class,
                'attr' =>['style' => 'display:none'],
                'label' =>false,

            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Spot::class,
            'translation_domain' => 'forms'
        ]);
    }
}
