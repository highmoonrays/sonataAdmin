<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Form\DataTransferObject\ProductDTO;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\Processor\ProductCreator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @var ProductCreator
     */
    private $creator;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ProductController constructor.
     * @param ProductCreator $creator
     * @param EntityManagerInterface $em
     */
    public function __construct(ProductCreator $creator, EntityManagerInterface $em)
    {
        $this->creator = $creator;
        $this->em = $em;
    }

    /**
     * @Route("/", name="products", methods={"GET"})
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function products(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/createProduct", name="createNewProduct", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function createNewProduct(Request $request): Response
    {
        $productDTO = new ProductDTO();
        $form = $this->createForm(ProductType::class, $productDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = Product::createProductFromDTO($productDTO);
            $this->em->persist($product);
            $this->em->flush();

            return $this->redirectToRoute('products');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="showProduct", methods={"GET"})
     * @param Product $product
     * @return Response
     */
    public function showProduct(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="editProduct", methods={"GET","POST"})
     * @param Request $request
     * @param Product $product
     * @return Response
     * @throws \Exception
     */
    public function editProduct(Request $request, Product $product): Response
    {
        $productDTO = ProductDTO::createDTOFromProduct($product);
        $form = $this->createForm(ProductType::class, $productDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            Product::updateProductFromDTO($product, $productDTO);
            $this->em->flush();

            return $this->redirectToRoute('products');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="deleteProduct", methods={"DELETE"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function deleteProduct(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('products');
    }
}
