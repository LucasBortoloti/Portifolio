<?php

use Adianti\Database\TRecord;

class VendaItem extends TRecord
{
    const TABLENAME = 'venda_item';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('id');
        parent::addAttribute('venda_id');
        parent::addAttribute('produto_id');
        parent::addAttribute('quantidade');
        parent::addAttribute('preco_venda');
        parent::addAttribute('desconto');
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
}
