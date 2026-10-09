<?php

namespace App\Tests;

use App\Controller\ControllerPessoa,
    PHPUnit\Framework\TestCase,
    Doctrine\ORM\EntityManager;

/**
 * Testes unitários para validar os métodos do ControllerPessoa
 * @package Tests
 * @author Ruan Pereira -> ruanpdev@outlook.com
 * @since 08/10/2026
 */
class ControllerPessoaTest extends TestCase {

    /**
     * Testa se o método de formatação de CPF adiciona os pontos e traços corretamente
     */
    public function testAddPontuacaoCpf() {
        $oEntityManagerMock = $this->createMock(EntityManager::class);
        $oController = new ControllerPessoa($oEntityManagerMock);
        $sCpfEntrada = "12345678901";
        $sCpfEsperado = "123.456.789-01";
        $this->assertEquals($sCpfEsperado, $oController->addPontuacaoCpf($sCpfEntrada));
    }

    /**
     * Testa se o método lida corretamente com CPFs que venham com caracteres aleatórios
     */
    public function testFormatarCpfIncorreto() {
        $oEntityManagerMock = $this->createMock(EntityManager::class);
        $oController = new ControllerPessoa($oEntityManagerMock);
        $sCpfIncorreto = "123.abc.456 78901";
        $sCpfEsperado = "123.456.789-01";
        $this->assertEquals($sCpfEsperado, $oController->addPontuacaoCpf($sCpfIncorreto));
    }
}