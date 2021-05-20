<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\MatchDetail;
use App\Repository\MatchDetailRepository;
use App\Service\Mapper\MatchMapper;
use App\Service\Validator\ValidatorMatch;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Exception;

class MatchManager
{

    private MatchMapper $matchMapper;
    private MatchDetailRepository $matchDetailRepository;
    public ValidatorMatch $validatorMatch;
    private EntityManagerInterface $entityManager;
    private MatchReader $matchReader;

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
        EntityManagerInterface $entityManager,
        MatchReader $matchReader)
    {
        $this->matchMapper = $matchMapper;
        $this->matchDetailRepository = $matchDetailRepository;
        $this->validatorMatch = $validatorMatch;
        $this->entityManager = $entityManager;
        $this->matchReader = $matchReader;
    }


    /**
     * @throws \JsonException
     */
    public function saveFromJsonToDB($json): array
    {
        $matchList = $this->matchMapper->mapJsonToArray($json);
        $matchList = $matchList['data'];

        foreach ($matchList as $match) {
            $matchDetail = $this->matchMapper->mapToMatchDetail($match);

            $matchFromDb = $this->matchDetailRepository->findOneBy(['matchId' => $matchDetail->getMatchId()]);

            if ($matchFromDb instanceof MatchDetail) {
                if ($this->validatorMatch->hasChanged($matchDetail, $matchFromDb)) {
                    $matchFromDb->setScoreTeam1($match['scoreTeam1']);
                    $matchFromDb->setScoreTeam2($match['scoreTeam2']);
                }
                $matchDetail = $matchFromDb;
            }
            $this->entityManager->persist($matchDetail);
        }
        $this->entityManager->flush();

        return $this->matchDetailRepository->findAll();
    }


    /**
     * @throws \Exception
     */
    public function save(MatchDetail $match): ?MatchDetail
    {

        $matchId = $match->getMatchId();

        if (!$matchId) {
            return null;
        }
        $this->entityManager->persist($match);
        $this->entityManager->flush();


        // return $this->matchDetailRepository->findOneBy(['matchId' => $matchId]);
        return $this->matchReader->getMatchWhereId($matchId);


        /*
            try {
                $this->entityManager->persist($match);
                $this->entityManager->flush();

            } catch (Exception $exception ) {
                throw new \Exception("There is no matchId in DB");
            }
            return $this->matchDetailRepository->findOneBy(['matchId' => $match->getMatchId()]);
        */
    }
}