<?php

namespace App\Controller;

use App\Entity\Reservations;
use DateTime;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\ReservationsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private Security $security;
    private EntityManagerInterface $em;
    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }
    #[Route('/client/{user}', name: 'app_client')]
    #[IsGranted('ROLE_USER')]
    public function client(EntityManagerInterface $entityManager, User $user): Response
    {
        $repository = $entityManager->getRepository(Reservations::class);
        $user = $user->getId();
        $reservations = $repository->createQueryBuilder('u')
        ->andWhere('u.booker LIKE :booker')
        ->andWhere('u.is_confirmed = :true')
        ->setParameter('booker', $user)
        ->setParameter('true', 1)
        ->getQuery()
        ->getResult();

        return $this->render('user/client.html.twig', [        
            'reservations' => $reservations
        ]);
    }

    #[Route('/photographer/{user}', name: 'app_photographer')]
    #[IsGranted('ROLE_PHOTOGRAPHER')]
    public function photographer(User $user, ReservationsRepository $reservationRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Reservations::class);
        $user = $user->getId();
        $reservationsAccepted = $repository->createQueryBuilder('u')
        ->andWhere('u.photographer_id LIKE :photographer')
        ->andWhere('u.is_confirmed = :true')
        ->setParameter('photographer', $user)
        ->setParameter('true', 1)
        ->getQuery()
        ->getResult();

        $reservationsWaited = $repository->createQueryBuilder('u')
        ->andWhere('u.photographer_id LIKE :photographer')
        ->andWhere('u.is_confirmed = :true')
        ->setParameter('photographer', $user)
        ->setParameter('true', 0)
        ->getQuery()
        ->getResult();
        return $this->render('user/photographer.html.twig', [        
            'reservationsAccepted' => $reservationsAccepted,
            'reservationsWaited' => $reservationsWaited
        ]);
    }

    #[Route('/accept/{id}', name: 'app_accept')]
    #[IsGranted('ROLE_PHOTOGRAPHER')]
    public function accept(int $id, Reservations $reservation): Response
    {
        $reservation->setIsConfirmed(1);
        return $this->redirectToRoute('app_foto');
    }
    
    #[Route('/decline/{id}', name: 'app_decline')]
    #[IsGranted('ROLE_PHOTOGRAPHER')]
    public function decline(int $id, Reservations $reservation): Response
    {
        $reservation->setIsConfirmed(1);
        return $this->redirectToRoute('app_foto');
    }

    #[Route('/user/{user}/role', name: 'app_user_role')]
    #[IsGranted('ROLE_ADMIN')]
    public function editRoles(User $user, Request $request, UserRepository $users): Response 
    {
        
        $form = $this->createForm(UserType::class, $user);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
           $user = $form->getData();
           $currentUser = $this->security->getUser();
           $users -> save($user, true);
           $this -> addFlash('success', 'User role have been updated');

           
           return $this->redirectToRoute('app_user');
        }
        return $this->render(
            'user/edit_user.html.twig', [
                'roleForm' => $form,
                'user' => $user
            ]);

    }

    #[Route('/user', name: 'app_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository $users, ): Response
    {
        
        return $this->render('user/user.html.twig', [
            'users' => $users->findAll(),
            
        ]);
    }
}