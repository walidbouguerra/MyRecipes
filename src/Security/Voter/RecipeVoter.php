<?php

namespace App\Security\Voter;

use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class RecipeVoter extends Voter
{
    public const EDIT = 'edit';
    public const VIEW = 'view';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Recipe;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $recipe = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($recipe, $user),
            self::EDIT => $this->canEdit($recipe, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
        return false;
    }

    private function canView(Recipe $recipe, User $user): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($recipe, $user)) {
            return true;
        } else {
            return false;
        }
    }

    private function canEdit(Recipe $recipe, User $user): bool
    {
        return $user === $recipe->getCreatedBy();
    }
}
