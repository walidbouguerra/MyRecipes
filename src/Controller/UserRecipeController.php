<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/user/recettes', name: 'user.recipe.')]
class UserRecipeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $user = $this->getUser();
        return $this->render('user/recipe/index.html.twig', [
            'recipes' => $user->getRecipes()
        ]);
    }

    #[Route('/{slug}-{id}', name: 'show', requirements: ['slug' => Requirement::ASCII_SLUG ,'id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(Recipe $recipe): Response
    {
        return $this->render('user/recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recipe);
            $entityManager->flush();

            return $this->redirectToRoute('user.recipe.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/recipe/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }   

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            return $this->redirectToRoute('user.recipe.show', ['id' => $recipe->getId(),'slug' =>$recipe->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }
    
    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recipe->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($recipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user.recipe.index', [], Response::HTTP_SEE_OTHER);
    }
}
