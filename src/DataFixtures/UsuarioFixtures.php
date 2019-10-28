<?php

namespace App\DataFixtures;

use App\Entity\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UsuarioFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new Usuario();
        $user->setUsername('usuario')
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$qvLXqSQrDAyxzyiJua3nJQ$FhBIMGPbBzKGOuUG45hf95un+WEQ3ZSgHbTSMoeh88s');

        $manager->persist($user);
        $manager->flush();
    }
}
