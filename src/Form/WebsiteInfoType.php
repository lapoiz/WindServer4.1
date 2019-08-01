<?php

namespace App\Form;

use App\Entity\Spot;
use App\Entity\WebSiteInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebsiteInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,  [
                'label' => 'Nom du site'
            ])
            ->add('description', TextareaType::class, array('attr' => array('class' => 'ckeditor')))
            ->add('url', TextType::class, array('label' => 'ULR du site'))
            ->add('date', DateType::class, array(
                'label' => 'Date des sources',
                'widget' => 'single_text',
                'required' => false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WebSiteInfo::class
        ]);
    }
}
