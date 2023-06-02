<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Reservations;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use App\Form\ReservationsFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ReservationsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class CalendarController extends AbstractController
{
    #[Route('/calendar/{user}', name: 'app_calendar')]
    public function index(Request $request, User $user, ReservationsRepository $reservations, Reservations $reservation, UserRepository $users): Response
    {
    $userObj = $users->findOneBy(['id'=>$user]);
    $userId = $userObj->getId();
    $userBooker = $this->getUser()->getId();
    $bookerEmail = $this->getUser()->getEmail();
    $form = $this->createForm(ReservationsFormType::class, $reservation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $reservation = $form->getData();
        
        $reservation->setBookerEmail($bookerEmail);
        $reservation->setBooker($userBooker);
        $reservation->setIsConfirmed(0);
        $reservation->setPhotographerId($userId);
        $userObj -> setReservations($reservation);
        $reservations->save($reservation, true);
        $users->save($userObj, true);
        
        $this -> addFlash('success', 'Termin wizyty został przesłany do fotografa');

        return $this->redirectToRoute('app_foto');
    }

        return $this->render('calendar/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/photographers', name: 'app_photographers')]
    public function photographers(EntityManagerInterface $entityManager, UserRepository $users): Response
    {
        $repository = $entityManager->getRepository(User::class);
        $users = $repository->createQueryBuilder('u')
        ->andWhere('u.roles LIKE :role')
        ->setParameter('role', '%"ROLE_PHOTOGRAPHER"%')
        ->getQuery()
        ->getResult();
        return $this->render('calendar/list.html.twig', [
            'users' => $users,
        ]);
    }
}
