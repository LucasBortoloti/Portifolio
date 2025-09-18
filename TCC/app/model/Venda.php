<?php

use Adianti\Database\TRecord;

class Venda extends TRecord
{
    const TABLENAME = 'venda';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max';
    private $cliente;
    private $produtos;
    private $venda_items;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {

        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('id');
        parent::addAttribute('date');
        parent::addAttribute('cliente_id');
        parent::addAttribute('total');
    }

    // feito para eu pegar a quantidade da tabela venda_item e do model venda_item
    public function get_venda_items()
    {
        if (empty($this->venda_items)) {
            $this->venda_items = VendaItem::where('venda_id', '=', $this->id)->load();
        }
        return $this->sale_items;
    }

    // feito para eu pegar os produtos da tabela e do model produto
    public function get_produtos()
    {
        $produto_ids = VendaItem::where('venda_id', '=', $this->id)->getIndexedArray('produto_id');
        $this->produtos = Produto::where('id', 'IN', $produto_ids)->load();

        return $this->produtos;
    }

    public function set_produto(Produto $object)
    {
        $this->produto = $object;
    }

    public function get_produto()
    {
        if (empty($this->produto)) {
            $this->produto = new Produto($this->produto_id);
        }

        return $this->produto;
    }

    public function set_cliente(Cliente $object)
    {
        $this->cliente = $object;
    }

    public function get_cliente()
    {
        if (empty($this->cliente)) {
            $this->cliente = new Cliente($this->cliente_id);
        }

        return $this->cliente;
    }

    public function set_status(VendaStatus $object)
    {
        $this->status = $object;
    }

    public function get_status()
    {
        if (empty($this->status)) {
            $this->status = new VendaStatus($this->status_id);
        }

        return $this->status;
    }

    public function onBeforeDelete($param)
    {

        try {   //echo "<pre>";
            //print_r($param);

            // TTransaction::open('vendas');

            VendaItem::where('venda_id', '=', $this->id)->delete();

            // foreach( $venda_items as $item){

            //     $item->delete();
            // }

            // parent::delete();

            // TTransaction::close();

        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
        //echo "</pre>";
    }
}
