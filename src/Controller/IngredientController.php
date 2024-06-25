<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    /**
     * This function displays all ingredients
     *
     * @param IngredientRepository $ingredientRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'app_ingredient', methods:['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(IngredientRepository $ingredientRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $ingredientRepository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    /**
     * This controller shows the form to add an ingredient to the database
     * 
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/nouveau', name: 'app_ingredient_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $ingredient->setUser($this->getUser());
            
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été créé avec succès !'
            );

            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This controller shows the form to edit an ingredient
     * 
     * @param IngredientRepository $ingredientRepository
     * @param int $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/edition/{id}', name: 'app_ingredient_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(IngredientRepository $ingredientRepository, int $id, Request $request, EntityManagerInterface $manager): Response
    {
        $ingredient = $ingredientRepository->findOneBy(["id" => $id]);
        if (!$ingredient) {
            throw $this->createNotFoundException('L\'ingrédient n\'a pas été trouvé');
        }

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès !'
            );

            return $this->redirectToRoute('app_ingredient');
        }
        
        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This controller deletes an ingredient
     * 
     * @param EntityManagerInterface $manager
     * @param int $id
     * @param IngredientRepository $ingredientRepository
     * @return Response
     */
    #[Route('/ingredient/suppression/{id}', name: 'app_ingredient_delete', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function delete(EntityManagerInterface $manager, int $id, IngredientRepository $ingredientRepository): Response
    {
        $ingredient = $ingredientRepository->findOneBy(["id" => $id]);
        if (!$ingredient) {
            $this->addFlash(
                'error',
                "Votre ingrédient n'a pas été trouvé !"
            );

            return $this->redirectToRoute('app_ingredient');
        }

        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            "Votre ingrédient a été supprimé avec succès !"
        );

        return $this->redirectToRoute('app_ingredient');
    }
}
