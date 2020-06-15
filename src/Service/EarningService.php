<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\MeetingInterface;
use App\Entity\Session;
use App\Entity\Student;
use App\Repository\EvaluationRepository;
use App\Repository\StudentRepository;

/**
 * Class EarningService.
 */
class EarningService
{
    private const EARN_BY_RATE = [0 => 0, 1 => 30, 2 => 35, 3 => 40];
    private const BASE_BONUS = 30;

    /**
     * @var StudentRepository
     */
    private $studentRepository;

    /**
     * @var EvaluationRepository
     */
    private $evaluationRepository;

    /**
     * EarningService constructor.
     */
    public function __construct(StudentRepository $studentRepository, EvaluationRepository $evaluationRepository)
    {
        $this->studentRepository = $studentRepository;
        $this->evaluationRepository = $evaluationRepository;
    }

    public function getExpectedBonus(): float
    {
        $total = 0;

        foreach ($this->studentRepository->findAll() as $student) {
            if ($student->isFunded()) {
                continue;
            }

            $total += self::BASE_BONUS;
        }

        return (float) $total;
    }

    public function getEarnsForMeetings(array $meetings): float
    {
        $totalEarning = 0;

        foreach ($meetings as $meeting) {
            $totalEarning += $this->getEarnsForMeeting($meeting);
        }

        return (float) $totalEarning;
    }

    public function getExpectedEarnsForMonth(string $yearAndMonth): float
    {
        $totalEarning = 0;

        foreach ($this->studentRepository->findAll() as $student) {
            $totalEarning += $this->getExpectedTotalEarnsForStudent($yearAndMonth, $student);
        }

        foreach ($this->evaluationRepository->findByMonth($yearAndMonth) as $evaluation) {
            $totalEarning += $this->getEarnsForMeeting($evaluation);
        }

        return (float) $totalEarning;
    }

    private function getExpectedTotalEarnsForStudent(string $yearAndMonth, Student $student): float
    {
        $sessionsRemaining = Session::SESSIONS_BY_MONTH;

        $total = 0;
        foreach ($student->getMonthSessions($yearAndMonth) as $monthSession) {
            $total += $this->getEarnsForMeeting($monthSession);
            --$sessionsRemaining;
        }

        $total += $this->getEarnsForStudent($student) * $sessionsRemaining;

        return (float) $total;
    }

    private function getEarnsForMeeting(MeetingInterface $meeting): float
    {
        $earn = self::EARN_BY_RATE[$meeting->getRate()] ?? 0;

        if ($meeting instanceof Session && $meeting->getStudent() && !$meeting->getStudent()->isFunded()) {
            $earn /= 2;
        }

        if ($meeting->isNoShow()) {
            $earn /= 2;
        }

        return (float) $earn;
    }

    private function getEarnsForStudent(Student $student): float
    {
        if (!$project = $student->getProject()) {
            return 0;
        }

        $earn = self::EARN_BY_RATE[$project->getRate()] ?? 0;

        if (!$student->isFunded()) {
            $earn /= 2;
        }

        return (float) $earn;
    }
}
