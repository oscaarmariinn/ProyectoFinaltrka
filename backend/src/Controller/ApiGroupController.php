<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ApiGroupController extends AbstractController
{
    #[Route('/api/group', name: 'app_api_group')]
    public function index(): Response
    {
        return $this->render('api_group/index.html.twig', [
            'controller_name' => 'ApiGroupController',
        ]);
    }
}
