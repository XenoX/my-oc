<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user
            ->setUsername('user')
            ->setPassword($this->encoder->encodePassword($user, 'user'))
            ->setEmail('user@user.com')
        ;

        $this->setReference('user', $user);

        $manager->persist($user);
        $manager->flush();
    }
}
