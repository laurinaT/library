<?php

namespace App\Form;

use App\Entity\Books;
use App\Entity\History;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;



class BookFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('title', TextType::class, ["attr" => ["class" => "form-control", "placeholder" => 'Titre du livre'], 'required' => true])
            ->add('author', TextType::class, ["attr" => ["class" => "form-control", "placeholder" => 'Auteur du livre',]])
            ->add('description', TextareaType::class, ["attr" => ["class" => "form-control"]])
            ->add('publisher', TextType::class, ["attr" => ["class" => "form-control"]])
            ->add('category', TextType::class, ["attr" => ["class" => "form-control"]])
            ->add(
                'cover',
                FileType::class,
                [
                    "label" => "Ajouter votre couverture",
                    "mapped" => false,
                    "required" => false,
                    "constraints" => [new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => ['image/png', 'image/jpeg'],
                        'mimeTypesMessage' => 'Veuillez choisir une image au format jpeg ou png',
                    ])],
                ]
            )

            ->add('save', SubmitType::class, ["label" => "Envoyer", "attr" => ["class" => "btn btn-green"]]);
    }
}