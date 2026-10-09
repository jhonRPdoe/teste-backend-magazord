<?php

namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Modelo de dados do contato da pessoa
 * @package Model
 * @author Ruan Pereira -> ruanpdev@outlook.com
 * @since 07/10/2026
 */
#[ORM\Entity]
#[ORM\Table(name: 'tbcontato')]
class Contato {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $tipo;

    #[ORM\Column(type: 'string', length: 255)]
    private string $descricao;

    #[ORM\ManyToOne(targetEntity: Pessoa::class, inversedBy: 'contatos')]
    #[ORM\JoinColumn(name: 'pessoa_id', referencedColumnName: 'id', nullable: false)]
    private Pessoa $pessoa;

    public function getId() {
        return $this->id;
    }

    public function setId($iId) {
        $this->id = $iId;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function setTipo($iTipo) {
        $this->tipo = $iTipo;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function setDescricao($sDescricao) {
        $this->descricao = $sDescricao;
    }

    public function getPessoa() {
        return (isset($this->pessoa)) ? $this->pessoa : new Pessoa();
    }

    public function setPessoa($oPessoa) {
        $this->pessoa = $oPessoa;
    }
}