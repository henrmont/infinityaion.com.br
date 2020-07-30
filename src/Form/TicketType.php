<?php

namespace App\Form;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('user', TextType::class)
            // ->add('status')
            ->add('message', TextareaType::class, [
                'label'     =>  'Mensagem'
            ])
            ->add('title', TextType::class, [
                'label'     =>  'Título'
            ])
            // ->add('created_at')
            // ->add('modified_at')
            ->add('submit', SubmitType::class, [
                'label'     =>  'Enviar',
                'attr'      =>  [
                    'class' =>  'btn-blue'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
