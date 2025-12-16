<?php

namespace App\DataFixtures;

use App\Entity\Place;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = ['Historical', 'Beach', 'Museum', 'Park', 'Restaurant', 'Hotel'];
        $tunisLocations = [
            ['lat' => 36.8065, 'lon' => 10.1815], // Tunis
            ['lat' => 36.8625, 'lon' => 10.3314], // Carthage
            ['lat' => 36.8702, 'lon' => 10.3417], // Sidi Bou Said
            ['lat' => 35.8256, 'lon' => 10.6084], // Sousse
            ['lat' => 35.8322, 'lon' => 10.6358], // Port El Kantaoui
            ['lat' => 36.4000, 'lon' => 10.6000], // Hammamet
            ['lat' => 33.8869, 'lon' => 10.8531], // Djerba
        ];

        for ($i = 1; $i <= 20; $i++) {
            $place = new Place();
            $place->setName('Place ' . $i);
            $place->setDescription('This is a beautiful description for place number ' . $i . '. It features amazing views and great atmosphere.');
            $place->setCategory($categories[array_rand($categories)]);

            $location = $tunisLocations[array_rand($tunisLocations)];
            // Add some randomness to location so they aren't all exactly on top of each other
            $place->setLatitude($location['lat'] + (rand(-100, 100) / 10000));
            $place->setLongtitude($location['lon'] + (rand(-100, 100) / 10000));

            $place->setAddress('Address ' . $i . ', Tunisia');
            $place->setCreatedAt(new \DateTimeImmutable());
            // Optional: Set a dummy image
            // $place->setImageUrl('https://placehold.co/600x400?text=Place+' . $i);

            $manager->persist($place);
        }

        $manager->flush();
    }
}
