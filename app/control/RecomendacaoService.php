<?php

use Adianti\Control\TPage;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Database\TTransaction;

class RecomendacaoService extends TPage
{
    public function onRecomendar($param)
    {
        try {
            if (empty($param['id'])) {
                new TMessage('error', 'ID da venda não informado.');
                return;
            }

            $venda_id = (int) $param['id'];

            // busca o cliente da venda
            TTransaction::open('vendas');
            $conn = TTransaction::get();
            $result = $conn->query("
                SELECT v.cliente_id, c.nome 
                FROM venda v
                JOIN cliente c ON c.id = v.cliente_id
                WHERE v.id = {$venda_id}
                LIMIT 1
            ");
            $venda = $result->fetchObject();
            TTransaction::close();

            if (!$venda) {
                new TMessage('error', 'Venda não encontrada.');
                return;
            }

            $cliente_id = (int) $venda->cliente_id;

            $python = 'python';
            $script = '"C:\\xampp\\htdocs\\TCC\\app\\python\\recomendacao.py"';

            // manda o cliente_id pro script gerar as recomendações
            $command = "{$python} {$script} {$cliente_id} 2>&1";

            $output = shell_exec($command);
            $output = trim($output);

            if (!$output) {
                throw new Exception('Não foi possível executar o script Python. Verifique se o Python está no PATH e o script existe.');
            }

            // decodifica o JSON q venho do Python
            $dados = json_decode($output, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception(
                    'Erro ao decodificar JSON: ' . json_last_error_msg() .
                        '<br><br><b>Saída bruta do Python:</b><br><pre>' . htmlspecialchars($output) . '</pre>'
                );
            }

            if (empty($dados)) {
                new TMessage('info', 'Nenhuma recomendação disponível para este cliente.');
                return;
            }

            $lista = '';
            foreach ($dados as $rec) {
                if (!is_array($rec)) {
                    $lista .= "<br><i> Retorno inesperado: " . htmlspecialchars(json_encode($rec)) . "</i><br>";
                    continue;
                }

                // transforma a probabilidade para porcentagem
                $prob = number_format($rec['probabilidade_compra'] * 100, 1, ',', '.');
                $lista .= "• {$rec['produto_nome']} ({$prob}% de chance)<br>";
            }

            $action = new TAction(['VendasList', 'onReload']);
            $cliente_nome = htmlspecialchars($venda->nome);

            new TMessage(
                'info',
                "Produtos recomendados para o cliente <b>{$cliente_nome} #{$cliente_id}</b>:<br><br>{$lista}",
                $action
            );
        } catch (Exception $e) {
            new TMessage('error', 'Erro ao gerar recomendações: ' . $e->getMessage());
        }
    }
}
