<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends AbstractController
{
    /**
     * Endpoint para registrar nuevos usuarios
     * POST /api/register
     * Body: { "email": "user@example.com", "password": "123456" }
     */
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request                     $request,
        EntityManagerInterface      $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        // Decodificar el JSON del body
        $data = json_decode($request->getContent(), true);

        // Validaciones básicas
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['name']) || !isset($data['surname'])) {
            return new JsonResponse(
                ['error' => 'Email y password son obligatorios'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Verificar si el usuario ya existe
        $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(
                ['error' => 'El email ya está registrado'],
                Response::HTTP_CONFLICT
            );
        }

        // Crear usuario con contraseña hasheada
        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setName($data['name']);
        $user->setSurname($data['surname']);
        $user->setRoles(['ROLE_USER']);

        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Usuario registrado correctamente',
            'data' => [
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'name' => $user->getName(),
                    'surname' => $user->getSurname(),
                    'roles' => $user->getRoles(),
                    'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s')
                ]
            ],
            'meta' => [
                'timestamp' => time(),
                'version' => '1.0'
            ]
        ], Response::HTTP_CREATED);
    }

    /**
     * Endpoint de login - Este método NO se ejecuta
     * El json_login de security.yaml intercepta la petición
     * y Lexik devuelve el token automáticamente
     */
    #[Route('/api/login_check', name: 'api_login_check', methods: ['POST'])]
    public function loginCheck(): JsonResponse
    {
        throw new \LogicException('This method should not be reached!');
    }
}
