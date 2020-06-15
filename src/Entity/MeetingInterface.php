<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;

interface MeetingInterface
{
    public function getStartAt(): ?DateTimeInterface;

    public function setStartAt(DateTimeInterface $startAt);

    public function getRate(): ?int;

    public function setRate(int $rate);

    public function isNoShow(): bool;

    public function setNoShow(bool $noShow);
}
