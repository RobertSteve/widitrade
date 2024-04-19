<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository,
    ) {
    }

    /**
     * @Route("/", name="producto_index")
     */
    public function index(): Response
    {
        $products = $this->productRepository->ratingOrdered();
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }
}