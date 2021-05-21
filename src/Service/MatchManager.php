<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\MatchDetail;
use App\Repository\MatchDetailRepository;
use App\Service\Mapper\MatchMapper;
use App\Service\Validator\ValidatorMatch;
use Doctrine\ORM\EntityManagerInterface;

class MatchManager
{

    private const JSON_DEPTH = 512;

    private MatchMapper $matchMapper;
    private MatchDetailRepository $matchDetailRepository;
    public ValidatorMatch $validatorMatch;
    private EntityManagerInterface $entityManager;

    /**
     * MatchManager constructor.
     * @param MatchMapper $matchMapper
     * @param MatchDetailRepository $matchDetailRepository
     * @param ValidatorMatch $validatorMatch
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        MatchMapper $matchMapper,
        MatchDetailRepository $matchDetailRepository,
        ValidatorMatch $validatorMatch,
        EntityManagerInterface $entityManager
    )
    {
        $this->matchMapper = $matchMapper;
        $this->matchDetailRepository = $matchDetailRepository;
        $this->validatorMatch = $validatorMatch;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws \JsonException
     */
    public function saveFromJsonToDB($json): void
    {
        $matchList = json_decode($json, true, self::JSON_DEPTH, JSON_THROW_ON_ERROR);;
        $matchList = $matchList['data'];

        foreach ($matchList as $match) {
            $matchDetail = $this->matchDetailRepository->find($match['matchId']); //co jak nie znajdzie ID?
            if (!$matchDetail instanceof MatchDetail) {
                $matchDetail = $this->matchMapper->mapToMatchDetail($match);
                $this->entityManager->persist($matchDetail);

            } elseif ($this->validatorMatch->hasChanged($matchDetail, $match)) {
                $matchDetail->setScoreTeam1($match['scoreTeam1']);
                $matchDetail->setScoreTeam2($match['scoreTeam2']);
                $this->entityManager->persist($matchDetail);
            }
        }
        $this->entityManager->flush();
        // return $this->matchDetailRepository->findAll();
    }
}