<?php

namespace App\DataFixtures;

use App\Entity\Place;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\User;
use App\Entity\Guide;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Create Admin
        $admin = new User();
        $admin->setEmail('admin@tuniway.com');
        $admin->setUsername('SuperAdmin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setRole('ROLE_ADMIN');
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $admin->setPhoneNumber('555-ADMIN');
        $admin->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($admin);

        // 2. Create 3 Guides
        for ($i = 1; $i <= 3; $i++) {
            $guide = new Guide();
            $guide->setEmail("guide$i@tuniway.com");
            $guide->setUsername("Guide $i");
            $guide->setRoles(['ROLE_GUIDE']);
            $guide->setRole('ROLE_GUIDE');
            $guide->setPassword($this->hasher->hashPassword($guide, 'guide123'));
            $guide->setBio("I am an experienced guide number $i specializing in historical tours.");
            $guide->setLanguages(['English', 'French', 'Arabic']);
            $guide->setPhoneNumber("555-00$i");
            $guide->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($guide);
        }

        // 3. Create 5 Users
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setEmail("user$i@tuniway.com");
            $user->setUsername("User $i");
            $user->setRoles(['ROLE_USER']);
            $user->setRole('ROLE_USER');
            $user->setPassword($this->hasher->hashPassword($user, 'user123'));
            $user->setPhoneNumber("999-00$i");
            $user->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($user);
        }

        // 4. Create Places (Existing logic)
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
