<?php

namespace App\Entity;

use App\Repository\MatchDetailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MatchDetailRepository::class)
 */
class MatchDetail
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $matchId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $matchDateTime;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $team1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $team2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoreTeam1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoreTeam2;


    public function getMatchId(): ?string
    {
        return $this->matchId;
    }

    public function setMatchId(string $matchId): self
    {
        $this->matchId = $matchId;

        return $this;
    }

    public function getMatchDateTime(): ?string
    {
        return $this->matchDateTime;
    }

    public function setMatchDateTime(string $matchDateTime): self
    {
        $this->matchDateTime = $matchDateTime;

        return $this;
    }

    public function getTeam1(): ?string
    {
        return $this->team1;
    }

    public function setTeam1(string $team1): self
    {
        $this->team1 = $team1;

        return $this;
    }

    public function getTeam2(): ?string
    {
        return $this->team2;
    }

    public function setTeam2(string $team2): self
    {
        $this->team2 = $team2;

        return $this;
    }

    public function getScoreTeam1(): ?int
    {
        return $this->scoreTeam1;
    }

    public function setScoreTeam1(?int $scoreTeam1): self
    {
        $this->scoreTeam1 = $scoreTeam1;

        return $this;
    }

    public function getScoreTeam2(): ?int
    {
        return $this->scoreTeam2;
    }

    public function setScoreTeam2(?int $scoreTeam2): self
    {
        $this->scoreTeam2 = $scoreTeam2;

        return $this;
    }
}
