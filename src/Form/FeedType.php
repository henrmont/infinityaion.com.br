<?php

namespace App\Form;

use App\Entity\Feed;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class FeedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('user')
            ->add('text', TextareaType::class,[
                'label' =>  'Escreva algo...',
                'attr'  => [
                    'class' => 'form-textarea',
                    'rows'  =>  '3'
                ]
            ])
            // ->add('image')
            // ->add('created_at')
            // ->add('modified_at')
            ->add('image', FileType::class, [
                // 'label' => 'Anexar',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '5120k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => '',
                    ])
                ],
            ])
            ->add('submit', SubmitType::class,[
                'label' =>  'Publicar',
                'attr'  =>  [
                    'class'    =>  'btn-blue'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Feed::class,
        ]);
    }
}
