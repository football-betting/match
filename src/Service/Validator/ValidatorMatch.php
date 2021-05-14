<?php declare(strict_types=1);

namespace App\Service\Validator;

use App\Entity\MatchDetail;

class ValidatorMatch
{
    public function hasChanged(MatchDetail $matchDetailReceived, MatchDetail $matchFromDb): bool
    {
        return $matchFromDb->getScoreTeam1() !== $matchDetailReceived->getScoreTeam1() ||
            $matchFromDb->getScoreTeam2() !== $matchDetailReceived->getScoreTeam2();
    }
}