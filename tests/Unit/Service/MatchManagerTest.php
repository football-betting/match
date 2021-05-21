<?php declare(strict_types=1);


use App\DataFixtures\MatchFixtures;
use App\Repository\MatchDetailRepository;
use App\Service\MatchManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MatchManagerTest extends KernelTestCase
{
    private MatchDetailRepository $matchDetailRepository;
    private MatchManager $matchManager;
    private MatchFixtures $matchFixtures;
    private $entityManager;

    /**
     * @throws JsonException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $matchDetailRepository = self::$container->get(MatchDetailRepository::class);
        $this->matchDetailRepository = $matchDetailRepository;

        $matchManager = self::$container->get(MatchManager::class);
        $this->matchManager = $matchManager;

        $matchFixtures = self::$container->get(MatchFixtures::class);
        $this->matchFixtures = $matchFixtures;

        $this->matchFixtures->load($this->entityManager);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    protected function tearDown(): void
    {
        $this->matchFixtures->truncateTable($this->entityManager);
        parent::tearDown();
    }


    public function testSaveFromJsonToDB(): void
    {

        $matchFromDB = $this->matchDetailRepository->find('2020-06-16:2100:FR-DE');

        self::assertSame('2020-06-16:2100:FR-DE', $matchFromDB->getMatchId());
        self::assertSame('FR', $matchFromDB->getTeam1());
        self::assertSame('DE', $matchFromDB->getTeam2());
        self::assertSame('2020-06-16 21:00', $matchFromDB->getMatchDateTime());
        self::assertSame(1, $matchFromDB->getScoreTeam1());
        self::assertSame(0, $matchFromDB->getScoreTeam2());

        $matchFromDB = $this->matchDetailRepository->find('2020-06-20:2000:PL-IT');

        self::assertSame('2020-06-20:2000:PL-IT', $matchFromDB->getMatchId());
        self::assertSame('PL', $matchFromDB->getTeam1());
        self::assertSame('IT', $matchFromDB->getTeam2());
        self::assertSame('2020-06-20 20:00', $matchFromDB->getMatchDateTime());
        self::assertNull($matchFromDB->getScoreTeam1());
        self::assertNull($matchFromDB->getScoreTeam2());
    }


    public function testUpdateSaveFromJsonToDB(): void
    {
        self::assertCount(2, $this->matchDetailRepository->findAll());

        $this->matchManager->saveFromJsonToDB($this->matchFixtures->getJsonDataUpdate());

        self::assertCount(3, $this->matchDetailRepository->findAll());


        $matchFromDB = $this->matchDetailRepository->find('2020-06-16:2100:FR-DE');

        self::assertSame('2020-06-16:2100:FR-DE', $matchFromDB->getMatchId());
        self::assertSame('FR', $matchFromDB->getTeam1());
        self::assertSame('DE', $matchFromDB->getTeam2());
        self::assertSame('2020-06-16 21:00', $matchFromDB->getMatchDateTime());
        self::assertSame(1, $matchFromDB->getScoreTeam1());
        self::assertSame(0, $matchFromDB->getScoreTeam2());

        $matchFromDB = $this->matchDetailRepository->find('2020-06-20:2000:PL-IT');

        self::assertSame('2020-06-20:2000:PL-IT', $matchFromDB->getMatchId());
        self::assertSame('PL', $matchFromDB->getTeam1());
        self::assertSame('IT', $matchFromDB->getTeam2());
        self::assertSame('2020-06-20 20:00', $matchFromDB->getMatchDateTime());
        self::assertSame(1, $matchFromDB->getScoreTeam1());
        self::assertSame(0, $matchFromDB->getScoreTeam2());

        $matchFromDB = $this->matchDetailRepository->find('2020-06-19:2000:EN-IT');

        self::assertSame('2020-06-19:2000:EN-IT', $matchFromDB->getMatchId());
        self::assertSame('EN', $matchFromDB->getTeam1());
        self::assertSame('IT', $matchFromDB->getTeam2());
        self::assertSame('2020-06-19 20:00', $matchFromDB->getMatchDateTime());
        self::assertSame(2, $matchFromDB->getScoreTeam1());
        self::assertSame(0, $matchFromDB->getScoreTeam2());
    }

}
