<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EvaluationRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EvaluationRepository::class)
 */
class Evaluation implements MeetingInterface
{
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
     * @ORM\Column(type="integer")
     */
    private $rate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $noShow;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="evaluations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * Evaluation constructor.
     */
    public function __construct()
    {
        $this->startAt = (new DateTime())->setTime(0, 0);
        $this->noShow = false;
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

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(int $rate): self
    {
        $this->rate = $rate;

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
