<?php

namespace App\DataFixtures;


use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Purchase;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasherInterface;

    public function __construct(UserPasswordHasherInterface $userPasswordHasherInterface){

        $this->userPasswordHasherInterface = $userPasswordHasherInterface; 
    }
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");                  
        // $faker->addProvider(new Bluemmb\Faker\PicsumPhotosProvider($faker));


        $allUser = [];
        $allCategory = []; 
        $allProduct = [];
        $allPurchase = [];  

         //************ */ CATEGORIES
         for ($i = 0; $i < 15; $i++) {
                        

            $category = new Category();
            $category->setName($faker->word()); 
            $category->setDescription($faker->paragraph()); 
                 
            $manager->persist($category);

            array_push($allCategory, $category); 

         
        }

        
        // *************PRODUCTS
        for ($a = 0; $a < 40; $a++) {
                        
            $product = new Product();
            $product->setName($faker->word()); 
            $product->setDescription($faker->paragraph()); 
            $product->setImage($faker->imageUrl(640, 480,true)); 
            $product->setStock($faker->randomNumber(2, false)); 
            $product->setSlug($faker->word()); 
            $product->setPrice($faker->randomFloat(2, 5, 30)); 
            
            // Ajout catégorie aux produits
                for($c = 0; $c<= rand(0,4);$c++)
                {
                    $product->addCategory($allCategory[array_rand($allCategory,1)]);
                } 
            
            array_push($allProduct, $product);  
    
            $manager->persist($product);

        }
        

        //**********  AJOUT USER 
        for ($b = 0; $b < 25; $b++) {
                        

            $user = new User();
            $user->setLastname($faker->name()); 
            $user->setFirstname($faker->firstName()); 
            $user->setEmail($faker->unique()->email());
            $user->setRoles([]); 
            $user->setPassword($this->userPasswordHasherInterface->hashPassword($user,"testtest")); 
            $user->setCreatedAt($faker->datetime()); 

            if($b > 5 ) {
                $user->setAddress($faker->address());   
            }

            // *********AJOUT ACHATS PAR USER (PURCHASE) 
            for($p = 0; $p<= rand(0,12);$p++){

                $purchase = new Purchase();
                $purchase->setAmount($faker->randomFloat(2, 10, 80)); 
                $purchase->setQuantity($faker->randomNumber(2, false)); 

                $purchase->setProduct($allProduct[array_rand($allProduct,1)]);
                $purchase->setUser($user); 

                $manager->persist($purchase);

                array_push($allPurchase, $purchase); 
                
            }


                        
            $manager->persist($user);

            array_push($allUser, $user); 

        }


        $manager->flush();
    }
}
