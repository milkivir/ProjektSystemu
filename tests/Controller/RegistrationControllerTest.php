<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegisterPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Rejestracja');
    }

    public function testRegisterUser(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Register')->form();
        $form['registration_form[email]'] = 'test@example.com';
        $form['registration_form[plainPassword]'] = 'password';
        $form['registration_form[agreeTerms]'] = '1';
        $client->submit($form);

        // Check if the user is properly registered and persisted in the database
        $user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);

        $this->assertNotNull($user);
        $this->assertEquals('test@example.com', $user->getEmail());
    }
}