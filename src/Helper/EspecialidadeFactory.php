<?php


namespace App\Helper;


use App\Entity\Especialidade;

class EspecialidadeFactory implements EntityFactory
{
    /**
     * @param string $json
     * @return Especialidade
     * @throws EntityFactoryException
     */
    public function createEntity(string $json): Especialidade
    {
        $dataJson = json_decode($json);

        if (!property_exists($dataJson, 'descricao')) {
            throw new EntityFactoryException('Epecialidade precisa de descrição');
        }

        $especialidade = new Especialidade();
        $especialidade->setDescricao($dataJson->descricao);

        return $especialidade;
    }
}