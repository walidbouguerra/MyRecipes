<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use FakerRestaurant\Provider\fr_FR\Restaurant;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixtures extends Fixture
{ 

    public function __construct(private readonly SluggerInterface $slugger) 
    {
        
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Restaurant($faker));

        // Categories
        $categories = ['Entrées', 'Plats', 'Desserts'];
        for ($i=0; $i < count($categories); $i++) { 
            $category = (new Category())
            ->setName($categories[$i])
            ->setSlug($this->slugger->slug($categories[$i])->lower())
            ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
            ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()));
        $manager->persist($category);
        $this->addReference('CATEGORY' . $i, $category);
        }

        $manager->flush();
    }
}
