<?php

namespace App\Form;

use App\Entity\Spot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('noteGeneral', TextType::class,[
                'label' => 'Note du spot de 0 à 5.'])

            ->add('description', TextareaType::class, array('attr' => array('class' => 'ckeditor')))

            ->add('isFoil', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non'  => false,
                ],
                'label' => 'Foil praticable'
            ])
            ->add('descFoil', TextareaType::class, array(
                'attr' => array('class' => 'ckeditor'),
                'label' => 'Description des conditions pour le foil.'))

            ->add('isContraintEte', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non'  => false,
                ],
                'label' => 'Contraintes en été ?'])
            ->add('descContraintEte', TextareaType::class, array(
                'attr' => array('class' => 'ckeditor'),
                'label' => 'Description concernant les contraintes en été.'))


            ->add('descWave', TextareaType::class, array(
                'attr' => array('class' => 'ckeditor'),
                'label' => 'Description des conditions de vagues.'))

            ->add('uRLMap', null, [
                'label' => 'URL de la Map'])
            ->add('urlBalise', null, [
                'label' => 'URL de la Balise la plus proche'])
            ->add('urlWebcam', null, [
                'label' => 'URL de la Webcam du spot'])
            ->add('urlWindFinder', null, [
                'label' => 'URL de Windfinder'])
            ->add('uRLWindguru', null, [
                'label' => 'URL de Windguru'])
            ->add('uRLMerteo', null, [
                'label' => 'URL de Merteo'])
            ->add('uRLMeteoConsult', null, [
                'label' => 'URL de Meteo consult'])
            ->add('uRLMeteoFrance', null, [
                'label' => 'URL de Meteo France'])
            ->add('uRLTempWater', null, [
                'label' => 'URL du site de la T°C de l eau '])

            ->add('imageFile', FileType::class, [
                'required' => false
            ])

            ->add('desc_route', TextareaType::class, array(
                'attr' => array('class' => 'ckeditor'),
                'label' => 'Description de la route pour se rendre au spot'))
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
