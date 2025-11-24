<?php

class VendasList extends TPage
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;

    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('vendas');
        $this->setActiveRecord('Venda');
        $this->setDefaultOrder('id', 'asc');
        $this->addFilterField('id', '=', 'id');
        $this->addFilterField('cliente_id', '=', 'cliente_id');

        $this->addFilterField('date', '>=', 'data_inicio', function ($value) {
            return TDate::convertToMask($value, 'dd/mm/yyyy', 'yyyy-mm-dd');
        });

        $this->addFilterField('date', '<=', 'data_ate', function ($value) {
            return TDate::convertToMask($value, 'dd/mm/yyyy', 'yyyy-mm-dd');
        });

        $this->form = new BootstrapFormBuilder('form_search_Vendas');
        $this->form->setFormTitle('Lista de Vendas');

        $id          = new TEntry('id');
        $data_inicio = new TDate('data_inicio');
        $data_ate    = new TDate('data_ate');

        $cliente_id = new TDBUniqueSearch('cliente_id', 'vendas', 'Cliente', 'id', 'nome');
        $cliente_id->setMinLength(1);
        $cliente_id->setMask('{nome} ({id})');

        $button = new TActionLink('', new TAction(['ClienteForm', 'onEdit']), 'green', null, null, 'fa:plus-circle');
        $button->class = 'btn btn-default inline-button';
        $button->title = 'Novo Cliente';
        $cliente_id->after($button);

        $this->form->addFields([new TLabel('Id')], [$id]);
        $this->form->addFields(
            [new TLabel('Data (De)')],
            [$data_inicio],
            [new TLabel('Data (Até)')],
            [$data_ate]
        );
        $this->form->addFields([new TLabel('Cliente')], [$cliente_id]);

        $id->setSize('39%');
        $data_inicio->setSize('100%');
        $data_ate->setSize('100%');
        $cliente_id->setSize('97%');

        $data_inicio->setMask('dd/mm/yyyy');
        $data_ate->setMask('dd/mm/yyyy');

        $this->form->setData(TSession::getValue('VendasList_filter_data'));

        $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search');
        $this->form->addActionLink('Nova Venda', new TAction(['VendasForm', 'onEdit'], ['register_state' => 'false']), 'fa:plus green');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';

        $column_id      = new TDataGridColumn('id', 'Id', 'center', '10%');
        $column_date    = new TDataGridColumn('date', 'Data', 'center', '25%');
        $column_cliente = new TDataGridColumn('{cliente->nome}', 'Cliente', 'center', '45%');
        $column_total   = new TDataGridColumn('total', 'Total', 'right', '20%');

        $format_value = function ($value) {
            if (is_numeric($value)) {
                return 'R$ ' . number_format($value, 2, ',', '.');
            }
            return $value;
        };
        $column_total->setTransformer($format_value);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_date);
        $this->datagrid->addColumn($column_cliente);
        $this->datagrid->addColumn($column_total);

        $column_id->setAction(new TAction([$this, 'onReload']), ['order' => 'id']);
        $column_date->setAction(new TAction([$this, 'onReload']), ['order' => 'date']);

        $column_date->setTransformer(function ($value) {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        });

        $action_view   = new TDataGridAction(['VendasPainel', 'onView'], ['key' => '{id}', 'register_state' => 'false']);
        $action_edit   = new TDataGridAction(['VendasForm', 'onEdit'], ['key' => '{id}', 'register_state' => 'false']);
        $action_delete = new TDataGridAction([$this, 'onDelete'], ['key' => '{id}']);
        $action_recomendar = new TDataGridAction(['RecomendacaoService', 'onRecomendar'], ['id' => '{id}']);
        $action_recomendar->setLabel('Recomendar produtos');
        $action_recomendar->setImage('fa:cogs gray fa-fw');

        $this->datagrid->addAction($action_view, 'Ver detalhes', 'fa:search green fa-fw');
        $this->datagrid->addAction($action_edit, 'Editar', 'far:edit blue fa-fw');
        $this->datagrid->addAction($action_delete, 'Deletar', 'far:trash-alt red fa-fw');
        $this->datagrid->addAction($action_recomendar, 'Recomendar produtos', 'fa:cogs gray fa-fw');

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel = TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        $panel->getBody()->style = 'overflow-x:auto';

        parent::add($container);
    }
}
