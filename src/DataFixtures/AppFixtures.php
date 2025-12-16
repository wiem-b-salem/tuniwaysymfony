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
        // 4. Create Real Places in Tunisia
        $placesData = [
            // HISTORICAL
            [
                'name' => 'Amphitheatre of El Djem',
                'category' => 'Historical',
                'description' => 'One of the largest Roman amphitheaters in the world and a UNESCO World Heritage site.',
                'lat' => 35.2966,
                'lon' => 10.7063,
                'address' => 'El Djem, Mahdia, Tunisia',
            ],
            [
                'name' => 'Carthage Ruins',
                'category' => 'Historical',
                'description' => 'Ancient ruins of the Phoenician and Roman city of Carthage overlooking the Mediterranean.',
                'lat' => 36.8529,
                'lon' => 10.3230,
                'address' => 'Carthage, Tunis, Tunisia',
            ],
            [
                'name' => 'Kairouan Great Mosque',
                'category' => 'Historical',
                'description' => 'One of the most important mosques in Islam and a UNESCO World Heritage site.',
                'lat' => 35.6781,
                'lon' => 10.0963,
                'address' => 'Kairouan, Tunisia',
            ],

            // BEACHES
            [
                'name' => 'Sidi Bou Said Beach',
                'category' => 'Beach',
                'description' => 'A scenic beach near the famous blue-and-white village of Sidi Bou Said.',
                'lat' => 36.8705,
                'lon' => 10.3428,
                'address' => 'Sidi Bou Said, Tunis, Tunisia',
            ],
            [
                'name' => 'Hammamet Beach',
                'category' => 'Beach',
                'description' => 'A popular sandy beach known for its clear waters and resorts.',
                'lat' => 36.3996,
                'lon' => 10.6166,
                'address' => 'Hammamet, Nabeul, Tunisia',
            ],
            [
                'name' => 'Djerba Beach',
                'category' => 'Beach',
                'description' => 'A beautiful Mediterranean beach located on the island of Djerba.',
                'lat' => 33.8076,
                'lon' => 10.8451,
                'address' => 'Djerba, Medenine, Tunisia',
            ],

            // MUSEUMS
            [
                'name' => 'Bardo National Museum',
                'category' => 'Museum',
                'description' => 'World-famous museum housing one of the largest collections of Roman mosaics.',
                'lat' => 36.8093,
                'lon' => 10.1346,
                'address' => 'Le Bardo, Tunis, Tunisia',
            ],
            [
                'name' => 'Dar Ben Abdallah Museum',
                'category' => 'Museum',
                'description' => 'Museum showcasing traditional Tunisian culture and daily life.',
                'lat' => 36.7979,
                'lon' => 10.1713,
                'address' => 'Medina of Tunis, Tunisia',
            ],
            [
                'name' => 'El Djem Museum',
                'category' => 'Museum',
                'description' => 'Museum dedicated to Roman history and artifacts found in El Djem.',
                'lat' => 35.2969,
                'lon' => 10.7069,
                'address' => 'El Djem, Mahdia, Tunisia',
            ],

            // PARKS
            [
                'name' => 'Belvedere Park',
                'category' => 'Park',
                'description' => 'The largest urban park in Tunis, offering green spaces and a zoo.',
                'lat' => 36.8167,
                'lon' => 10.1658,
                'address' => 'Tunis, Tunisia',
            ],
            [
                'name' => 'Ichkeul National Park',
                'category' => 'Park',
                'description' => 'UNESCO-listed national park known for its lake and migratory birds.',
                'lat' => 37.1519,
                'lon' => 9.6764,
                'address' => 'Bizerte, Tunisia',
            ],

            // RESTAURANTS
            [
                'name' => 'Dar El Jeld',
                'category' => 'Restaurant',
                'description' => 'A high-end restaurant serving authentic Tunisian cuisine.',
                'lat' => 36.7974,
                'lon' => 10.1702,
                'address' => 'Medina of Tunis, Tunisia',
            ],
            [
                'name' => 'Fondouk El Attarine',
                'category' => 'Restaurant',
                'description' => 'Traditional restaurant located inside the old Medina of Tunis.',
                'lat' => 36.7985,
                'lon' => 10.1709,
                'address' => 'Tunis Medina, Tunisia',
            ],
            [
                'name' => 'El Ali Restaurant',
                'category' => 'Restaurant',
                'description' => 'Restaurant offering Tunisian dishes with a view over the Medina.',
                'lat' => 36.7992,
                'lon' => 10.1717,
                'address' => 'Medina of Tunis, Tunisia',
            ],

            // HOTELS
            [
                'name' => 'La Badira Hotel',
                'category' => 'Hotel',
                'description' => 'Luxury hotel located on the coast of Hammamet.',
                'lat' => 36.4305,
                'lon' => 10.6440,
                'address' => 'Hammamet, Nabeul, Tunisia',
            ],
            [
                'name' => 'Mövenpick Hotel Gammarth',
                'category' => 'Hotel',
                'description' => 'Five-star hotel overlooking the Mediterranean Sea.',
                'lat' => 36.9176,
                'lon' => 10.2843,
                'address' => 'Gammarth, Tunis, Tunisia',
            ],
            [
                'name' => 'Hasdrubal Thalassa',
                'category' => 'Hotel',
                'description' => 'Luxury thalasso and spa hotel in Djerba.',
                'lat' => 33.8219,
                'lon' => 10.8482,
                'address' => 'Djerba, Tunisia',
            ],
            [
                'name' => 'El Mouradi Palace',
                'category' => 'Hotel',
                'description' => 'Resort hotel located in Port El Kantaoui.',
                'lat' => 35.8925,
                'lon' => 10.5932,
                'address' => 'Port El Kantaoui, Sousse, Tunisia',
            ],
            [
                'name' => 'Royal Azur Hotel',
                'category' => 'Hotel',
                'description' => 'Luxury beachfront hotel with private beach access.',
                'lat' => 36.4029,
                'lon' => 10.6160,
                'address' => 'Hammamet, Tunisia',
            ],
        ];

        foreach ($placesData as $data) {
            $place = new Place();
            $place->setName($data['name']);
            $place->setCategory($data['category']);
            $place->setDescription($data['description']);
            $place->setLatitude($data['lat']);
            $place->setLongtitude($data['lon']);
            $place->setAddress($data['address']);
            $place->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($place);
        }





        $manager->flush();
    }
}
