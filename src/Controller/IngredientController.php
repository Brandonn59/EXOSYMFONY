<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    /**
     * This function displays all ingredients
     * @param IngredientRepository $ingredientRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'app_ingredient', methods: ['GET', 'POST'])]
    public function index(IngredientRepository $ingredientRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupérer tous les ingrédients
        $ingredients = $paginator->paginate(
            $ingredientRepository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        // Passer les ingrédients au template
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    #[Route('/ingredient/nouveau', name: 'app_ingredient_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ingredient);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre ingredient a été créé avec succès'
            );

            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ingredient/edition/{id}', name: 'app_ingredient_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(IngredientRepository $ingredientRepository, int $id): Response  
    {
        $ingredient = $ingredientRepository->findOneBy(["id" => $id]);
        if (!$ingredient) {
            throw $this->createNotFoundException('Ingredient not found');
        }

        $form = $this->createForm(IngredientType::class, $ingredient);

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/ingredient/suppression/{id}', name: 'app_ingredient_delete', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function delete(EntityManagerInterface $manager, int $id, IngredientRepository $ingredientRepository): Response
    {
        $ingredient = $ingredientRepository->findOneBy(['id' => $id]);

        if (!$ingredient) {
            $this->addFlash(
                'error',
                "Votre ingredient n'a pas été trouvé !"
            );

            return $this->redirectToRoute('app_ingredient');
        }

        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingredient a été supprimé avec succès'
        );

        return $this->redirectToRoute('app_ingredient');
    }
}
  