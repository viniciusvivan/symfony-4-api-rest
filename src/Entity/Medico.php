<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Medico
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\EspecialidadeRepository")
 */
class Medico implements \JsonSerializable
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $crm;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $nome;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Especialidade")
     * @ORM\JoinColumn(nullable=false)
     */
    private $especialidade;

    /**
     * @return Especialidade|null
     */
    public function getEspecialidade(): ?Especialidade
    {
        return $this->especialidade;
    }

    /**
     * @param Especialidade|null $especialidade
     * @return Medico
     */
    public function setEspecialidade(?Especialidade $especialidade): self
    {
        $this->especialidade = $especialidade;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Medico
     */
    public function setId(int $id): ?Medico
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCrm(): ?string
    {
        return $this->crm;
    }

    /**
     * @param string $crm
     * @return Medico
     */
    public function setCrm(string $crm): Medico
    {
        $this->crm = $crm;
        return $this;
    }

    /**
     * @return string
     */
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     * @return Medico
     */
    public function setNome(string $nome): ?Medico
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'crm' => $this->getCrm(),
            'nome' => $this->getNome(),
            'especialidade' => $this->getEspecialidade()->getId(),
            '_links' => [
                [
                    'rel' => 'self',
                    'path' => '/medico/' . $this->getId()
                ],
                [
                    'rel' => 'especialidade',
                    'path' => '/especialidade/' . $this->getEspecialidade()->getId()
                ]
            ]
        ];
    }
}