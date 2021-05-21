<?php declare(strict_types=1);

namespace App\Service;

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

    /**
     * @throws \JsonException
     */
    public function getMatchListAsJson()
    {
        $matchList = $this->matchDetailRepository->findAll();
        $matchLisAsJson = $this->matchMapper->mapArrayToJsonWithDp($matchList, 'match');
        return json_encode($matchLisAsJson, JSON_THROW_ON_ERROR);
    }

}