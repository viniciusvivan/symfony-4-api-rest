<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EspecialidadesWebTest extends WebTestCase
{
    public function testGaranteQueRequisicaoFalhaSemAutenticacao()
    {
        $client = static::createClient();
        $client->request('GET', '/especialidade');

        self::assertEquals(401, $client->getResponse()->getStatusCode());
    }

    public function testGaranteQueEspecilidadesSaoListadas()
    {
        $client = static::createClient();
        $token = $this->login($client);
        $client->request('GET', '/especialidade', [], [], [
            'HTTP_AUTHORIZATION' => "Bearer $token"
        ]);

        $resposta = json_decode($client->getResponse()->getContent());

        self::assertTrue($resposta->success);
    }

    public function testInsertEspecialidade()
    {
        $client = static::createClient();
        $token = $this->login($client);
        $client->request('POST', '/especialidade', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => "Bearer $token"
        ], json_encode([
            'descricao' => 'Teste',
        ]));

        self::assertEquals(
            201,
            json_decode($client->getResponse()->getStatusCode())
        );
    }

    private function login(KernelBrowser $client)
    {
        $client->request('POST', '/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'usuario' => 'usuario',
            'senha' => '123456'
        ]));

        return json_decode($client->getResponse()->getContent())->access_token;
    }
}