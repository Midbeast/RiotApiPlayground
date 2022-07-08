<?php

namespace App\Controller;

use App\Form\Search\NameType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\RiotApi;


class HomepageController extends AbstractController
{
    private RiotApi $riotApi;

    public function __construct(RiotApi $riotApi)
    {
        $this->riotApi = $riotApi;
    }

    #[Route('/', name: 'homepage')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(NameType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $userName = $data['username'];
            $data = $this->riotApi->fetchRiotApiInformation($userName);
            dd(json_decode($data->getContent()));
            return $this->renderForm('homepage/index.html.twig', [
                'data' => $data ?? null,
            ]);
        }
        return $this->renderForm('homepage/index.html.twig', [
            'form' => $form,
        ]);
    }
}
