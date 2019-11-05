<?php

namespace App\Controller;

use App\Helper\EntityFactory;
use App\Helper\RequestDataExtractor;
use App\Helper\ResponseFactory;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
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
     * @var CacheItemPoolInterface
     */
    private $cache;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * BaseController constructor.
     * @param ObjectRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param EntityFactory $factory
     * @param CacheItemPoolInterface $cache
     * @param LoggerInterface $logger
     */
    public function __construct(
        ObjectRepository $repository,
        EntityManagerInterface $entityManager,
        EntityFactory $factory,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->factory = $factory;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function insert(Request $request): Response
    {
        $requestBody = $request->getContent();

        $entity = $this->factory->createEntity($requestBody);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $cacheItem = $this->cache->getItem(
            $this->cachePrefix() . $entity->getId()
        );

        $cacheItem->set($entity);
        $this->cache->save($cacheItem);

        $this->logger
            ->notice(
                'Novo registro de {entidade} adicionado com id: {id}.',
                [
                    'entidade' => get_class($entity),
                    'id' => $entity->getId()
                ]
            );

        return new JsonResponse($entity, Response::HTTP_CREATED);
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
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function findOne(int $id): Response
    {
        $entity = $this->cache->hasItem($this->cachePrefix() . $id)
            ? $this->cache->getItem($this->cachePrefix() . $id)->get()
            : $this->repository->find($id);

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
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function delete(int $id): Response
    {
        $entity = $this->repository->find($id);

        if (is_null($entity)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        $this->cache->deleteItem($this->cachePrefix() . $id);

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function edit(Request $request, int $id): Response
    {
        $requestBody = $request->getContent();
        $entityReceived = $this->factory->createEntity($requestBody);

        try {
            $entityDataBase = $this->refreshEntity($id, $entityReceived);
            $this->entityManager->flush();

            $cacheItem = $this->cache->getItem($this->cachePrefix() . $id);
            $cacheItem->set($entityDataBase);
            $this->cache->save($cacheItem);

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
    abstract public function cachePrefix(): string ;
}
