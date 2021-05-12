<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\MatchDetail;
use App\Repository\MatchDetailRepository;
use App\Service\Mapper\MatchMapper;
use Doctrine\ORM\EntityManagerInterface;

class MatchReader
{
    private MatchDetailRepository $matchDetailRepository;
    private MatchMapper $matchMapper;
    private EntityManagerInterface $entityManager;


    /**
     * MatchReader constructor.
     * @param EntityManagerInterface $entityManager
     * @param MatchDetailRepository $matchDetailRepository
     * @param MatchMapper $matchMapper
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        MatchDetailRepository $matchDetailRepository,
        MatchMapper $matchMapper)
    {
        $this->entityManager = $entityManager;
        $this->matchDetailRepository = $matchDetailRepository;
        $this->matchMapper = $matchMapper;
    }


    public function getMatchList(): array
    {
        return $this->matchDetailRepository->findAll();
    }

    public function getMatchListAsJson()
    {
        $matchList = $this->matchDetailRepository->findAll();
        return $this->matchMapper->mapArrayToJson($matchList);
    }


    public function getMatchWhereId(string $id): ?MatchDetail
    {
        $match = $this->matchDetailRepository->findOneBy(['matchId' => $id]);

        if (!$match)
        {
            return null;
        }
        return $match;
    }
}