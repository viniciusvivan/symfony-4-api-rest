<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Repository\EspecialidadeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EspecialidadesController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EspecialidadeRepository
     */
    private $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        EspecialidadeRepository $repository
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
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
     * @return Response
     *
     * @Route("/especialidade", methods={"GET"})
     */
    public function fildAll(): Response
    {
        $especialidadeList = $this->repository->findAll();

        if (is_null($especialidadeList)) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse($especialidadeList, Response::HTTP_OK);
    }

    /**
     * @return Response
     *
     * @Route("/especialidade/{id}", methods={"GET"})
     */
    public function fildOne(int $id): Response
    {
        $especialidade = $this->repository->find($id);

        if (is_null($especialidade)) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

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

    /**
     * @param int $id
     * @return Response
     *
     * @Route("/especialidade/{id}", methods={"DELETE"})
     */
    public function remove(int $id): Response
    {
        $especialidade = $this->repository->find($id);

        if (is_null($especialidade)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($especialidade);
        $this->entityManager->flush();

        return new JsonResponse($especialidade, Response::HTTP_NO_CONTENT);
    }
}
