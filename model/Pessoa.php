<?php

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection,
    Doctrine\Common\Collections\Collection,
    Doctrine\ORM\Mapping as ORM;

/**
 * Modelo de dados da pessoa
 * @package Model
 * @author Ruan Pereira -> ruanpdev@outlook.com
 * @since 07/10/2026
 */
#[ORM\Entity]
#[ORM\Table(name: 'tbpessoa')]
class Pessoa {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $nome;

    #[ORM\Column(type: 'string', length: 11, unique: true)]
    private string $cpf;

    #[ORM\OneToMany(targetEntity: Contato::class, mappedBy: 'pessoa', cascade: ['remove'])]
    private Collection $contatos;

    public function __construct() {
        $this->contatos = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
    }

    public function setId($iId) {
        $this->id = $iId;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setNome($sNome) {
        $this->nome = $sNome;
    }

    public function getCpf() {
        return $this->cpf;
    }

    public function setCpf($sCpf) {
        $this->cpf = $sCpf;
    }

    public function getContatos(): Collection { return $this->contatos; }
}