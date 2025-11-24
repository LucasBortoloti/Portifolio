<?php

class ClienteForm extends TWindow
{
    protected $form;

    public function __construct($param)
    {
        parent::__construct($param);
        parent::setSize(0.5, null);
        parent::setMinWidth(0.5, 700);
        parent::removePadding();
        parent::disableEscape();
        parent::setTitle('Cadastro de Novos Clientes');

        $this->form = new BootstrapFormBuilder('form_cliente');

        $id     = new TEntry('id');
        $nome   = new TEntry('nome');
        $cpf   = new TEntry('cpf');
        $fone   = new TEntry('fone');
        $cidade = new TEntry('cidade');
        $estado = new TEntry('estado');

        $id->setEditable(false);
        $id->setSize('20%');

        $nome->setSize('100%');
        $cpf->setSize('100%');
        $fone->setSize('100%');
        $cidade->setSize('80%');
        $estado->setSize('30%');

        $cpf->setMask('999.999.999-99');
        $fone->setMask('(99) 99999-9999');

        $nome->addValidation('Nome', new TRequiredValidator);
        $cpf->addValidation('CPF', new TRequiredValidator);
        $fone->addValidation('Telefone', new TRequiredValidator);

        $this->form->addFields([new TLabel('Id')], [$id]);
        $this->form->addFields([new TLabel('Nome')], [$nome]);
        $this->form->addFields([new TLabel('CPF')], [$cpf], [new TLabel('Telefone')], [$fone]);
        $this->form->addFields([new TLabel('Cidade')], [$cidade], [new TLabel('Estado/Sigla')], [$estado]);

        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');

        parent::add($this->form);
    }

    public function onSave($param)
    {
        try {
            TTransaction::open('vendas');

            $object = $this->form->getData('Cliente');
            $this->form->validate();
            $object->store();

            TTransaction::close();

            new TMessage('info', 'Cliente salvo com sucesso', new TAction(['VendasList', 'onReload']));
        } catch (Exception $e) {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onEdit($param)
    {
        // sempre abre vazio para novo cliente
        $this->form->clear();
    }
}
