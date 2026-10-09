<?php

namespace App\Controller;

use App\Model\Contato,
    App\Model\Pessoa,
    Exception;

/**
 * Controller para gerenciamento dos contatos das pessoas cadastradas
 * @package Controller
 * @author Ruan Pereira -> ruanpdev@outlook.com
 * @since 08/10/2026
 */
class ControllerContato extends Controller {

    /**
     * Retorna todos os contatos de uma pessoa em JSON
     * @param int $iPessoaId
     */
    public function consultarPorPessoa(int $iPessoaId) {
        header('Content-Type: application/json; charset=utf-8');
        $oPessoa = $this->EntityManager->find(Pessoa::class, $iPessoaId);
        if (!$oPessoa) {
            http_response_code(404);
            echo json_encode(['erro' => 'Pessoa não encontrada.']);
            exit;
        }

        $aContatos = $oPessoa->getContatos();
        $aDadosJson = [];

        foreach ($aContatos as $oContato) {
            $aDadosJson[] = [
                'id' => $oContato->getId(),
                'tipo' => $oContato->getTipo(),
                'descricao' => $oContato->getDescricao()
            ];
        }
        echo json_encode($aDadosJson);
    }

    /**
     * Insere um novo contato e relaciona-o a pessoa
     */
    public function cadastrar() {
        header('Content-Type: application/json; charset=utf-8');
        $oDados = json_decode(file_get_contents('php://input'), true);
        $iPessoaId = $oDados['pessoa_id'] ?? null;
        $iTipo = $oDados['tipo'] ?? null;
        $sDescricao = $oDados['descricao'] ?? null;

        if (!$iPessoaId || !$iTipo || empty($sDescricao)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados incompletos para o contato.']);
            exit;
        }

        $oPessoa = $this->EntityManager->find(Pessoa::class, (int)$iPessoaId);
        if (!$oPessoa) {
            http_response_code(404);
            echo json_encode(['erro' => 'Pessoa não encontrada.']);
            exit;
        }

        try {
            $oContato = new Contato();
            $oContato->setTipo($iTipo);
            $oContato->setDescricao($sDescricao);
            $oContato->setPessoa($oPessoa);
            $this->EntityManager->persist($oContato);
            $this->EntityManager->flush();

            echo json_encode(['sucesso' => true, 'mensagem' => 'Contato adicionado!']);
        } catch (Exception $oErro) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao salvar contato no banco.']);
        }
    }

    /**
     * Exclui o contato informado
     * @param int $iId
     */
    public function excluir(int $iId) {
        header('Content-Type: application/json; charset=utf-8');
        $oContato = $this->EntityManager->find(Contato::class, $iId);
        if (!$oContato) {
            http_response_code(404);
            echo json_encode(['erro' => 'Contato não encontrado.']);
            exit;
        }

        try {
            $this->EntityManager->remove($oContato);
            $this->EntityManager->flush();
            echo json_encode(['sucesso' => true, 'mensagem' => 'Contato excluído!']);
        } catch (Exception $oErro) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao excluir contato.']);
        }
    }
}