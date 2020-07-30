<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('username')
            // ->add('roles')
            // ->add('password')
            // ->add('created_at')
            // ->add('modified_at')
            // ->add('coin')
            ->add('email', TextType::class, [
                'label'     =>  'Email: '
            ])
            ->add('image', FileType::class, [
                'label' => 'Anexar',

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
            ->add('name', TextType::class, [
                'label'     =>  'Nome de exibição:'
            ])
            // ->add('race')
            // ->add('tagFeed')
            // ->add('tagShop')
            // ->add('tagCoin')
            // ->add('tagTicket')
            ->add('submit', SubmitType::class,[
                'label'     =>  'Editar'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
