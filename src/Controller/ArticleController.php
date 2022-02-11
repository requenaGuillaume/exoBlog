<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article/{id<\d+>}", name="article_show")
     */
    public function show(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $comment->setArticle($article)
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setAuthor($user->getPseudo());

            $em->persist($comment);
            $em->flush();

            $this->redirectToRoute('article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'formComment' => $form->createView()
        ]);
    }


    /**
     * @Route("/article/create", name="article_create")
     * @Route("/article/{id<\d+>}/edit", name="article_edit")
     * @IsGranted("ROLE_ADMIN", message="Cette page est accessible uniquement par les admins.")
     */
    public function handleArticle(Article $article = null, Request $request, EntityManagerInterface $em): Response
    {
        if(!$article){
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if(!$article->getId()){
            $title = 'Créer un article';
        }else{
            $title = "Editer l'article {$article->getTitle()}";
        }

        if($form->isSubmitted() && $form->isValid()){
            
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTimeImmutable());
            }            

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
        }

        return $this->render('article/handleArticle.html.twig', [
            'article' => $article, 
            'formArticle' => $form->createView(),
            'title' => $title
        ]);
    }
}
