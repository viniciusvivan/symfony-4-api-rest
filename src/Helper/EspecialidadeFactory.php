<?php


namespace App\Helper;


use App\Entity\Especialidade;

class EspecialidadeFactory implements EntityFactory
{
    /**
     * @param string $json
     * @return Especialidade
     */
    public function createEntity(string $json): Especialidade
    {
        $dataJson = json_decode($json);

        $especialidade = new Especialidade();
        $especialidade->setDescricao($dataJson->descricao);

        return $especialidade;
    }
}