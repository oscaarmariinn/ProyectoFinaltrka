<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
}
