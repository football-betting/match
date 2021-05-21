<?php declare(strict_types=1);

namespace App\Tests\Unit\Helper;

use App\Entity\MatchDetail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MatchHelperTest extends KernelTestCase
{

    public const MATCH = [
        [
            "matchId" => "2020-06-16:2100:FR-DE",
            "team1" => "FR",
            "team2" => "DE",
            "matchDatetime" => "2020-06-16 21:00",
            "scoreTeam1" => 1,
            "scoreTeam2" => 0
        ],
        [
            "matchId" => "2020-06-20:2000:PL-IT",
            "team1" => "PL",
            "team2" => "IT",
            "matchDatetime" => "2020-06-20 20:00",
            "scoreTeam1" => null,
            "scoreTeam2" => null
        ]
    ];

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function createTemporaryMatch(): array
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $matchEntityList = [];

        foreach (self::MATCH as $match) {
            $matchEntity = new MatchDetail();
            $matchEntity->setMatchId($match['matchId']);
            $matchEntity->setMatchDateTime($match['matchDatetime']);
            $matchEntity->setTeam1($match['team1']);
            $matchEntity->setTeam2($match['team2']);
            $matchEntity->setScoreTeam1($match['scoreTeam1']);
            $matchEntity->setScoreTeam2($match['scoreTeam2']);

            $this->entityManager->persist($matchEntity);
            $this->entityManager->flush();
        }
        return $matchEntityList;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteTemporaryMatch(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $connection = $this->entityManager->getConnection();

        $connection->executeUpdate('DELETE FROM match_detail');
        $connection->executeUpdate('ALTER TABLE match_detail AUTO_INCREMENT=0');

    }

    public function getJsonData()
    {
        return '{
        "event":"match",
                "data":[
                    {
                        "matchId":"2020-06-16:2100:FR-DE",
                        "team1":"FR",
                        "team2":"DE",
                        "matchDatetime":"2020-06-16 21:00",
                        "scoreTeam1":1,
                        "scoreTeam2":0
                        },
                        {
                        "matchId":"2020-06-20:2000:PL-IT",
                        "team1":"PL",
                        "team2":"IT",
                        "matchDatetime":"2020-06-20 20:00",
                        "scoreTeam1":null,
                        "scoreTeam2":null
                        }
                        ]
                        }';

    }


    public function getJsonDataUpdate()
    {
        return '{
        "event":"match",
                "data":[
                    {
                        "matchId":"2020-06-16:2100:FR-DE",
                        "team1":"FR",
                        "team2":"DE",
                        "matchDatetime":"2020-06-16 21:00",
                        "scoreTeam1":1,
                        "scoreTeam2":0
                     },
                    {
                        "matchId":"2020-06-20:2000:PL-IT",
                        "team1":"PL",
                        "team2":"IT",
                        "matchDatetime":"2020-06-20 20:00",
                        "scoreTeam1": 1,
                        "scoreTeam2": 0
                    },
                    {
                        "matchId":"2020-06-19:2000:EN-IT",
                        "team1":"EN",
                        "team2":"IT",
                        "matchDatetime":"2020-06-19 20:00",
                        "scoreTeam1": 2,
                        "scoreTeam2": 0
                    }
                ]
            }';
    }


    public function test(): void
    {
        self::assertTrue(true);
    }

}
