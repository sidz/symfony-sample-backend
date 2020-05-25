<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

abstract class WebTestCase extends BaseWebTestCase
{
    private ?EntityManagerInterface $em = null;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->disableReboot();

        $this->em = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->em->beginTransaction();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->em->rollback();

        $this->em->close();
        $this->em = null;
    }

    protected function createAuthenticatedClient(
        string $username = 'user',
        string $password = 'password'
    ): KernelBrowser {
        $this->registerUser($username, $password);

        $this->client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => $username,
                'password' => $password,
            ])
        );

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $this->client;
    }

    protected function registerUser(string $username = 'user', string $password = 'password'): void
    {
        $this->client->request(
            'POST',
            '/api/register',
            [
                'username' => $username,
                'password' => $password,
            ],
        );
    }
}
