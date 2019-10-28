<?php

namespace App\Controller;

use App\Helper\EntityFactory;
use App\Helper\RequestDataExtractor;
use App\Helper\ResponseFactory;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    use RequestDataExtractor;

    /**
     * @var ObjectRepository
     */
    protected $repository;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var EntityFactory
     */
    protected $factory;

    /**
     * BaseController constructor.
     * @param ObjectRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param EntityFactory $factory
     */
    public function __construct(
        ObjectRepository $repository,
        EntityManagerInterface $entityManager,
        EntityFactory $factory
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->factory = $factory;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function insert(Request $request): Response
    {
        $requestBody = $request->getContent();

        $entity = $this->factory->createEntity($requestBody);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return new JsonResponse($entity);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function findAll(Request $request): Response
    {
        $ordering = $this->getSort($request);
        $filter = $this->getFilter($request);
        $itens = $this->getItens($request);
        $page = $this->getPage($request);

        $entityList = $this->repository->findBy(
            $filter,
            $ordering,
            $itens,
            ($page - 1) * $itens
        );

        $status = empty($entityList) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;

        $responseFactory = new ResponseFactory(
            true,
            $entityList,
            $status,
            $page,
            $itens
        );

        return $responseFactory->getResponse();
    }

    /**
     * @param int $id
     * @return Response
     */
    public function findOne(int $id): Response
    {
        $entity = $this->repository->find($id);

        $status = is_null($entity) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;

        $responseFactory = new ResponseFactory(
            true,
            $entity,
            $status
        );

        return $responseFactory->getResponse();
    }

    /**
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $entity = $this->repository->find($id);

        if (is_null($entity)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function edit(Request $request, int $id): Response
    {
        $requestBody = $request->getContent();
        $entityReceived = $this->factory->createEntity($requestBody);

        try {
            $entityDataBase = $this->refreshEntity($id, $entityReceived);
            $this->entityManager->flush();

            $responseFactory = new ResponseFactory(
                true,
                $entityDataBase,
                Response::HTTP_OK
            );

            return $responseFactory->getResponse();
        } catch (\InvalidArgumentException $ex) {
            $responseFactory = new ResponseFactory(
                false,
                "Recurso não encontrado",
                Response::HTTP_NOT_FOUND
            );
            return $responseFactory->getResponse();
        }
    }

    /**
     * @param int $id
     * @param $entityReceived
     * @return mixed
     */
    abstract public function refreshEntity(int $id, $entityReceived);
}
