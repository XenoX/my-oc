<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=StudentRepository::class)
 */
class Student
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $idOC;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $funded;

    /**
     * @ORM\ManyToOne(targetEntity=Path::class, inversedBy="students")
     * @ORM\JoinColumn(nullable=false)
     */
    private $path;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity=Session::class, mappedBy="student", orphanRemoval=true)
     */
    private $sessions;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="students")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }

    public function getDetails(): string
    {
        return sprintf(
            '[%s] %s - %s (%s)',
            $this->isFunded() ? 'F' : 'AF',
            $this->name,
            $this->project->getName(),
            $this->path->getName()
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdOC(): ?int
    {
        return $this->idOC;
    }

    public function setIdOC(int $idOC): self
    {
        $this->idOC = $idOC;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isFunded(): ?bool
    {
        return $this->funded;
    }

    public function setFunded(bool $funded): self
    {
        $this->funded = $funded;

        return $this;
    }

    public function getPath(): ?Path
    {
        return $this->path;
    }

    public function setPath(?Path $path): self
    {
        $this->path = $path;

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

    /**
     * @return Collection|Session[]
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function getMonthSessions(string $yearAndMonth): Collection
    {
        return $this->sessions->filter(static function ($session) use ($yearAndMonth) {
            return $session->getStartAt() && $yearAndMonth === $session->getStartAt()->format('Y-m');
        });
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->setStudent($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            // set the owning side to null (unless already changed)
            if ($session->getStudent() === $this) {
                $session->setStudent(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }
}
