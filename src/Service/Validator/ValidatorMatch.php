<?php declare(strict_types=1);

namespace App\Service\Validator;

use App\Entity\MatchDetail;

class ValidatorMatch
{
    public function hasChanged( MatchDetail $matchFromDb, array $matchDetailReceived): bool
    {
        return $matchFromDb->getScoreTeam1() !== $matchDetailReceived['scoreTeam1'] ||
            $matchFromDb->getScoreTeam2() !== $matchDetailReceived['scoreTeam2'] ;
    }
}