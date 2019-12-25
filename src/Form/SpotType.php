<?php

namespace App\Form;

use App\Entity\Spot;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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

            ->add('shortDescription', CKEditorType::class, array(

                'label' => 'Courte description du spot.')
            )

            ->add('niveauMin',ChoiceType::class, array(
                'choices' => array('stagiaire'=>0, 'débutant'=>1, 'autonome'=>2, 'bon'=>3, 'expert'=>4),
                    'label' => 'Niveau minimum pour naviguer.'
            ))

            ->add('description', CKEditorType::class, array(

                'help' => 'Couleurs: top: Navy, OK: darkgreen, warn: orange, KO: red '
            ))

            ->add('descOrientationVent', TextareaType::class, array(
                'label' => 'Description sur l orientation du vent'
            ))

            ->add('isFoil', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non'  => false,
                ],
                'label' => 'Foil praticable'
            ])
            ->add('descFoil', CKEditorType::class, array(
                'label' => 'Description des conditions pour le foil.'
            ))

            ->add('isContraintEte', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non'  => false,
                ],
                'label' => 'Contraintes en été ?'])
            ->add('descContraintEte', CKEditorType::class, array(
                'label' => 'Description concernant les contraintes en été.'))


            ->add('descWave', CKEditorType::class, array(
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

            ->add('desc_route', CKEditorType::class, array(

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

            ->add('region', EntityType::class, array(
                'class' => 'App\Entity\Region',
                'choice_label' => 'nom',
                'expanded' => false,
                'multiple' => false,

            ))
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
