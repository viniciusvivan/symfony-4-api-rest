<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Debug\Tests\testHeader;

class MedicoController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManagernager;

    /**
     * @var MedicoFactory
     */
    private $medicoFactory;

    //É o Symfony quem instaica o controler, ele se encarrega de passar o entity manager
    public function __construct(
        EntityManagerInterface $entityManagernager,
        MedicoFactory $medicoFactory
    ) {
        $this->entityManagernager = $entityManagernager;
        $this->medicoFactory = $medicoFactory;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/medico", methods={"POST"})
     */
    public function insert(Request $request): Response
    {
        $requestBody = $request->getContent();

        $medico = $this->medicoFactory->criarMedico($requestBody);

        $this->entityManagernager->persist($medico);
        $this->entityManagernager->flush();

        return new JsonResponse($medico);
    }

    /**
     * @return Response
     * @Route("medico", methods={"GET"})
     */
    public function fildAll(): Response
    {
        $medicoRepository = $this
            ->getDoctrine()
            ->getRepository(Medico::class);

        $medicoList = $medicoRepository->findAll();

        return new JsonResponse($medicoList);
    }

    /**
     * @param $id
     * @return Response
     * @Route("medico/{id}", methods={"GET"})
     */
    public function findOne(int $id): Response
    {
        $medico = $this->findMedico($id);

        $returnCode = is_null($medico) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;

        return new JsonResponse($medico, $returnCode);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @Route("medico/{id}", methods={"PUT"})
     */
    public function edit(Request $request, int $id): Response
    {
        $requestBody = $request->getContent();

        $medicoRecebido = $this->medicoFactory->criarMedico($requestBody);

        $medicoExistente = $this->findMedico($id);

        if (is_null($medicoExistente)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $medicoExistente
            ->setCrm($medicoRecebido->getCrm())
            ->setNome($medicoRecebido->getNome());

        $this->entityManagernager->flush();

        return new JsonResponse($medicoExistente, Response::HTTP_OK);
    }

    /**
     * @param int $id
     * @return object|null
     */
    public function findMedico(int $id)
    {
        $medico = $this
            ->getDoctrine()
            ->getRepository(Medico::class);

       return $medico->find($id);
    }

    /**
     * @param $id
     * @return Response
     * @Route("medico/{id}", methods={"DELETE"})
     */
    public function delete(int $id): Response
    {
        $medico = $this->findMedico($id);

        if (is_null($medico)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $this->entityManagernager->remove($medico);
        $this->entityManagernager->flush();

        return new JsonResponse("", Response::HTTP_NO_CONTENT);
    }
}