<?php
error_reporting(E_ALL & ~E_DEPRECATED);

use Adianti\Control\TPage;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;
use Adianti\Widget\Container\TVBox;
use Adianti\Database\TTransaction;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Base\TElement;
use Adianti\Control\TWindow;

class RelatorioVendas extends TPage
{
    protected $form;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_relatorio_vendas');
        $this->form->setFormTitle('Relatório de Vendas por Cliente');

        $cliente_id   = new TDBUniqueSearch('cliente_id', 'vendas', 'Cliente', 'id', 'nome');
        $cliente_id->setMinLength(1);
        $cliente_id->setMask('{nome} ({id})');
        $cliente_id->setSize('50%');

        $data_inicio  = new TDate('data_inicio');
        $data_fim     = new TDate('data_fim');

        $data_inicio->setMask('dd/mm/yyyy');
        $data_fim->setMask('dd/mm/yyyy');
        $data_inicio->setDatabaseMask('yyyy-mm-dd');
        $data_fim->setDatabaseMask('yyyy-mm-dd');
        $data_inicio->setSize('30%');
        $data_fim->setSize('30%');

        $this->form->addFields([new TLabel('Cliente')], [$cliente_id]);
        $this->form->addFields(
            [new TLabel('Data (De)')],
            [$data_inicio],
            [new TLabel('Data (Até)')],
            [$data_fim]
        );

        $this->form->addAction('Gerar Relatório', new TAction([$this, 'onGenerate']), 'fa:plus green');

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        parent::add($vbox);
    }

    public function onGenerate($param)
    {
        try {
            $data = $this->form->getData();
            $this->form->setData($data);

            if (empty($data->cliente_id)) {
                throw new Exception('Selecione um cliente');
            }

            TTransaction::open('vendas');
            $cliente = Cliente::find($data->cliente_id);
            if (!$cliente) {
                throw new Exception('Cliente não encontrado');
            }

            $pdf = new FPDF('P', 'pt', 'A4');
            $pdf->AddPage();
            $pdf->SetLeftMargin(50);
            $pdf->SetRightMargin(50);
            $pdf->SetAutoPageBreak(true, 40);
            $pdf->SetDrawColor(0);
            $pdf->SetLineWidth(0.3);
            $pdf->SetFont('Arial', '', 10);

            $t = fn($str) => iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $str);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(430, 40);
            $pdf->Cell(115, 15, $t('NOTA FISCAL: 01'), 1, 1, 'C');

            $pdf->SetXY(50, 70);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 14, $t('DESTINATÁRIO / REMETENTE:'), 0, 1, 'L');

            $pdf->SetFont('Arial', '', 9);
            $pdf->SetX(50);
            $pdf->Cell(190, 14, $t('Nome/Razão Social:'), 'LTR', 0, 'L');
            $pdf->Cell(145, 14, $t('CNPJ/CPF:'), 'LTR', 0, 'L');
            $pdf->Cell(160, 14, $t('Data de emissão:'), 'LTR', 1, 'L');

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetX(50);
            $pdf->Cell(190, 16, $t($cliente->nome), 'LBR', 0, 'L');
            $pdf->Cell(145, 16, $t($cliente->cpf ?? '---'), 'LBR', 0, 'L');
            $pdf->Cell(160, 16, date('d/m/Y'), 'LBR', 1, 'L');

            $pdf->SetFont('Arial', '', 9);
            $pdf->SetX(50);
            $pdf->Cell(130, 14, $t('Município:'), 'LTR', 0, 'L');
            $pdf->Cell(95, 14, $t('Fone/Fax:'), 'LTR', 0, 'L');
            $pdf->Cell(40, 14, $t('UF:'), 'LTR', 0, 'L');
            $pdf->Cell(120, 14, $t('Inscrição Estadual:'), 'LTR', 0, 'L');
            $pdf->Cell(110, 14, $t('Hora Saída:'), 'LTR', 1, 'L');

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetX(50);
            $pdf->Cell(130, 16, $t($cliente->cidade), 'LBR', 0, 'L');
            $pdf->Cell(95, 16, $t($cliente->fone ?? '---'), 'LBR', 0, 'L');
            $pdf->Cell(40, 16, $t($cliente->estado ?? 'SC'), 'LBR', 0, 'L');
            $pdf->Cell(120, 16, '', 'LBR', 0, 'L');
            $pdf->Cell(110, 16, date('H:i'), 'LBR', 1, 'L');

            $pdf->Ln(10);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetX(50);
            $pdf->Cell(0, 14, $t('DADOS DO PRODUTO:'), 0, 1, 'L');
            $pdf->SetFillColor(230, 230, 230);

            $pdf->SetX(50);
            $pdf->Cell(45, 15, $t('Código'), 1, 0, 'C', true);
            $pdf->Cell(200, 15, $t('Nome'), 1, 0, 'C', true);
            $pdf->Cell(45, 15, $t('Qtd'), 1, 0, 'C', true);
            $pdf->Cell(70, 15, $t('Valor Unit'), 1, 0, 'C', true);
            $pdf->Cell(60, 15, $t('Desc'), 1, 0, 'C', true);
            $pdf->Cell(75, 15, $t('Total'), 1, 1, 'C', true);

            $filtro_data = '';
            if (!empty($data->data_inicio)) {
                $inicio = TDate::convertToMask($data->data_inicio, 'dd/mm/yyyy', 'yyyy-mm-dd');
                $filtro_data .= " AND v.date >= '{$inicio}'";
            }
            if (!empty($data->data_fim)) {
                $fim = TDate::convertToMask($data->data_fim, 'dd/mm/yyyy', 'yyyy-mm-dd');
                $filtro_data .= " AND v.date <= '{$fim}'";
            }

            $conn = TTransaction::get();
            $sql = "SELECT 
                    p.id AS produto_id, 
                    p.nome, 
                    vi.quantidade, 
                    vi.preco_venda, 
                    vi.desconto, 
                    v.date
                FROM venda_item vi
                JOIN venda v ON v.id = vi.venda_id
                JOIN produto p ON p.id = vi.produto_id
                WHERE v.cliente_id = :cliente_id {$filtro_data}
                ORDER BY v.date";

            $result = $conn->prepare($sql);
            $result->execute([':cliente_id' => $cliente->id]);
            $vendas = $result->fetchAll(PDO::FETCH_ASSOC);

            if (empty($vendas)) {
                throw new Exception('Nenhuma venda encontrada nesse período para este cliente.');
            }

            $pdf->SetFont('Arial', '', 9);
            $total_geral = 0;

            foreach ($vendas as $item) {
                $preco_unitario = (float) $item['preco_venda'];
                $quantidade     = (float) $item['quantidade'];
                $desconto       = (float) ($item['desconto'] ?? 0);

                $preco_final = max(0, $preco_unitario - $desconto);
                $total = $preco_final * $quantidade;
                $total_geral += $total;

                $pdf->SetX(50);
                $pdf->Cell(45, 15, $item['produto_id'], 1, 0, 'C');
                $pdf->Cell(200, 15, $t($item['nome']), 1, 0, 'L');
                $pdf->Cell(45, 15, number_format($quantidade, 0), 1, 0, 'C');
                $pdf->Cell(70, 15, 'R$ ' . number_format($preco_unitario, 2, ',', '.'), 1, 0, 'R');
                $pdf->Cell(60, 15, 'R$ ' . number_format($desconto, 2, ',', '.'), 1, 0, 'R');
                $pdf->Cell(75, 15, 'R$ ' . number_format($total, 2, ',', '.'), 1, 1, 'R');
            }

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->SetX(50);
            $pdf->Cell(420, 14, $t('Total Geral:'), 1, 0, 'R', true);
            $pdf->Cell(75, 14, 'R$ ' . number_format($total_geral, 2, ',', '.'), 1, 1, 'R', true);

            $pdf->Ln(20);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->Cell(0, 10, $t('Documento gerado em ') . date('d/m/Y H:i'), 0, 1, 'R');

            $file = 'app/output/relatorio_vendas_cliente.pdf';
            $pdf->Output($file, 'F');

            $window = TWindow::create('Nota Fiscal de Vendas', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $file;
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $object->add('Seu navegador não suporta PDF. <a href="' . $file . '" target="_blank">Clique aqui para baixar</a>.');
            $window->add($object);
            $window->show();

            TTransaction::close();
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }
}
