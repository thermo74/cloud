<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FilesControllerTest extends WebTestCase
{
    public function testDefaultUrlOK()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseRedirects();

        $client->request('GET', '/en/login');
        $this->assertResponseIsSuccessful();
        $client->request('GET', '/fr/connection');
        $this->assertResponseIsSuccessful();

    }
}
