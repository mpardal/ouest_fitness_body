<?php

namespace App\Controller;

use App\Entity\ExhibitorGroup;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\ExhibitorGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/exhibitorGroup', name: 'exhibitorGroup_')]
//#[IsGranted('ROLE_ADMIN')]
class ExhibitorGroupController extends AbstractController
{
    private ExhibitorGroupRepository $exhibitorGroup;
    private EntityManagerInterface $entityManager;

    public function __construct(ExhibitorGroupRepository $exhibitorGroup, EntityManagerInterface $entityManager)
    {
        $this->exhibitorGroup = $exhibitorGroup;
        $this->entityManager = $entityManager;
    }
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $exhibitorGroup = $this->exhibitorGroup;

        $exhibitorsGroup = $exhibitorGroup->findAll();

        return $this->render('exhibitorGroup/index.html.twig', [
            'exhibitorsGroup' => $exhibitorsGroup,
        ]);
    }

    #[Route('/pre-creation', name: 'pre-create')]
    public function preCreate(Request $request): Response
    {
        $exhibitorGroupRepo = $this->exhibitorGroup;
        $entityManager = $this->entityManager;

        $form = $this->createForm(ExhibitorGroupNameType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $groupName = $form->get('groupName')->getData();

            $existingGroup = $exhibitorGroupRepo->findOneBy(['groupName' => $groupName]);

            if ($existingGroup) {
                $this->addFlash('error', 'Un groupe d\'exposition avec ce nom existe déjà.');
                return $this->redirectToRoute('exhibitor_pre-create');
            }

            $exhibitorGroup = new ExhibitorGroup();
            $exhibitorGroup->setGroupName($groupName);

            $entityManager->persist($exhibitorGroup);
            $entityManager->flush();

            // Rediriger vers la création avec l'ID de l'utilisateur
            return $this->redirectToRoute('exhibitorGroup_create', ['id' => $exhibitorGroup->getId()]);
        }

        // Afficher le formulaire de vérification de l'email
        return $this->render('exhibitorGroup/pre-create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/creation/{id}', name: 'create')]
    public function create(Request $request, int $id, UserPasswordHasherInterface $passwordHasher): Response
    {
        $exhibitorGroup = $this->exhibitorGroup;
        $entityManager = $this->entityManager;

        $exhibitor = $userRepository->find($id);

        // Création du formulaire de création
        $form = $this->createForm(UserType::class, $exhibitor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($exhibitor, $exhibitor->getPassword());
            $exhibitor->setPassword($hashedPassword);

            $entityManager->persist($exhibitor);
            $entityManager->flush();

            $this->addFlash('success', 'Exposant créé avec succès.');
            return $this->redirectToRoute('exhibitor_index');
        }

        return $this->render('exhibitor/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/modification/{id}', name: 'edit')]
    public function edit(Request $request, User $exhibitor, UserPasswordHasherInterface $passwordHasher): Response
    {
        $entityManager = $this->entityManager;

        $form = $this->createForm(UserType::class, $exhibitor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe si modifié
            if ($form->get('password')->getData()) {
                $hashedPassword = $passwordHasher->hashPassword($exhibitor, $exhibitor->getPassword());
                $exhibitor->setPassword($hashedPassword);
            }

            // Sauvegarder les changements
            $entityManager->flush();

            $this->addFlash('success', 'Exposant modifié avec succès.');
            return $this->redirectToRoute('exhibitor_index');
        }

        return $this->render('exhibitor/edit.html.twig', [
            'form' => $form->createView(),
            'exhibitor' => $exhibitor,
        ]);
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(User $exhibitor): Response
    {
        $entityManager = $this->entityManager;
        $entityManager->remove($exhibitor);
        $entityManager->flush();

        $this->addFlash('success', 'Exposant supprimé avec succès.');
        return $this->redirectToRoute('exhibitor_index');
    }
}