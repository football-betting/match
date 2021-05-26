<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Service\MatchManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MatchFixtures extends Fixture
{

    private MatchManager $matchManager;

    /**
     * MatchFixtures constructor.
     * @param MatchManager $matchManager
     */
    public function __construct(MatchManager $matchManager)
    {
        $this->matchManager = $matchManager;

    }

    /**
     * @throws \JsonException
     */
    public function load(ObjectManager $manager): void
    {
        $this->truncateTable($manager);
        $this->matchManager->saveFromJsonToDB($this->getJsonData());
    }

    /**
     * @param ObjectManager $manager
     */
    public function truncateTable(ObjectManager $manager): void
    {
        $connection = $manager->getConnection();
        $platform = $connection->getDatabasePlatform();

        $connection->executeUpdate($platform->getTruncateTableSQL('match_detail'));
        $manager->clear();
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
}