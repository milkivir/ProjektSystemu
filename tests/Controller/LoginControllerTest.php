<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testLoginPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Login');
    }

    public function testLoginUser(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'test@example.com';
        $form['_password'] = 'password';
        $client->submit($form);

        $crawler = $client->followRedirect();

        $uri = $client->getRequest()->getUri();
        $this->assertEquals('http://localhost/', $uri);
    }

    public function testLoginUserForIncorrectEmail(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'NotExisting@example.com';
        $form['_password'] = 'password';
        $client->submit($form);

        $crawler = $client->followRedirect();

        $uri = $client->getRequest()->getUri();
        $this->assertNotEquals('http://localhost/', $uri);
    }

    public function testLoginUserForIncorrectPassword(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'test@example.com';
        $form['_password'] = 'wrongPasswd';
        $client->submit($form);

        $crawler = $client->followRedirect();

        $uri = $client->getRequest()->getUri();
        $this->assertNotEquals('http://localhost/', $uri);
    }
} 