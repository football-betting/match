<?php declare(strict_types=1);

namespace App\Service\Mapper;

use App\DataTransferObject\MatchDetailDataProvider;
use App\DataTransferObject\MatchListDataProvider;
use App\Entity\MatchDetail;

class MatchMapper
{
    private const JSON_DEPTH = 512;

    public function mapJsonToArray($json): array
    {
        return json_decode($json, true, self::JSON_DEPTH, JSON_THROW_ON_ERROR);
    }


    public function mapArrayToJson(array $matchList)
    {
        return json_encode($matchList, JSON_THROW_ON_ERROR);
    }


    public function mapToMatchDetail(array $match): MatchDetail
    {
        $matchDetail = new MatchDetail();
        $matchDetail->setMatchId($match['matchId']);
        $matchDetail->setTeam1($match['team1']);
        $matchDetail->setTeam2($match['team2']);
        $matchDetail->setMatchDateTime($match['matchDatetime']);
        $matchDetail->setScoreTeam1($match['scoreTeam1']);
        $matchDetail->setScoreTeam2($match['scoreTeam2']);

        return $matchDetail;
    }

    private function mapToMatchDetailDataProvider(MatchDetail $matchDetail): MatchDetailDataProvider
    {
        $matchDto = new MatchDetailDataProvider();
        $matchDto->setMatchId($matchDetail->getMatchId());
        $matchDto->setTeam1($matchDetail->getTeam1());
        $matchDto->setTeam2($matchDetail->getTeam2());
        $matchDto->setMatchDateTime($matchDetail->getMatchDateTime());
        $matchDto->setScoreTeam1($matchDetail->getScoreTeam1());
        $matchDto->setScoreTeam2($matchDetail->getScoreTeam2());

        return $matchDto;
    }


    private function setNullValue(array $arrayGiven, string $field): array
    {
        foreach ($arrayGiven as $key => $item) {
            if (!isset($item[$field])) {
                $arrayGiven[$key][$field] = null;
            }
        }
        return $arrayGiven;
    }


    public function mapArrayToJsonWithDp(array $matchListFromDb, string $event): array
    {
        $matchListDto = new MatchListDataProvider();
        $matchListTemp = [];
        $matchListDto->setEvent($event);

        foreach ($matchListFromDb as $match) {
            $matchDto = $this->mapToMatchDetailDataProvider($match);
            $matchListTemp[] = $matchDto;
        }
        $matchListDto->setData($matchListTemp);
        $matchListDtoArray = $matchListDto->toArray();

        $matchListDtoArray['data'] = $this->setNullValue($matchListDtoArray['data'], 'scoreTeam1');
        $matchListDtoArray['data'] = $this->setNullValue($matchListDtoArray['data'], 'scoreTeam2');

        return $matchListDtoArray;
    }

}