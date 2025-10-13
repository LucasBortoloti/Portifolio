<?php

use Adianti\Control\TPage;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Template\THtmlRenderer;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TButton;
use Adianti\Database\TTransaction;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Core\AdiantiSession;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Container\TPanelGroup;

class GraficoVendas extends TPage
{
    protected $form;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_filtro_vendas');
        $this->form->setFormTitle('Resultado das Vendas');

        $data_inicio = new TDate('data_inicio');
        $data_inicio->setMask('dd/mm/yyyy');
        $data_inicio->setDatabaseMask('yyyy-mm-dd');
        $data_inicio->setSize('30%');

        $data_fim = new TDate('data_fim');
        $data_fim->setMask('dd/mm/yyyy');
        $data_fim->setDatabaseMask('yyyy-mm-dd');
        $data_fim->setSize('30%');

        $btn = new TButton('filtrar');
        $btn->setAction(new TAction([$this, 'onFilter']), 'Buscar');
        $btn->setImage('fa:search');

        $this->form->addFields(
            [new TLabel('Data (De):')],
            [$data_inicio],
            [new TLabel('Data (Até):')],
            [$data_fim]
        );

        $this->form->addAction('Buscar', new TAction([$this, 'onFilter']), 'fa:search');

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);

        parent::add($vbox);

        $this->onGeneratorGraficoMensal();
        $this->onGeneratorGraficoProdutos();
    }

    public function onFilter($param)
    {
        TSession::setValue('filtro_data_inicio', $param['data_inicio'] ?? null);
        TSession::setValue('filtro_data_fim', $param['data_fim'] ?? null);

        AdiantiCoreApplication::gotoPage(__CLASS__);
    }

    public function onGeneratorGraficoMensal()
    {
        $html = new THtmlRenderer('app/resources/google_column_chart.html');

        TTransaction::open('vendas');
        $conn = TTransaction::get();

        $where = '';
        if ($ini = TSession::getValue('filtro_data_inicio')) {
            $ini = TDate::date2us($ini);
            $where .= " AND date >= '{$ini}' ";
        }
        if ($fim = TSession::getValue('filtro_data_fim')) {
            $fim = TDate::date2us($fim);
            $where .= " AND date <= '{$fim}' ";
        }

        $sql = "SELECT DATE_FORMAT(date, '%m/%Y') as mes, SUM(total) as total
                FROM venda
                WHERE 1=1 {$where}
                GROUP BY mes
                ORDER BY mes";

        $result = $conn->query($sql);

        $dados = [];
        $dados[] = ['Mês/Ano', 'Total de Vendas'];
        foreach ($result as $row) {
            $dados[] = [(string) $row['mes'], (float) ($row['total'] ?? 0)];
        }

        if (count($dados) == 1) {
            $dados[] = ['Sem dados', 0];
        }

        $div = new TElement('div');
        $div->id = 'grafico_vendas_mes';
        $div->style = 'width:100%;height:500px;margin-bottom:40px;';
        $div->add($html);

        $html->enableSection('main', [
            'data'   => json_encode($dados),
            'width'  => '100%',
            'height' => '500px',
            'title'  => 'Vendas Mensais',
            'xtitle' => 'Mês/Ano',
            'ytitle' => 'Total (R$)'
        ]);

        TTransaction::close();
        parent::add($div);
    }

    public function onGeneratorGraficoProdutos()
    {
        $html2 = new THtmlRenderer('app/resources/google_pie_chart.html');

        TTransaction::open('vendas');
        $conn = TTransaction::get();

        $where = '';
        if ($ini = TSession::getValue('filtro_data_inicio')) {
            $ini = TDate::date2us($ini);
            $where .= " AND v.date >= '{$ini}' ";
        }
        if ($fim = TSession::getValue('filtro_data_fim')) {
            $fim = TDate::date2us($fim);
            $where .= " AND v.date <= '{$fim}' ";
        }

        $sql = "SELECT p.nome, SUM(vi.quantidade) as qtd
                FROM venda_item vi
                JOIN produto p ON p.id = vi.produto_id
                JOIN venda v   ON v.id = vi.venda_id
                WHERE 1=1 {$where}
                GROUP BY p.nome
                ORDER BY qtd DESC
                LIMIT 10";

        $result = $conn->query($sql);

        $dados = [];
        $dados[] = ['Produto', 'Quantidade Vendida'];

        if ($result->rowCount() > 0) {
            foreach ($result as $row) {
                $dados[] = [$row['nome'], (int)$row['qtd']];
            }
        } else {
            $dados[] = ['Sem dados', 0];
        }

        $div = new TElement('div');
        $div->id = 'grafico_produtos';
        $div->style = 'width:100%;height:500px;margin-top:40px;';
        $div->add($html2);

        $html2->enableSection('main', [
            'data'   => json_encode($dados),
            'width'  => '100%',
            'height' => '550px',
            'title'  => 'Top Produtos Mais Vendidos',
            'xtitle' => 'Produto',
            'ytitle' => 'Quantidade'
        ]);

        TTransaction::close();
        parent::add($div);
    }
}
