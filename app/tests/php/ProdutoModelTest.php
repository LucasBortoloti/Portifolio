<?php

use PHPUnit\Framework\TestCase;

class ProdutoModelTest extends TestCase
{
    public function testProdutoValido()
    {
        $produto = [
            'nome' => 'Headset HyperX Cloud',
            'categoria' => 'headsets',
            'preco' => 399.90
        ];

        $this->assertEquals('headsets', $produto['categoria']);
        $this->assertGreaterThan(0, $produto['preco']);
    }

    public function testPrecoInvalido()
    {
        $preco = -10;

        $this->assertLessThan(0, $preco);
    }
}
