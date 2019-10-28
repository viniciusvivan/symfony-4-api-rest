<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
use App\Repository\MedicoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MedicoController extends BaseController
{
    public function __construct(
        EntityManagerInterface $entityManagernager,
        MedicoFactory $factory,
        MedicoRepository $medicoRepository
    ) {
        parent::__construct($medicoRepository, $entityManagernager, $factory);
    }

    /**
     * @param int $especialidadeId
     * @return Response
     */
    public function findByEspecialidade(int $especialidadeId): Response
    {
        $medicos = $this->repository->findBy([
            'especialidade' => $especialidadeId
        ]);

        return new JsonResponse($medicos);
    }


    /**
     * @param $id
     * @param Medico $entityReceived
     * @return mixed|object|null
     */
    public function refreshEntity(int $id, $entityReceived)
    {
        $entityDataBase = $this->repository->find($id);
        if (is_null($entityDataBase)) {
            throw new \InvalidArgumentException();
        }

        $entityDataBase
            ->setCrm($entityReceived->getCrm())
            ->setNome($entityReceived->getNome())
            ->setEspecialidade($entityReceived->getEspecialidade());

        return $entityDataBase;
    }
}
