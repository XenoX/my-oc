<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\EarningService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SessionExtension extends AbstractExtension
{
    /**
     * @var EarningService
     */
    private $earningService;

    /**
     * SessionExtension constructor.
     */
    public function __construct(EarningService $earningService)
    {
        $this->earningService = $earningService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_earn', [$this, 'getEarn']),
        ];
    }

    public function getEarn(array $meetings): float
    {
        return $this->earningService->getEarnsForMeetings($meetings);
    }
}
