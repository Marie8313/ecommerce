<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\PurchaseRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(UserRepository $repoUser, ProductRepository $repoProduct, CategoryRepository $repoCategory, PurchaseRepository $repoPurchase): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'users' => $repoUser->findAll(), 
            'products' => $repoProduct->findAll(), 
            'purchases' => $repoPurchase->findAll(),
        ]);
    }
}
