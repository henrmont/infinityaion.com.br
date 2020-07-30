<?php

namespace App\Form;

use App\Entity\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class FormItem extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('aion_id', IntegerType::class,[
                'required'      =>  false
            ])
            ->add('name', TextType::class)
            // ->add('type', ChoiceType::class)
            ->add('level', IntegerType::class)
            ->add('price', IntegerType::class)
            ->add('discount', IntegerType::class, [
                'required'      =>  false
            ])
            // ->add('created_at', DateType::class)
            // ->add('modified_at', DateType::class)
            ->add('bbcode', TextType::class, [
                'required'      =>  false
            ])
            // ->add('promo', CheckboxType::class, [
            //     'required'  =>  false
            // ])
            ->add('amount', IntegerType::class, [
                'required'  =>  false
            ])
            ->add('race', ChoiceType::class, [
                'choices'  =>  [
                    'ASMODIANS'     => 'ASMODIANS',
                    'ELYOS'         => 'ELYOS',
                    'ANY'           => 'ANY'
                ]
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
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }
}
