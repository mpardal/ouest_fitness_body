<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EmailVerificationType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/volunteer', name: 'volunteer_')]
//#[IsGranted('ROLE_VOLUNTEER')]
class VolunteerController extends AbstractController
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

        $volunteers = $userRepository->findByRole('ROLE_VOLUNTEER');

        return $this->render('volunteer/index.html.twig', [
            'volunteers' => $volunteers,
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
                return $this->redirectToRoute('volunteer_pre-create');
            }

            // Créer un utilisateur temporaire
            $volunteer = new User();
            $volunteer->setEmail($email);
            $volunteer->setRoles(['ROLE_VOLUNTEER']);

            // Persister l'utilisateur temporaire dans la base de données (sans mot de passe ou rôle pour le moment)
            $entityManager->persist($volunteer);
            $entityManager->flush();

            // Rediriger vers la création avec l'ID de l'utilisateur
            return $this->redirectToRoute('volunteer_create', ['id' => $volunteer->getId()]);
        }

        // Afficher le formulaire de vérification de l'email
        return $this->render('volunteer/pre-create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/creation/{id}', name: 'create')]
    public function create(Request $request, int $id, UserPasswordHasherInterface $passwordHasher): Response
    {
        $userRepository = $this->userRepository;
        $entityManager = $this->entityManager;

        $volunteer = $userRepository->find($id);

        // Création du formulaire de création
        $form = $this->createForm(UserType::class, $volunteer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($volunteer, $volunteer->getPassword());
            $volunteer->setPassword($hashedPassword);

            $entityManager->persist($volunteer);
            $entityManager->flush();

            $this->addFlash('success', 'Bénévole créé avec succès.');
            return $this->redirectToRoute('volunteer_index');
        }

        return $this->render('volunteer/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/modification/{id}', name: 'edit')]
    public function edit(Request $request, User $volunteer, UserPasswordHasherInterface $passwordHasher): Response
    {
        $entityManager = $this->entityManager;

        $form = $this->createForm(UserType::class, $volunteer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe si modifié
            if ($form->get('password')->getData()) {
                $hashedPassword = $passwordHasher->hashPassword($volunteer, $volunteer->getPassword());
                $volunteer->setPassword($hashedPassword);
            }

            // Sauvegarder les changements
            $entityManager->flush();

            $this->addFlash('success', 'Bénévole modifié avec succès.');
            return $this->redirectToRoute('volunteer_index');
        }

        return $this->render('volunteer/edit.html.twig', [
            'form' => $form->createView(),
            'volunteer' => $volunteer,
        ]);
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(User $volunteer): Response
    {
        $entityManager = $this->entityManager;
        $entityManager->remove($volunteer);
        $entityManager->flush();

        $this->addFlash('success', 'Bénévole supprimé avec succès.');
        return $this->redirectToRoute('volunteer_index');
    }
}