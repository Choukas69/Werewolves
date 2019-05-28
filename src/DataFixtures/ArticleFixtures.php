<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ArticleFixtures extends Fixture
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();

            $user->setEmail($faker->email)
                ->setPseudo($faker->firstName)
                ->setPassword($this->passwordEncoder->encodePassword($user, 'test'))
                ->setRoles(['ROLE_USER'])
                ->setBanned(false);

            $manager->persist($user);

            for ($j = 1; $j <= $faker->numberBetween(0, 30); $j++) {
                $article = new Article();

                $article->setTitle($faker->title)
                    ->setCreatedAt($faker->dateTime)
                    ->setAuthor($user)
                    ->setContent($faker->text)
                    ->setOnline($faker->numberBetween(0, 1));

                $manager->persist($article);
            }
        }

        $manager->flush();
    }
}
