<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class OCApiClient.
 */
class OCApiClient
{
    private const BASE_API_URL = 'https://api.openclassrooms.com';
    private const BASE_WEBSITE_URL = 'https://openclassrooms.com';

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var string
     */
    private $token;

    /**
     * OCApiClient constructor.
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getProjectRates(): string
    {
        try {
            return $this->client->request(
                'GET',
                sprintf('%s/fr/mentor-rates', self::BASE_WEBSITE_URL)
            )->getContent();
        } catch (ExceptionInterface$e) {
            return '';
        }
    }

    public function getPaths(): array
    {
        try {
            return $this->client->request(
                'GET',
                sprintf('%s/paths', self::BASE_API_URL),
                $this->getDefaultParams()
            )->toArray();
        } catch (ExceptionInterface $e) {
            return [];
        }
    }

    public function getPath(int $id): array
    {
        try {
            return $this->client->request(
                'GET',
                sprintf('%s/paths/%d', self::BASE_API_URL, $id),
                $this->getDefaultParams()
            )->toArray();
        } catch (ExceptionInterface $e) {
            return [];
        }
    }

    private function getDefaultParams(): array
    {
        $this->auth();

        return ['headers' => ['Authorization' => sprintf('Bearer %s', $this->token)]];
    }

    private function auth(): void
    {
        if (null !== $this->token) {
            return;
        }

        $params = [
            'headers' => [
                'Authorization' => sprintf(
                    'Basic %s',
                    base64_encode(
                        sprintf(
                            '%s:%s',
                            $_ENV['OAUTH_OC_CLIENT_ID'],
                            $_ENV['OAUTH_OC_CLIENT_SECRET']
                        )
                    )
                ),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'grant_type' => 'client_credentials',
                'scope' => 'learning_content',
            ],
        ];

        try {
            $response = $this->client->request('POST', sprintf('%s/oauth2/token', self::BASE_API_URL), $params)->toArray();
            $this->token = $response['access_token'];
        } catch (ExceptionInterface$e) {
        }
    }
}
