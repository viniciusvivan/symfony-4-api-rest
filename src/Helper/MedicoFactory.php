<?php

namespace App\Helper;

use App\Entity\Medico;
use App\Repository\EspecialidadeRepository;

class MedicoFactory implements EntityFactory
{
    /**
     * @var EspecialidadeRepository
     */
    private $especialidadeRepository;

    public function __construct(EspecialidadeRepository $especialidadeRepository)
    {
        $this->especialidadeRepository = $especialidadeRepository;
    }

    /**
     * @param string $json
     * @return Medico
     * @throws EntityFactoryException
     */
    public function createEntity(string $json): Medico
    {
        $jsonData = json_decode($json);

        $this->checkAllProperties($jsonData);

        $especialidadeId = $jsonData->especialidadeId;
        $especialidade = $this->especialidadeRepository->find($especialidadeId);

        $medico = new Medico();
        $medico
            ->setCrm($jsonData->crm)
            ->setNome($jsonData->nome)
            ->setEspecialidade($especialidade);

        return $medico;
    }

    /* Guard Clauses */
    private function checkAllProperties(object $jsonData): void
    {
        if (!property_exists($jsonData, 'nome')) {
            throw new EntityFactoryException('Médico precisa de nome');
        }

        if (!property_exists($jsonData, 'crm')) {
            throw new EntityFactoryException('Médico precisa de CRM');
        }

        if (!property_exists($jsonData, 'especialidadeId')) {
            throw new EntityFactoryException('Médico precisa de especialidade');
        }
    }
}