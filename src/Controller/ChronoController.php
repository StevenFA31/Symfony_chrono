<?php

namespace App\Controller;

use App\Entity\Chrono;
use App\Form\ChronoType;
use App\Repository\ChronoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/chrono')]
class ChronoController extends AbstractController
{
    #[Route('/', name: 'app_chrono_index', methods: ['GET'])]
    public function index(ChronoRepository $chronoRepository): Response
    {
        return $this->render('chrono/index.html.twig', [
            'chronos' => $chronoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_chrono_new', methods: ['GET', 'POST'])]
    public function new (Request $request, ChronoRepository $chronoRepository): Response
    {
        $chrono = new Chrono();
        $form = $this->createForm(ChronoType::class, $chrono);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chronoRepository->save($chrono, true);

            return $this->redirectToRoute('app_chrono_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('chrono/new.html.twig', [
            'chrono' => $chrono,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chrono_show', methods: ['GET'])]
    public function show(Chrono $chrono): Response
    {
        return $this->render('chrono/show.html.twig', [
            'chrono' => $chrono,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chrono_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chrono $chrono, ChronoRepository $chronoRepository): Response
    {
        $form = $this->createForm(ChronoType::class, $chrono);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $chronoRepository->save($chrono, true);

            return $this->redirectToRoute('app_chrono_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('chrono/edit.html.twig', [
            'chrono' => $chrono,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chrono_delete', methods: ['POST'])]
    public function delete(Request $request, Chrono $chrono, ChronoRepository $chronoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $chrono->getId(), $request->request->get('_token'))) {
            $chronoRepository->remove($chrono, true);
        }

        return $this->redirectToRoute('app_chrono_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/save/{start_date}/{stop_date}/{compteur}', name: 'app_chrono_save')]
    public function save(EntityManagerInterface $entityManager, $start_date, $stop_date, $compteur): Response
    {
        // Envoyer des donnée vers la base de données 

        $chrono = new Chrono();

        $dtstart = new \DateTime();
        $dtstart->setTimestamp($start_date / 1000);

        $dtstop = new \DateTime();
        $dtstop->setTimestamp($stop_date / 1000);

        $dttime = new \DateTime();
        $dttime->setTimestamp($compteur - 3600);

        $chrono->setStartTime($dtstart);
        $chrono->setEndTime($dtstop);
        $chrono->setDuration($dttime);


        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($chrono);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();


        // Renvoie mes données en JSON (dans la console)
        return new Response('Saved new chrono with id ' . $chrono->getId());
    }
}