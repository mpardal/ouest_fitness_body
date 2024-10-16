<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\EmailVerificationType;
use App\Repository\UserRepository;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin_')]
//#[IsGranted('ROLE_ADMIN')]  // Cette route est accessible uniquement aux administrateurs
class AdminController extends AbstractController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    // Injecter le UserRepository et l'EntityManagerInterface via le constructeur
    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $userRepository = $this->userRepository;

        $admins = $userRepository->findByRole('ROLE_ADMIN');

        return $this->render('admin/index.html.twig', [
            'admins' => $admins,
        ]);
    }

    #[Route('/pre-creation', name: 'pre-create')]
    public function preCreate(Request $request): Response
    {
        $userRepository = $this->userRepository;
        $entityManager = $this->entityManager;
        // Créer le formulaire pour la vérification de l'email
        $form = $this->createForm(EmailVerificationType::class);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et s'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'email à partir du formulaire
            $email = $form->get('email')->getData();

            // Vérifier si un utilisateur avec cet email existe déjà
            $existingUser = $userRepository->findOneBy(['email' => $email]);

            if ($existingUser) {
                // Ajouter un message flash si l'email existe déjà
                $this->addFlash('error', 'Un utilisateur avec cet email existe déjà.');
                return $this->redirectToRoute('admin_pre-create');
            }

            // Créer un utilisateur temporaire
            $admin = new User();
            $admin->setEmail($email);
            $admin->setRoles(['ROLE_ADMIN']);

            // Persister l'utilisateur temporaire dans la base de données (sans mot de passe ou rôle pour le moment)
            $entityManager->persist($admin);
            $entityManager->flush();

            // Rediriger vers la création avec l'ID de l'utilisateur
            return $this->redirectToRoute('admin_create', ['id' => $admin->getId()]);
        }

        // Afficher le formulaire de vérification de l'email
        return $this->render('admin/pre-create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/creation/{id}', name: 'create')]
    public function create(Request $request, int $id, UserPasswordHasherInterface $passwordHasher): Response
    {
        $userRepository = $this->userRepository;
        $entityManager = $this->entityManager;

        $admin = $userRepository->find($id);

        // Création du formulaire de création
        $form = $this->createForm(UserType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($admin, $admin->getPassword());
            $admin->setPassword($hashedPassword);

            // Persister l'administrateur
            $entityManager->persist($admin);
            $entityManager->flush();

            $this->addFlash('success', 'Administrateur créé avec succès.');
            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/modification/{id}', name: 'edit')]
    public function edit(Request $request, User $admin, UserPasswordHasherInterface $passwordHasher): Response
    {
        $entityManager = $this->entityManager;
        // Création du formulaire pour la modification de l'administrateur
        $form = $this->createForm(UserType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe si modifié
            if ($form->get('password')->getData()) {
                $hashedPassword = $passwordHasher->hashPassword($admin, $admin->getPassword());
                $admin->setPassword($hashedPassword);
            }

            // Sauvegarder les changements
            $entityManager->flush();

            $this->addFlash('success', 'Administrateur modifié avec succès.');
            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
            'admin' => $admin,
        ]);
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(User $admin): Response
    {
        $entityManager = $this->entityManager;
        $entityManager->remove($admin);
        $entityManager->flush();

        $this->addFlash('success', 'Administrateur supprimé avec succès.');
        return $this->redirectToRoute('admin_index');
    }
}