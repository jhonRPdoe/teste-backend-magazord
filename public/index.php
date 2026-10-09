<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\ControllerContato,
    App\Controller\ControllerPessoa,
    Doctrine\ORM\EntityManager;

/** @var EntityManager $oEntityManager */
$oEntityManager = require_once __DIR__ . '/../bootstrap.php';
$sUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$sMethod = $_SERVER['REQUEST_METHOD'];
$oController = (str_contains($sUrl, 'pessoas')) ? new ControllerPessoa($oEntityManager) : new ControllerContato($oEntityManager);

switch (true) {
    case ($sUrl === '/' || $sUrl === '/pessoas'):
        $oController->consultar();
        break;
    case ($sUrl === '/pessoas/cadastrar'):
        if ($sMethod === 'POST') {
            $oController->cadastrar();
        }
        break;
    case (preg_match('/^\/pessoas\/buscar\/(\d+)$/', $sUrl, $aMatches)):
        $iId = (int) $aMatches[1];
        if ($sMethod === 'GET') {
            $oController->consultarPorId($iId);
        }
        break;
    case ($sUrl === '/pessoas/alterar'):
        if ($sMethod === 'POST') {
            $oController->alterar();
        }
        break;
    case (preg_match('/^\/pessoas\/excluir\/(\d+)$/', $sUrl, $aMatches)):
        if ($sMethod === 'DELETE') {
            $oController->excluir($aMatches[1]);
        }
        break;
    case (preg_match('/^\/contatos\/pessoa\/(\d+)$/', $sUrl, $aMatches)):
        if ($sMethod === 'GET') {
            $oController->consultarPorPessoa((int)$aMatches[1]);
        }
        break;
    case ($sUrl === '/contatos/cadastrar'):
        if ($sMethod === 'POST') {
            $oController->cadastrar();
        }
        break;
    case (preg_match('/^\/contatos\/excluir\/(\d+)$/', $sUrl, $aMatches)):
        if ($sMethod === 'DELETE') {
            $oController->excluir((int)$aMatches[1]);
        }
        break;
    default:
        http_response_code(404);
        echo "<h1>Erro 404 - Página Não Encontrada</h1>";
        break;
}
exit;