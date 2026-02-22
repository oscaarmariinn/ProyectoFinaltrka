<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/user', name: 'app_api_user')]
class ApiUserController extends AbstractController
{
    #[Route('/profile', name: 'profile', methods: ['PATCH'])]
    public function updateProfile(Request $request, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'No autenticado'], 401);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $user->setName($data['name']);
        }

        if (isset($data['surname'])) {
            $user->setSurname($data['surname']);
        }

        $em->flush();

        return new JsonResponse([
            'id'      => $user->getId(),
            'email'   => $user->getEmail(),
            'roles'   => $user->getRoles(),
            'name'    => $user->getName(),
            'surname' => $user->getSurname(),
        ]);
    }

    #[Route('/password', name: 'password', methods: ['PATCH'])]
    public function updatePassword(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'No autenticado'], 401);
        }

        $data = json_decode($request->getContent(), true);

        $currentPassword = $data['currentPassword'] ?? null;
        $newPassword     = $data['newPassword']     ?? null;

        if (!$currentPassword || !$newPassword) {
            return new JsonResponse(['message' => 'Faltan campos obligatorios'], 400);
        }

        if (!$hasher->isPasswordValid($user, $currentPassword)) {
            return new JsonResponse(['message' => 'La contraseña actual es incorrecta'], 400);
        }

        if (strlen($newPassword) < 6) {
            return new JsonResponse(['message' => 'La nueva contraseña debe tener al menos 6 caracteres'], 400);
        }

        $user->setPassword($hasher->hashPassword($user, $newPassword));
        $em->flush();

        return new JsonResponse(['message' => 'Contraseña actualizada correctamente']);
    }
}
