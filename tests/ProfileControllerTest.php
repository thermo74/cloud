<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileControllerTest extends WebTestCase
{
    protected function createAuthorizedClient(int $id)
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->find($id);
        $client->loginUser($testUser);

        return $client;
    }

    public function testVisitingWhileLoggedAsAdminOK()
    {
        $client = $this->createAuthorizedClient(3);

        $client->request('GET', '/en/profile/files');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Available files');

        $client->request('GET', '/en/profile/account');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('label', 'Name');

        $client->request('GET', '/en/admin/roles/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Roles');

        $client->request('GET', '/en/admin/categories/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Categories');

        $client->request('GET', '/en/admin/users/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Users');

        $client->request('GET', '/en/admin/files/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Files');

        $client->request('GET', '/en/profile/files/view/6');
        $this->assertResponseIsSuccessful();
        $client->request('GET', '/en/profile/files/view/0');
        $this->assertTrue($client->getResponse()->isNotFound());

        $client->request('GET', '/en/cloud-admin');
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'User');

        $client->request('GET', '/en/logout');
        $this->assertResponseRedirects();
        $client->followRedirect();

    }

    public function testVisitingWhileLoggedAsModeratorOK()
    {
        $client = $this->createAuthorizedClient(2);

        $client->request('GET', '/en/profile/files');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Available files');

        $client->request('GET', '/en/profile/account');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('label', 'Name');

        $client->request('GET', '/en/admin/roles/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Roles');

        $client->request('GET', '/en/admin/categories/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Categories');

        $client->request('GET', '/en/admin/users/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Users');

        $client->request('GET', '/en/admin/files/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Files');

        $client->request('GET', '/en/profile/files/view/6');
        $this->assertResponseIsSuccessful();
        $client->request('GET', '/en/profile/files/view/0');
        $this->assertTrue($client->getResponse()->isNotFound());

        

    }

    public function testVisitingWhileLoggedAsUserOK()
    {
        $client = $this->createAuthorizedClient(1);

        $client->request('GET','/en/profile/files');
        $this->assertResponseIsSuccessful();

        $client->request('GET','/en/profile/account');
        $this->assertResponseIsSuccessful();

        $client->request('GET', '/en/profile/files/view/2');
        $this->assertTrue($client->getResponse()->isNotFound());
        $client->request('GET', '/en/profile/files/view/3');
        $this->assertResponseRedirects('/en/profile/files', '302');
    }
}
