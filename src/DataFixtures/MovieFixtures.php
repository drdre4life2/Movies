<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
       $movie  = new Movie();
       $movie->setTitle('Star wars');
       $movie->setReleaseYear(2018);
       $movie->setDescription('A junk description of the movie');
       $movie->setImagePath('https://cdn.pixabay.com/photo/2016/10/09/00/18/star-wars-1724901__480.jpg');

       $movie->addActor($this->getReference('actor_1'));
       $movie->addActor($this->getReference('actor_2'));

       $manager->persist($movie);

       $movie2  = new Movie();
       $movie2->setTitle('Bat Man');
       $movie2->setReleaseYear(2020);
       $movie2->setDescription('A junk description of the green');
       $movie2->setImagePath('https://cdn.pixabay.com/photo/2023/03/14/22/20/relationship-7853278__480.jpg');

       $movie2->addActor($this->getReference('actor_3'));
       $movie2->addActor($this->getReference('actor_4'));

       $manager->persist($movie2);

       $manager->flush();

    }
}
