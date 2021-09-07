<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Users;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UsersFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        function inituser(array $roles, string $email)
        {
            $user = new Users();
            $user->setRoles($roles);
            $user->setEmail($email);
            $user->setIsVerified(true);
            return $user;
        };

        $faker = Faker\Factory::create('fr_FR');

        for ($nbUsers = 1; $nbUsers <= 20; $nbUsers++) {

            if ($nbUsers === 1) {

                $newuser = inituser(['ROLE_ADMIN'], 'admin@aconde.fr');
                $newuser->setPassword($this->encoder->hashPassword($newuser, '123456'));

            } elseif ($nbUsers == 2) {

                $newuser = inituser(['ROLE_USER'], 'demo@aconde.fr');
                $newuser->setPassword($this->encoder->hashPassword($newuser, '123456'));

            } elseif ($nbUsers > 2) {
                
                $newuser = inituser(['ROLE_USER'], $faker->email);
                $newuser->setPassword($this->encoder->hashPassword($newuser, '123456'));
            }
            $manager->persist($newuser);
            // Enregistre l'utilisateur dans une référence
            $this->addReference('user_' . $nbUsers, $newuser);
        }

        $manager->flush();
    }
}
