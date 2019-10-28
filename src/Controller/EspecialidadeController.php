<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Helper\EspecialidadeFactory;
use App\Repository\EspecialidadeRepository;
use Doctrine\ORM\EntityManagerInterface;

class EspecialidadeController extends BaseController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        EspecialidadeRepository $repository,
        EspecialidadeFactory $factory
    ) {
        parent::__construct($repository, $entityManager, $factory);
    }

    /**
     * @param int $id
     * @param Especialidade $entityReceived
     * @return mixed|object|null
     */
    public function refreshEntity(int $id, $entityReceived)
    {
        $entityDataBase = $this->repository->find($id);

        if (is_null($entityDataBase)) {
            throw new \InvalidArgumentException();
        }

        $entityDataBase
            ->setDescricao($entityReceived->getDescricao());

        return $entityDataBase;
    }
}
