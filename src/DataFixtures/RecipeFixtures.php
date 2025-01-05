<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use FakerRestaurant\Provider\fr_FR\Restaurant;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecipeFixtures extends Fixture
{ 
    public function __construct(private readonly SluggerInterface $slugger) 
    {
        
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Restaurant($faker));

        // Create 10 recipes
        for ($i=0; $i < 10; $i++) {
            $title = $faker->foodName();
            $recipe = (new Recipe())
                ->setCategory($this->getReference('CATEGORY' . $faker->numberBetween(0,2), Category::class))
                ->setTitle($title)
                ->setSlug($this->slugger->slug($title))
                ->setContent($faker->paragraphs(10, true))
                ->setDuration($faker->numberBetween(10, 60))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setCreatedBy($this->getReference('USER' . $faker->numberBetween(0, 9), User::class));
                $manager->persist($recipe);
            }

        $manager->flush();
    }
}
