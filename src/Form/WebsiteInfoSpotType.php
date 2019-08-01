<?php

namespace App\Form;

use App\Entity\Spot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebsiteInfoSpotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('websiteInfos', CollectionType::class, [
                'entry_type' => WebsiteInfoType::class,
                'entry_options' => array('label' => false),
                'label' => 'Website sur le spot',
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('Save',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Spot::class,
            'translation_domain' => 'forms',
            'attr' => array('id' => 'websiteInfo_form')
        ]);
    }
}
