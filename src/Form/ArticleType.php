<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => function(Category $category){
                    return $category->getTitle();
                },
                'label' => 'Catégorie',
                'placeholder' => '-- Choisir une catégorie --',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('image', UrlType::class, [
                'attr' => [
                'placeholder' => 'Entrez l\'url d\'une image'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
