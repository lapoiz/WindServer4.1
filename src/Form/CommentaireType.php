<?php

namespace App\Form;

use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, array('label' => 'Titre'))
            ->add('commentaire', TextareaType::class, array(
                'attr' => array('class' => 'ckeditor'),
                'label' => 'Commentaire sur le spot'))
            ->add('mail', TextType::class, array('label' => 'Votre email '))
            ->add('pseudo', TextType::class, array('label' => 'Votre pseudo '))

            ->add('Envoyer',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
            'translation_domain' => 'forms',
            'attr' => array('id' => 'comment_form')
        ]);
    }
}
