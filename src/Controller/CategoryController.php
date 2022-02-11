<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{

    /**
     * @Route("/category/create", name="category_create")
     * @Route("/category/{title<[a-z]+>}/edit", name="category_edit")
     * @IsGranted("ROLE_ADMIN", message="Cette page est accessible uniquement par les admins.")
     */
    public function handleCategory(Category $category = null, Request $request, EntityManagerInterface $em)
    {
        if(!$category){
            $category = new Category();
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);        

        if($form->isSubmitted() && $form->isValid()){
            
            $category->setTitle(strtolower($category->getTitle()));
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('category_show', ['title' => $category->getTitle()]);
        }

        if(!$category->getId()){
            $title = 'Créer une catégorie';
        }else{
            $title = 'Modifier la catégorie';
        }

        return $this->render('category/handleCategory.html.twig', [
            'formCategory' => $form->createView(),
            'title' => $title
        ]);
    }


    /**
     * @Route("/category/{title<[a-z]+>}", name="category_show")
     */
    public function show(Category $category): Response
    {
        $articles = $category->getArticles();

        return $this->render('category/show.html.twig', [
            'category' => $category->getTitle(),
            'articles' => $articles
        ]);
    }
}
