<?php

namespace App\Form;

use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CommentaireType extends AbstractType
{
    /**
     * Cette méthode permet de construire le formulaire de commentaire.
     * Les champs "name" et "message" sont demandés à l'utilisateur.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom complet ou pseudonyme',
                'attr' => [
                    'placeholder' => 'Ex. Jean Dupont ou LaVoixDuClient',
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre témoignage',
                'attr' => [
                    'rows' => 6,
                    'placeholder' => 'Exprimez-vous ici...',
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('envoyer', SubmitType::class, [
                'label' => 'Poster mon témoignage',
                'attr' => [
                    'class' => 'btn btn-primary mt-3'
                ]
            ]);
    }

    /**
     * Cette méthode configure le formulaire pour qu’il fonctionne avec l’entité Commentaire.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}
