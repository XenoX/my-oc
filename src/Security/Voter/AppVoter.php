<?php

namespace App\Security\Voter;

use App\Entity\Evaluation;
use App\Entity\Path;
use App\Entity\Session;
use App\Entity\Student;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AppVoter extends Voter
{
    public const VIEW = 'view';
    public const UPDATE = 'update';
    public const DELETE = 'delete';

    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::UPDATE, self::DELETE])
            && ($subject instanceof Path
                || $subject instanceof Session
                || $subject instanceof Evaluation
                || $subject instanceof Student
            )
        ;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($subject, $user);
            case self::UPDATE:
                return $this->canUpdate($subject, $user);
            case self::DELETE:
                return $this->canDelete($subject, $user);
        }

        return false;
    }

    private function canView($object, UserInterface $user): bool
    {
        return $user === $object->getUser();
    }

    private function canUpdate($object, UserInterface $user): bool
    {
        return $user === $object->getUser();
    }

    private function canDelete($object, UserInterface $user): bool
    {
        return $user === $object->getUser();
    }
}
