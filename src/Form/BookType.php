<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Editor;
use App\Entity\Image;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class,[
                "required"=>true,
            ])
            ->add('nbrPages',null,["label"=>"Number of Pages"])
            ->add('editionDate',DateType::class,[
                "widget"=>"single_text"
            ])
            ->add('nbrCopies',null,["label"=>"Number of Copies"])
            ->add('price',MoneyType::class,["currency"=>"TND","html5"=>true])
            ->add('isbn',NumberType::class,["label"=>"ISBN","html5"=>true,"attr"=>["min"=>1000000000000,"max"=>99999999999999]])
            ->add('category',EntityType::class,[
                "class"=>Category::class,
                "attr"=>["class"=>"js-example-placeholder-single"],
            ])
            ->add('editor',EntityType::class,[
                "class"=>Editor::class,
                "attr"=>["class"=>"js-example-basic-single"]
            ])
            ->add("authors",null,[
                "expanded"=>false,
                "required"=>true,
                "attr"=>["class"=>"js-example-basic-multiple"]
            ])
            ->add('images', CollectionType::class ,[
                    "entry_type"=>ImageType::class,
                    'allow_add' => true,
                    "allow_delete"=>true,
                    "allow_file_upload"=>true,
                    "prototype"=>true,
                    "by_reference"=>false,
                    'required' => is_null($builder->getData()->getId()),
                    'entry_options' => [
                        'label'     => false,
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
