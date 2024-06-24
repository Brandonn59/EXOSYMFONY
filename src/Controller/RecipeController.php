<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeetRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * This function displays all ingredients
     * @param IngredientRepository $ingredientRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    // #[Route('recipe/indexx.html.twig', name: 'app_recipe', methods:['GET'])]
    // public function index(): Response
    // {
    //     return $this->render('recipe/index.html.twig', [
    //         'controller_name' => 'RecipeController',
    //     ]);
    // }

    #[Route('/recipe', name: 'app_recipe', methods: ['GET'])]
    public function indexx(RecipeetRepository $recipeRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $recipes = $paginator->paginate(
            $recipeRepository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
        
    }

    #[Route('/recipe/new.html.twig', name: 'app_recipe_new' , methods:['GET','POST'])]
    public function new() :   Response 
    {
        return $this->render('pages/recipe/new.html.twig'); 
    }
    
}
