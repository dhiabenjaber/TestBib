<?php

namespace App\Form;

use App\Entity\Borrowing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditBorrowingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('returnDateTime',DateTimeType::class,[
                "widget"=>"single_text",
                "required"=>false,
            ])
            ->add('status',ChoiceType::class,[
                "multiple"=>false,
                "expanded"=>false,
                "choices"=>["Returned"=>"Returned","Never Returned"=>"Never Returned"]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Borrowing::class,
        ]);
    }
}
