<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

#[Route('/recettes', name: 'recipe.')]
class RecipeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(RecipeRepository $recipeRepository): Response
    {
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipeRepository->findAll(),
        ]);
    }
    
    #[Route('/{slug}-{id}', name: 'show', requirements: ['slug' => Requirement::ASCII_SLUG ,'id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(Recipe $recipe): Response
    {
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Route('/{id}/pdf', name: 'pdf')]
    public function pdf(Recipe $recipe, \Knp\Snappy\Pdf $knpSnappyPdf)
    {
        $html = $this->renderView('recipe/pdf.html.twig', array(
            'recipe' => $recipe,
        ));

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'file.pdf'
        );
    }
}


