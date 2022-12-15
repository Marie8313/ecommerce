<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Purchase;
use App\Form\PurchaseFormType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PurchaseController extends AbstractController
{
    #[Route('/purchase', name: 'app_purchase')]
    public function index(): Response
    {
        return $this->render('purchase/index.html.twig', [
            'controller_name' => 'PurchaseController',
        ]);
    }

    // #[Entity('product', expr: 'repository.find(product_id)')]
    // #[Entity('product', options: ['id' => 'product_id'])]
    #[IsGranted("ROLE_USER")]
    #[Route('/create/{idProduct}', 
        name: 'purchase_new', 
    )]
    public function create(int $idProduct,EntityManagerInterface $em, Request $request): Response
    {   
        // $this->denyAccessUnlessGranted("ROLE_USER"); 
        $product = $em->getRepository(Product::class)->find($idProduct);

        if($product === null){
            return $this->redirectToRoute('app_home'); 
        }

        $purchase = new Purchase();
        $form = $this->createForm(PurchaseFormType::class, $purchase);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $purchase->setUser($this->getUser()); 
            $purchase->setProduct($product); 
            $purchase->setAmount($product->getPrice()*$purchase->getQuantity()); 
            $em->persist($product);  

            $product->decrementStock($purchase->getQuantity()); 
            $em->persist($purchase);

            $em->flush(); 


            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('purchase/index.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);

    }
}
