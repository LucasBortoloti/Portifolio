<?php

use PHPUnit\Framework\TestCase;

class VendaModelTest extends TestCase
{
    public function testCalculoTotalDaVenda()
    {
        $itens = [
            ['preco' => 150.00],
            ['preco' => 50.00],
            ['preco' => 80.00],
        ];

        $total = 0;
        foreach ($itens as $item) {
            $total += $item['preco'];
        }

        $this->assertEquals(280.00, $total);
    }
}
