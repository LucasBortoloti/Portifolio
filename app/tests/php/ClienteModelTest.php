<?php

use PHPUnit\Framework\TestCase;

class ClienteModelTest extends TestCase
{
    public function testCriarCliente()
    {
        $cliente = [
            'nome' => 'Lucas Bortoloti',
            'cpf' => '123.456.789-10',
            'cidade' => 'Jaraguá do Sul',
            'estado' => 'SC',
        ];

        $this->assertEquals('Lucas Bortoloti', $cliente['nome']);
        $this->assertEquals('SC', $cliente['estado']);
    }

    public function testNomeNaoPodeSerVazio()
    {
        $nome = '';

        $this->assertTrue($nome === '');
    }
}
