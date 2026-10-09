<?php

namespace App\Controller;

use App\Model\Pessoa,
    Exception;

/**
 * Controller para gerenciamento das pessoas cadastradas
 * @package Controller
 * @author Ruan Pereira -> ruanpdev@outlook.com
 * @since 07/10/2026
 */
class ControllerPessoa extends Controller {

    /**
     * Retorna todas as pessoas em JSON
     */
    public function consultar() {
        $oPessoaRepository = $this->EntityManager->getRepository(Pessoa::class);
        $bIsJsonRequest = (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));
        
        if ($bIsJsonRequest) {
            header('Content-Type: application/json; charset=utf-8');
            $oPessoaRepository = $this->EntityManager->getRepository(Pessoa::class);
            $sNomeBuscado = filter_input(INPUT_GET, 'nome', FILTER_UNSAFE_RAW);

            if ($sNomeBuscado) {
                $oQueryBuilder = $oPessoaRepository->createQueryBuilder('p');
                $aListaPessoas = $oQueryBuilder
                    ->where('p.nome LIKE :nome')
                    ->setParameter('nome', '%' . $sNomeBuscado . '%')
                    ->getQuery()
                    ->getResult();
            } else {
                $aListaPessoas = $oPessoaRepository->findAll();
            }

            $oDadosJson = [];
            foreach ($aListaPessoas as $oPessoa) {
                $oDadosJson[] = [
                    'id' => $oPessoa->getId(),
                    'nome' => $oPessoa->getNome(),
                    'cpf' => $this->addPontuacaoCpf($oPessoa->getCpf())
                ];
            }
            echo json_encode($oDadosJson);
            exit;
        }

        $sCaminhoView = __DIR__ . '/../view/ViewConsultaPessoa.html';
        
        if (file_exists($sCaminhoView)) {
            require_once $sCaminhoView;
        } else {
            http_response_code(500);
            echo "<h1>Erro Interno: Arquivo view/ViewConsultaPessoa.html não encontrado.</h1>";
        }
    }

    /**
     * Faz a inserção de uma nova pessoa
     */
    public function cadastrar() {
        $sMethod = $_SERVER['REQUEST_METHOD'];

        if ($sMethod === 'POST') {
            header('Content-Type: application/json; charset=utf-8');
            $sJsonRecebido = file_get_contents('php://input');
            $aDados = json_decode($sJsonRecebido, true);
            $sNome = $aDados['nome'] ?? null;
            $sCpf = $aDados['cpf'] ?? null;

            if (empty($sNome) || empty($sCpf)) {
                http_response_code(400);
                echo json_encode(['erro' => 'Nome e CPF são campos obrigatórios.']);
                exit;
            }

            $sCpfLimpo = preg_replace('/[^0-9]/', '', $sCpf);
            try {
                $oPessoa = new Pessoa();
                $oPessoa->setNome($sNome);
                $oPessoa->setCpf($sCpfLimpo);
                $this->EntityManager->persist($oPessoa);
                $this->EntityManager->flush();

                http_response_code(201);
                echo json_encode([
                    'sucesso' => true, 
                    'mensagem' => 'Pessoa cadastrada com sucesso!',
                    'id' => $oPessoa->getId()
                ]);
            } catch (Exception $oErro) {
                http_response_code(500);
                echo json_encode(['erro' => 'Erro ao salvar no banco. Verifique se o CPF já está cadastrado.']);
            }
            exit;
        }
        header('Location: /pessoas');
    }

    /**
     * Retorna uma pessoa de acordo com o id informado
     * @param int $iId
     */
    public function consultarPorId(int $iId) {
        header('Content-Type: application/json; charset=utf-8');
        $oPessoa = $this->EntityManager->find(Pessoa::class, $iId);
        if (!$oPessoa) {
            http_response_code(404);
            echo json_encode(['erro' => 'Pessoa não encontrada.']);
        }
        echo json_encode([
            'id' => $oPessoa->getId(),
            'nome' => $oPessoa->getNome(),
            'cpf' => $oPessoa->getCpf()
        ]);
    }

    /**
     * Registra as alterações de dados da pessoa
     */
    public function alterar() {
        header('Content-Type: application/json; charset=utf-8');
        $sJsonRecebido = file_get_contents('php://input');
        $aDados = json_decode($sJsonRecebido, true);
        $iId = $aDados['id'] ?? null;
        $sNome = $aDados['nome'] ?? null;
        $sCpf = $aDados['cpf'] ?? null;

        if (!$iId || empty($sNome) || empty($sCpf)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados insuficientes para alteração.']);
            exit;
        }

        try {
            $oPessoa = $this->EntityManager->find(Pessoa::class, (int)$iId);

            if (!$oPessoa) {
                http_response_code(404);
                echo json_encode(['erro' => 'Pessoa não encontrada para alteração.']);
                exit;
            }
            $oPessoa->setNome($sNome);
            $oPessoa->setCpf(preg_replace('/[^0-9]/', '', $sCpf));
            $this->EntityManager->flush();

            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Pessoa alterada com sucesso!'
            ]);
        } catch (Exception $oErro) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar no banco. Verifique se o CPF já existe.']);
        }
    }

    /**
     * Executa a exclusão de uma pessoa de acordo com o id informado
     * @param int $iId
     */
    public function excluir(int $iId) {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $oPessoa = $this->EntityManager->find(Pessoa::class, $iId);
            if (!$oPessoa) {
                http_response_code(404);
                echo json_encode(['erro' => 'Pessoa não encontrada.']);
                exit;
            }

            $this->EntityManager->remove($oPessoa);
            $this->EntityManager->flush();

            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Pessoa e seus contatos excluídos com sucesso!'
            ]);
        } catch (Exception $oErro) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro interno ao tentar excluir a oPessoa.']);
        }
    }

    /**
     * Adiciona a pontuação padrão do CPF a string informada
     * @param string $sCpf
     * @return string
     */
    public function addPontuacaoCpf($sCpf) {
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", str_pad(preg_replace("/\D/", "", $sCpf), 11, '0', STR_PAD_LEFT));
    }
}