<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;

/**
 * Controller base para o processamento de dados
 * @package Controller
 * @author Ruan Pereira -> ruanpdev@outlook.com
 * @since 08/10/2026
 */
abstract class Controller {

    /** 
     * @var EntityManager $EntityManager 
     */
    protected $EntityManager;

    /**
     * @param EntityManager $oEntityManager 
     */
    public function __construct($oEntityManager) {
        $this->EntityManager = $oEntityManager;
    }

    /**
     * Executa a consulta
     */
    public function consultar(){}

    /**
     * Executa o cadastro
     */
    public function cadastrar(){}

    /**
     * Executa a alteração
     */
    public function alterar(){}

    /**
     * Executa a exclusão
     * @param int $iId
     */
    public function excluir(int $iId){}
}