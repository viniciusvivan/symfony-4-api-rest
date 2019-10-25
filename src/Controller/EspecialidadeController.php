<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Repository\EspecialidadeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EspecialidadesController extends BaseController
{

    public function __construct(
        EntityManagerInterface $entityManager,
        EspecialidadeRepository $repository
    ) {
        parent::__construct($repository, $entityManager);
    }

    /**
     * @Route("/especialidade", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $dataRequest = $request->getContent();
        $dataJson = json_decode($dataRequest);

        $especialidade = new Especialidade();
        $especialidade->setDescricao($dataJson->descricao);

        $this->entityManager->persist($especialidade);
        $this->entityManager->flush();

        return new JsonResponse($especialidade, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response
     * @Route("/especialidade/{id}", methods={"PUT"})
     */
    public function Edit(Request $request, int $id): Response
    {
        $dataRequest = $request->getContent();
        $dataJson = json_decode($dataRequest);

        $especialidade = $this->repository->find($id);

        if (is_null($especialidade)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $especialidade->setDescricao($dataJson->descricao);

        $this->entityManager->flush();

        return new JsonResponse($especialidade, Response::HTTP_OK);
    }
}
