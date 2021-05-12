<?php declare(strict_types=1);


use App\Entity\MatchDetail;
use App\Repository\MatchDetailRepository;
use App\Service\MatchManager;
use App\Service\MatchReader;
use App\Tests\Unit\Helper\MatchHelperTest;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MatchManagerTest extends KernelTestCase
{
    private MatchDetailRepository $matchDetailRepository;
    private MatchReader $matchReader;
    private MatchManager $matchManager;
    private MatchHelperTest $matchHelperTest;
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $matchDetailRepository = self::$container->get(MatchDetailRepository::class);
        $this->matchDetailRepository = $matchDetailRepository;

        $matchReader = self::$container->get(MatchReader::class);
        $this->matchReader = $matchReader;

        $matchManager = self::$container->get(MatchManager::class);
        $this->matchManager =  $matchManager;

        $this->matchHelperTest = new MatchHelperTest();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->matchHelperTest->deleteTemporaryMatch();

    }

    public function testSave(): void
    {
        $matchDetail = new MatchDetail();
        $matchDetail->setMatchId('2020-06-17:2100:FR-EN');
        $matchDetail->setTeam1('FR');
        $matchDetail->setTeam2('EN');
        $matchDetail->setMatchDateTime('2020-06-17 21:00');
        $matchDetail->setScoreTeam1(1);
        $matchDetail->setScoreTeam2(1);

        $actualValue = $this->matchManager->save($matchDetail);

        //$valueFromDatabase = $this->matchReader->getMatchWhereId($actualValue->getMatchId());
        $valueFromDatabase = $this->matchDetailRepository->findOneBy(['matchId' => $actualValue->getMatchId()]);

        self::assertSame('2020-06-17:2100:FR-EN', $valueFromDatabase->getMatchId());
        self::assertSame('FR', $valueFromDatabase->getTeam1());
        self::assertSame('EN', $valueFromDatabase->getTeam2());
        self::assertSame('2020-06-17 21:00', $valueFromDatabase->getMatchDateTime());
        self::assertSame(1, $valueFromDatabase->getScoreTeam1());
        self::assertSame(1, $valueFromDatabase->getScoreTeam2());

        $this->matchHelperTest->deleteTemporaryMatch();
    }


    public function testSaveFromJsonToDB(): void
    {
        $this->matchManager->saveFromJsonToDB($this->matchHelperTest->getJsonData());

        $matchFromDB = $this->matchReader->getMatchWhereId('2020-06-16:2100:FR-DE');

        //self::assertCount(2, $matchListFromDB);
        self::assertSame('2020-06-16:2100:FR-DE', $matchFromDB->getMatchId());
        self::assertSame('FR', $matchFromDB->getTeam1());
        self::assertSame('DE', $matchFromDB->getTeam2());
        self::assertSame('2020-06-16 21:00', $matchFromDB->getMatchDateTime());
        self::assertSame(1, $matchFromDB->getScoreTeam1());
        self::assertSame(0, $matchFromDB->getScoreTeam2());

        $matchFromDB = $this->matchReader->getMatchWhereId('2020-06-20:2000:PL-IT');

        self::assertSame('2020-06-20:2000:PL-IT', $matchFromDB->getMatchId());
        self::assertSame('PL', $matchFromDB->getTeam1());
        self::assertSame('IT', $matchFromDB->getTeam2());
        self::assertSame('2020-06-20 20:00', $matchFromDB->getMatchDateTime());
        self::assertNull($matchFromDB->getScoreTeam1());
        self::assertNull($matchFromDB->getScoreTeam2());

        $this->matchHelperTest->deleteTemporaryMatch();
    }


    public function testUpdateSaveFromJsonToDB(): void
    {
        $this->matchHelperTest->createTemporaryMatch();

        $this->matchManager->saveFromJsonToDB($this->matchHelperTest->getJsonDataUpdate());

        $matchFromDB = $this->matchReader->getMatchWhereId('2020-06-16:2100:FR-DE');

        //self::assertCount(2, $matchListFromDB);
        self::assertSame('2020-06-16:2100:FR-DE', $matchFromDB->getMatchId());
        self::assertSame('FR', $matchFromDB->getTeam1());
        self::assertSame('DE', $matchFromDB->getTeam2());
        self::assertSame('2020-06-16 21:00', $matchFromDB->getMatchDateTime());
        self::assertSame(1, $matchFromDB->getScoreTeam1());
        self::assertSame(0, $matchFromDB->getScoreTeam2());

        $matchFromDB = $this->matchReader->getMatchWhereId('2020-06-20:2000:PL-IT');

        self::assertSame('2020-06-20:2000:PL-IT', $matchFromDB->getMatchId());
        self::assertSame('PL', $matchFromDB->getTeam1());
        self::assertSame('IT', $matchFromDB->getTeam2());
        self::assertSame('2020-06-20 20:00', $matchFromDB->getMatchDateTime());
        self::assertSame(1, $matchFromDB->getScoreTeam1());
        self::assertSame(0, $matchFromDB->getScoreTeam2());

        $matchFromDB = $this->matchReader->getMatchWhereId('2020-06-19:2000:EN-IT');

        self::assertSame('2020-06-19:2000:EN-IT', $matchFromDB->getMatchId());
        self::assertSame('EN', $matchFromDB->getTeam1());
        self::assertSame('IT', $matchFromDB->getTeam2());
        self::assertSame('2020-06-19 20:00', $matchFromDB->getMatchDateTime());
        self::assertSame(2, $matchFromDB->getScoreTeam1());
        self::assertSame(0, $matchFromDB->getScoreTeam2());

        $this->matchHelperTest->deleteTemporaryMatch();
    }

}
