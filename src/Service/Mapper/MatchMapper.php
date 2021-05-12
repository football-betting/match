<?php declare(strict_types=1);

namespace App\Service\Mapper;

use App\Entity\MatchDetail;

class MatchMapper
{
    public function mapJsonToArray($json): array
    {
        $matchList[] = json_decode($json, true);

        return $matchList;
    }

    public function mapArrayToJson(array $matchList)
    {
        return json_encode($matchList);
    }


    public function mapToMatchDetail(array $match): MatchDetail
    {
        $matchDetail = new MatchDetail();
        $matchDetail->setMatchId($match['matchId']);
        $matchDetail->setMatchDateTime($match['matchDatetime']);
        $matchDetail->setTeam1($match['team1']);
        $matchDetail->setTeam2($match['team2']);
        $matchDetail->setScoreTeam1($match['scoreTeam1']);
        $matchDetail->setScoreTeam2($match['scoreTeam2']);

        return $matchDetail;
    }

}