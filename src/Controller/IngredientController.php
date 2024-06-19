<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'app_ingredient')]
    public function index(IngredientRepository $ingredientRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients =


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
}
