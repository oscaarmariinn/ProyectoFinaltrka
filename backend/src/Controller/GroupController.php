<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;


#[Route('/group')]
final class GroupController extends AbstractController
{

    #[Route(name: 'app_group_index', methods: ['GET'])]
    public function index(GroupRepository $groupRepository): Response
    {
        return $this->render('group/index.html.twig', [
            'groups' => $groupRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_group_new', methods: ['GET', 'POST'])]
    #[Route('/new', name: 'app_group_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepo
    ): Response {
        $group = new Group();


        $group->setCreator($this->getUser());

        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Opcional: añadir usuarios por email (PRO)
            $emailsRaw = $form->get('userEmails')->getData();
            if ($emailsRaw) {
                $emails = array_map('trim', explode(',', $emailsRaw));
                foreach ($emails as $email) {
                    if (!$email) continue;
                    $user = $userRepo->findOneBy(['email' => $email]);
                    if ($user && !$group->getUsers()->contains($user)) {
                        $group->addUser($user);
                    } elseif (!$user) {
                        $this->addFlash('error', "No existe usuario: $email");
                    }
                }
            }

            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('app_group_index');
        }

        return $this->render('group/new.html.twig', [
            'form' => $form,
        ]);
    }




    #[Route('/{id}', name: 'app_group_show', methods: ['GET'])]
    public function show(Group $group): Response
    {
        return $this->render('group/show.html.twig', [
            'group' => $group,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_group_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Group $group,
        EntityManagerInterface $entityManager,
        UserRepository $userRepo
    ): Response
    {
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $emailsRaw = $form->get('userEmails')->getData();

            if ($emailsRaw) {

                $emails = array_map('trim', explode(',', $emailsRaw));

                foreach ($emails as $email) {

                    if (!$email) continue;

                    $user = $userRepo->findOneBy(['email' => $email]);

                    if ($user) {

                        if (!$group->getUsers()->contains($user)) {
                            $group->addUser($user);
                        }

                    } else {
                        $this->addFlash('error', "No existe usuario: $email");
                    }
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_group_edit', [
                'id' => $group->getId()
            ]);
        }

        return $this->render('group/edit.html.twig', [
            'form' => $form,
            'group' => $group
        ]);
    }


    #[Route('/{id}', name: 'app_group_delete', methods: ['POST'])]
    public function delete(Request $request, Group $group, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$group->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($group);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
    }
}
