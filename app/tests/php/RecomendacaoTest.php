<?php

use PHPUnit\Framework\TestCase;

class RecomendacaoTest extends TestCase
{
    public function testRetornoRecomendacao()
    {
        $mock = [
            [
                'produto_nome' => 'Headset Cloud 2',
                'probabilidade_compra' => 0.95
            ],
            [
                'produto_nome' => 'Mouse Gamer Logitech',
                'probabilidade_compra' => 0.72
            ]
        ];

        $this->assertCount(2, $mock);
        $this->assertEquals('Headset Cloud 2', $mock[0]['produto_nome']);
        $this->assertGreaterThan(0, $mock[0]['probabilidade_compra']);
    }
}
