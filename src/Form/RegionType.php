<?php

namespace App\Form;

use App\Entity\Region;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null,  [
                'label' => 'Nom de la région'])

            ->add('codeRegion', TextType::class,[
                'label' => 'Code de la région: 3 ou 4 lettres, sans espace'])

            ->add('numDisplay', IntegerType::class ,[
                'label' => "Numéro correspondant à l'ordre d'affichage"])


            ->add('description', TextareaType::class,
                array('attr' => array('class' => 'ckeditor')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Region::class,
            'translation_domain' => 'forms'
        ]);
    }
}
