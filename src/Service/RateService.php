<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;

/**
 * Class RateService.
 */
class RateService
{
    private const DEFAULT_RATE = 2;

    /**
     * @var OCApiClient
     */
    private $client;

    /**
     * @var array
     */
    private $projectRates;

    /**
     * RateService constructor.
     */
    public function __construct(OCApiClient $client)
    {
        $this->client = $client;
    }

    public function getRateForPathAndProject(string $pathName, string $projectName): int
    {
        $this->setProjectRates();

        $pathRates = array_filter($this->projectRates, static function ($rate) use ($pathName) {
            return $rate[0] === $pathName;
        });

        $projectForPathRate = array_values(
            array_filter($pathRates, static function ($project) use ($projectName) {
                return $project[1] === $projectName;
            })
        );

        if (empty($projectForPathRate) || !isset($projectForPathRate[0][3])) {
            return self::DEFAULT_RATE;
        }

        return (int) $projectForPathRate[0][3];
    }

    private function setProjectRates(): void
    {
        if ($this->projectRates) {
            return;
        }

        $crawler = new Crawler($this->client->getProjectRates());

        $table = $crawler->filter('table.crud-list')->filter('tr')->each(static function ($tr) {
            return $tr->filter('td')->each(static function ($td) {
                return trim($td->text());
            });
        });

        unset($table[0]);

        $this->projectRates = $table;
    }
}
