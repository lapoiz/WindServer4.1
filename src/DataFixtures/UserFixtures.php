<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userEncoder;

    public function __construct(UserPasswordEncoderInterface $userEncoder)
    {
        $this->userEncoder = $userEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin')
            ->setPassword($this->userEncoder->encodePassword($user, 'admin'));
        $manager->persist($user);

        $manager->flush();
    }
}
