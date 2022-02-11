<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {

        $this->hasher = $hasher;
    }


    public function load(ObjectManager $manager): void
    {

        // Admin
        $admin = new User();
        $admin->setPseudo('admin')
              ->setEmail('admin@symfony.com')
              ->setPassword($this->hasher->hashPassword($admin, 'password'))
              ->setRoles(["ROLE_ADMIN"]);

        $manager->persist($admin);

        $faker = Factory::create();

        for($i = 0; $i < 3; $i++){
            $category = new Category();

            $category->setTitle($faker->word())
                     ->setDescription($faker->sentence());

            $manager->persist($category);

            for($a = 1; $a < mt_rand(5, 10); $a++){
                $article = new Article();

                $sentences = $faker->sentence() . '' . $faker->sentence() . '' . $faker->sentence() . '' . $faker->sentence() . '' . $faker->sentence();

                $article->setTitle($faker->word())
                        ->setDescription($sentences)
                        ->setImage('https://picsum.photos/200/300')
                        ->setCreatedAt(new \DateTimeImmutable())
                        ->setCategory($category);

                    $manager->persist($article);
                        
                for($c = 1; $c < mt_rand(2, 6); $c++){
                    $comment = new Comment();

                    $comment->setContent($faker->sentence())
                            ->setArticle($article)
                            ->setAuthor($faker->name())
                            ->setCreatedAt(new \DateTimeImmutable());

                    $manager->persist($comment);
                }
                        
            }

        }

        $manager->flush();
    }
}
