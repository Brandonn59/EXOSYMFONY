<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // Initialize ingredients array
        $ingredients = [];
        for ($i = 0; $i < 50; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word())
                       ->setPrice(mt_rand(0, 100));
            $manager->persist($ingredient);
            $ingredients[] = $ingredient; // Add to ingredients array
        }

        // Create recipes and add ingredients
        for ($j = 0; $j < 25; $j++) {
            $recipe = new Recipe();
            $recipe->setName($this->faker->word())
                   ->setTime(mt_rand(1, 1440))
                   ->setNbPeople(mt_rand(0,1) == 1 ? mt_rand(1, 50):null)
                   ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5):null)
                   ->setDescription($this->faker->text(300)) // Fixed typo in 'setDescription'
                   ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null)
                   ->setIsFavorite(mt_rand(0, 1) == 1? true:false);

            for ($k=0; $k < mt_rand(5, 15); $k++) {
                $recipe->addIngredient($ingredients[mt_rand(0,count($ingredients)-1)]);
            }

            $manager->persist($recipe);
        }

        $manager->flush();
    }
}


