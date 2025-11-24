<?php

class VendasPainel extends TPage
{
    protected $form;
    protected $detail_list;

    public function __construct($param)
    {
        parent::__construct();

        parent::setTargetContainer('adianti_right_panel');

        $this->form = new BootstrapFormBuilder('form_Vendas_View');
        $this->form->setFormTitle('Resumo da Venda');
        $this->form->setColumnClasses(2, ['col-sm-3', 'col-sm-9']);

        $this->form->addHeaderActionLink(('Editar'), new TAction([__CLASS__, 'onEdit'], ['key' => $param['key'], 'register_state' => 'true']), 'far:edit blue');
        $this->form->addHeaderActionLink(('Fechar'), new TAction([__CLASS__, 'onClose']), 'fa:times red');

        parent::add($this->form);
    }

    public function onView($param)
    {
        try {
            TTransaction::open('vendas');

            $master_object = new Venda($param['key']);

            $label_id      = new TLabel('ID:', '#ffffff', '16px', 'b');
            $label_cliente = new TLabel('Cliente:', '#ffffff', '16px', 'b');
            $label_date    = new TLabel('Data:', '#ffffff', '16px', 'b');
            $label_total   = new TLabel('Total:', '#ffffff', '16px', 'b');

            $text_id      = new TTextDisplay($master_object->id, '#ffffff', '16px', '');
            $text_cliente = new TTextDisplay(Cliente::find($master_object->cliente_id)->nome, '#ffffff', '16px', '');
            $text_date    = new TTextDisplay(TDate::date2br($master_object->date), '#ffffff', '16px', '');
            $text_total   = new TTextDisplay('R$ ' . number_format($master_object->total, 2, ',', '.'), '#ffffff', '16px', '');

            $this->form->addFields([$label_id], [$text_id], [$label_cliente], [$text_cliente]);

            $this->form->addFields([$label_date], [$text_date], [$label_total], [$text_total]);

            $this->detail_list = new BootstrapDatagridWrapper(new TDataGrid);
            $this->detail_list->style = 'width:100%';
            $this->detail_list->disableDefaultClick();

            $col_produto = new TDataGridColumn('produto->nome', 'Produto', 'left');
            $col_preco   = new TDataGridColumn('preco_venda', 'Preço', 'center');
            $col_qtd     = new TDataGridColumn('quantidade', 'Qtd', 'right');
            $col_desc    = new TDataGridColumn('desconto', 'Desconto', 'right');
            $col_total   = new TDataGridColumn('=({quantidade} * {preco_venda}) - {desconto}', 'Subtotal', 'right');

            $this->detail_list->addColumn($col_produto);
            $this->detail_list->addColumn($col_preco);
            $this->detail_list->addColumn($col_qtd);
            $this->detail_list->addColumn($col_desc);
            $this->detail_list->addColumn($col_total);

            $format_value = function ($value) {
                if (is_numeric($value)) {
                    return 'R$ ' . number_format($value, 2, ',', '.');
                }
                return $value;
            };

            $col_preco->setTransformer($format_value);
            $col_desc->setTransformer($format_value);
            $col_total->setTransformer($format_value);

            $col_total->enableTotal('sum', 'R$', 2, ',', '.');

            $this->detail_list->createModel();

            $items = VendaItem::where('venda_id', '=', $master_object->id)->load();
            $this->detail_list->addItems($items);

            $panel = new TPanelGroup('Itens da Venda');
            $panel->add($this->detail_list);
            $panel->getBody()->style = 'overflow-x:auto';

            $this->form->addContent([$panel]);
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onPrint($param)
    {
        try {
            $this->onView($param);

            $html = clone $this->form;
            $contents = '<style>' . file_get_contents('app/resources/styles-print-bundle.css') . '</style>';
            $contents .= $html->getContents();

            $options = new \Dompdf\Options();
            $options->setIsRemoteEnabled(true);
            $options->setChroot(getcwd());

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $file = 'app/output/vendas-export.pdf';
            file_put_contents($file, $dompdf->output());

            $window = TWindow::create('Export', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $file . '?rndval=' . uniqid();
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $object->add('O navegador não suporta a exibição deste conteúdo, 
                <a style="color:#007bff;" target=_newwindow href="' . $object->data . '"> clique aqui para baixar</a>...');

            $window->add($object);
            $window->show();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onEdit($param)
    {
        unset($param['static']);
        $param['register_state'] = 'false';
        AdiantiCoreApplication::loadPage('VendasForm', 'onEdit', $param);
    }

    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
}
