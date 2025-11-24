<?php

class VendasForm extends TPage
{
    protected $form;
    protected $grid_itens_venda;

    function __construct()
    {
        parent::__construct();
        $this->setTargetContainer('adianti_right_panel');

        $this->form = new BootstrapFormBuilder('form_Vendas');
        $this->form->setFormTitle('Cadastro de Vendas');
        $this->form->setProperty('style', 'margin:0;border:0');
        $this->form->setClientValidation(true);

        $id        = new TEntry('id');
        $data      = new TDate('date');
        $cliente   = new TDBUniqueSearch('cliente_id', 'vendas', 'Cliente', 'id', 'nome');

        $det_uniq   = new THidden('produto_detalhe_uniqid');
        $det_id     = new THidden('produto_detalhe_id');
        $det_prod   = new TDBUniqueSearch('produto_detalhe_produto_id', 'vendas', 'Produto', 'id', 'nome');
        $det_preco  = new TEntry('produto_detalhe_preco');
        $det_qtd    = new TEntry('produto_detalhe_quantidade');
        $det_desc   = new TEntry('produto_detalhe_desconto');
        $det_total  = new TEntry('produto_detalhe_total');

        $id->setEditable(false);
        $id->setSize('20%');
        $cliente->setSize('calc(100% - 30px)');
        $cliente->setMinLength(1);
        $data->setSize('100%');
        $data->setMask('dd/mm/yyyy');
        $data->setDatabaseMask('yyyy-mm-dd');
        $data->addValidation('Data da Venda', new TRequiredValidator);
        $cliente->addValidation('Cliente', new TRequiredValidator);

        $det_prod->setSize('100%');
        $det_prod->setMinLength(1);
        $det_preco->setSize('100%');
        $det_qtd->setSize('100%');
        $det_desc->setSize('100%');
        $det_prod->setChangeAction(new TAction([$this, 'onProdutoChange']));

        $this->form->addFields([new TLabel('Id')], [$id]);
        $this->form->addFields(
            [new TLabel('Cliente', '#0b8fccff')],
            [$cliente],
            [new TLabel('Data da Venda', '#0b8fccff')],
            [$data]
        );

        $this->form->addContent(['<p style="font-size:17px; color:#fff;">Itens da Venda</p><hr>']);
        $this->form->addFields([$det_uniq], [$det_id]);
        $this->form->addFields(
            [new TLabel('Produto', '#0b8fccff')],
            [$det_prod],
            [new TLabel('Quantidade', '#0b8fccff')],
            [$det_qtd]
        );
        $this->form->addFields(
            [new TLabel('Preço Unitário', '#0b8fccff')],
            [$det_preco],
            [new TLabel('Desconto (R$)')],
            [$det_desc]
        );

        $btn_add = TButton::create('add_item', [$this, 'onProdutoAdd'], 'Adicionar Produto', 'fa:plus-circle green');
        $btn_add->getAction()->setParameter('static', '1');
        $this->form->addFields([], [$btn_add]);

        $this->grid_itens_venda = new BootstrapDatagridWrapper(new TDataGrid);
        $this->grid_itens_venda->setHeight(150);
        $this->grid_itens_venda->makeScrollable();
        $this->grid_itens_venda->setId('grid_itens_venda');
        $this->grid_itens_venda->generateHiddenFields();
        $this->grid_itens_venda->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        $this->grid_itens_venda->setMutationAction(new TAction([$this, 'onMutationAction']));

        $col_uniq  = new TDataGridColumn('uniqid', 'Chave', 'center', '10%');
        $col_id    = new TDataGridColumn('id', 'ID', 'center', '10%');
        $col_cod   = new TDataGridColumn('produto_id', 'Id', 'center', '10%');
        $col_nome  = new TDataGridColumn('produto_id', 'Produto', 'left', '30%');
        $col_qtd   = new TDataGridColumn('quantidade', 'Qtd', 'center', '10%');
        $col_preco = new TDataGridColumn('preco_venda', 'Preço', 'right', '15%');
        $col_desc  = new TDataGridColumn('desconto', 'Desconto', 'right', '15%');
        $col_subt  = new TDataGridColumn('=({quantidade} * {preco_venda}) - {desconto}', 'Subtotal', 'right', '20%');

        $this->grid_itens_venda->addColumn($col_uniq);
        $this->grid_itens_venda->addColumn($col_id);
        $this->grid_itens_venda->addColumn($col_cod);
        $this->grid_itens_venda->addColumn($col_nome);
        $this->grid_itens_venda->addColumn($col_qtd);
        $this->grid_itens_venda->addColumn($col_preco);
        $this->grid_itens_venda->addColumn($col_desc);
        $this->grid_itens_venda->addColumn($col_subt);

        $col_nome->setTransformer(function ($value) {
            return Produto::findInTransaction('vendas', $value)->nome;
        });
        $col_subt->enableTotal('sum', 'R$', 2, ',', '.');

        $format_currency = function ($value) {
            if (is_numeric($value)) {
                return 'R$ ' . number_format($value, 2, ',', '.');
            }
            return $value;
        };
        $col_preco->setTransformer($format_currency);
        $col_desc->setTransformer($format_currency);
        $col_subt->setTransformer($format_currency);

        $col_id->setVisibility(false);
        $col_uniq->setVisibility(false);

        $acao_editar  = new TDataGridAction([$this, 'onEditItemProduto']);
        $acao_editar->setFields(['uniqid', '*']);

        $acao_remover = new TDataGridAction([$this, 'onDeleteItem']);
        $acao_remover->setField('uniqid');

        $this->grid_itens_venda->addAction($acao_editar, 'Editar', 'far:edit blue');
        $this->grid_itens_venda->addAction($acao_remover, 'Excluir', 'far:trash-alt red');

        $this->grid_itens_venda->createModel();

        $painel = new TPanelGroup;
        $painel->add($this->grid_itens_venda);
        $painel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent([$painel]);

        $this->form->addHeaderActionLink('Fechar', new TAction([__CLASS__, 'onClose'], ['static' => '1']), 'fa:times red');
        $this->form->addAction('Salvar', new TAction([$this, 'onSave'], ['static' => '1']), 'fa:save green');
        $this->form->addAction('Limpar', new TAction([$this, 'onClear']), 'fa:eraser orange');

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        parent::add($container);
    }

    public function onLoad($param)
    {
        $data = new stdClass;
        $data->cliente_id   = $param['cliente_id'];
        $this->form->setData($data);
    }

    public static function onProdutoChange($params)
    {
        if (!empty($params['produto_detalhe_produto_id'])) {
            try {
                TTransaction::open('vendas');
                $produto = new Produto($params['produto_detalhe_produto_id']);
                TForm::sendData('form_Vendas', (object) ['produto_detalhe_preco' => $produto->preco]);
                TTransaction::close();
            } catch (Exception $e) {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        }
    }

    function onClear($param)
    {
        $this->form->clear();
    }

    public function onProdutoAdd($param)
    {
        try {
            $this->form->validate();
            $data = $this->form->getData();

            if ((!$data->produto_detalhe_produto_id) || (!$data->produto_detalhe_quantidade) || (!$data->produto_detalhe_preco)) {
                throw new Exception('Os campos Produto, Quantidade e Preço são obrigatórios');
            }

            $uniqid = !empty($data->produto_detalhe_uniqid) ? $data->produto_detalhe_uniqid : uniqid();

            $grid_data = [
                'uniqid'      => $uniqid,
                'id'          => $data->produto_detalhe_id,
                'produto_id'  => $data->produto_detalhe_produto_id,
                'quantidade'  => $data->produto_detalhe_quantidade,
                'preco_venda' => $data->produto_detalhe_preco,
                'desconto'    => $data->produto_detalhe_desconto
            ];

            $row = $this->grid_itens_venda->addItem((object) $grid_data);
            $row->id = $uniqid;

            TDataGrid::replaceRowById('grid_itens_venda', $uniqid, $row);

            $data->produto_detalhe_uniqid     = '';
            $data->produto_detalhe_id         = '';
            $data->produto_detalhe_produto_id = '';
            $data->produto_detalhe_nome       = '';
            $data->produto_detalhe_quantidade = '';
            $data->produto_detalhe_preco      = '';
            $data->produto_detalhe_desconto   = '';

            TForm::sendData('form_Vendas', $data, false, false);
        } catch (Exception $e) {
            $this->form->setData($this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onEditItemProduto($param)
    {
        $data = new stdClass;
        $data->produto_detalhe_uniqid     = $param['uniqid'];
        $data->produto_detalhe_id         = $param['id'];
        $data->produto_detalhe_produto_id = $param['produto_id'];
        $data->produto_detalhe_quantidade = $param['quantidade'];
        $data->produto_detalhe_preco      = $param['preco_venda'];
        $data->produto_detalhe_desconto   = $param['desconto'];

        TForm::sendData('form_Vendas', $data, false, false);
    }

    public static function onDeleteItem($param)
    {
        $data = new stdClass;
        $data->produto_detalhe_uniqid     = '';
        $data->produto_detalhe_id         = '';
        $data->produto_detalhe_produto_id = '';
        $data->produto_detalhe_quantidade = '';
        $data->produto_detalhe_preco      = '';
        $data->produto_detalhe_desconto   = '';

        TForm::sendData('form_Vendas', $data, false, false);
        TDataGrid::removeRowById('grid_itens_venda', $param['uniqid']);
    }

    public function onEdit($param)
    {
        try {
            TTransaction::open('vendas');

            if (isset($param['key'])) {
                $key = $param['key'];

                $object = new Venda($key);
                $venda_items = VendaItem::where('venda_id', '=', $object->id)->load();

                foreach ($venda_items as $item) {
                    $item->uniqid = uniqid();
                    $row = $this->grid_itens_venda->addItem($item);
                    $row->id = $item->uniqid;
                }
                $this->form->setData($object);
                TTransaction::close();
            } else {
                $this->form->clear();
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onSave($param)
    {
        try {
            TTransaction::open('vendas');

            $data = $this->form->getData();
            $this->form->validate();

            $venda = new Venda;
            $venda->fromArray((array) $data);
            $venda->store();

            VendaItem::where('venda_id', '=', $venda->id)->delete();

            $total = 0;
            if (!empty($param['grid_itens_venda_produto_id'])) {
                foreach ($param['grid_itens_venda_produto_id'] as $key => $item_id) {
                    $item = new VendaItem;
                    $item->produto_id  = $item_id;
                    $item->preco_venda = (float) $param['grid_itens_venda_preco_venda'][$key];
                    $item->quantidade  = (float) $param['grid_itens_venda_quantidade'][$key];
                    $item->desconto    = (float) $param['grid_itens_venda_desconto'][$key];
                    $item->total       = ($item->preco_venda * $item->quantidade) - $item->desconto;

                    $item->venda_id = $venda->id;
                    $item->store();
                    $total += $item->total;
                }
            }
            $venda->total = $total;
            $venda->store();

            TForm::sendData('form_Vendas', (object) ['id' => $venda->id]);

            TTransaction::close();
            new TMessage('info', 'Venda salva com sucesso');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            $this->form->setData($this->form->getData());
            TTransaction::rollback();
        }
    }

    public static function onMutationAction($param)
    {
        $total = 0;
        if ($param['list_data']) {
            foreach ($param['list_data'] as $row) {
                $total += (floatval($row['preco_venda']) * floatval($row['quantidade'])) - floatval($row['desconto']);
            }
        }
        TToast::show('info', 'Novo total: <b>' . 'R$ ' . number_format($total, 2, ',', '.') . '</b>', 'bottom right');
    }

    public static function onClose()
    {
        TScript::create("Template.closeRightPanel()");
    }
}
