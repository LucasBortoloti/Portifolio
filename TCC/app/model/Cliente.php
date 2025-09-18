<?php

use Adianti\Database\TRecord;

class Cliente extends TRecord
{
    const TABLENAME = 'cliente';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {

        parent::__construct($id, $callObjectLoad);

        parent::addAttribute('id');
        parent::addAttribute('nome');
        parent::addAttribute('cpf');
        parent::addAttribute('cidade');
        parent::addAttribute('fone');
        parent::addAttribute('estado');
    }
}
