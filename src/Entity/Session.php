<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SessionRepository;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SessionRepository::class)
 */
class Session implements MeetingInterface
{
    public const SESSIONS_BY_MONTH = 5;
    public const EVALUATION_DURATION = 'PT45M';
    public const FOUNDED_DURATION = 'PT45M';
    public const AUTO_FOUNDED_DURATION = 'PT30M';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startAt;

    /**
     * @ORM\Column(type="dateinterval")
     */
    private $duration;

    /**
     * @ORM\Column(type="integer")
     */
    private $rate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $evaluation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $noShow;

    /**
     * @var Student
     *
     * @ORM\ManyToOne(targetEntity=Student::class, inversedBy="sessions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $student;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * Session constructor.
     */
    public function __construct()
    {
        $this->startAt = (new DateTime())->setTime(0, 0);
        $this->evaluation = false;
        $this->noShow = false;
    }

    public function isFunded(): bool
    {
        return (int) $this->duration->format('%i') === (int) filter_var(self::FOUNDED_DURATION, FILTER_SANITIZE_NUMBER_INT);
    }

    public function getDurationInterval(): DateInterval
    {
        return $this->student->isFunded()
            ? new DateInterval(self::FOUNDED_DURATION)
            : new DateInterval(self::AUTO_FOUNDED_DURATION)
        ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartAt(): ?DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getDuration(): ?DateInterval
    {
        return $this->duration;
    }

    public function setDuration(DateInterval $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(int $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function isEvaluation(): bool
    {
        return $this->evaluation ?? false;
    }

    public function setEvaluation(bool $evaluation): self
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    public function isNoShow(): bool
    {
        return $this->noShow ?? false;
    }

    public function setNoShow(bool $noShow): self
    {
        $this->noShow = $noShow;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }
}
