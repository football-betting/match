<?php declare(strict_types=1);


use App\Repository\MatchDetailRepository;
use App\Service\MatchManager;
use App\Service\MatchReader;
use App\Tests\Unit\Helper\MatchHelperTest;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @ORM\Embedded
 */
class MatchReaderTest extends KernelTestCase
{
    private MatchDetailRepository $matchDetailRepository;
    private MatchReader $matchReader;
    private MatchManager $matchManager;
    private MatchHelperTest $matchHelperTest;
    private EntityManagerInterface $entityManager;

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
        $this->matchManager = $matchManager;

        $this->matchHelperTest = new MatchHelperTest();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->matchHelperTest->deleteTemporaryMatch();
    }


    public function testGetMatchList(): void
    {
        $this->matchHelperTest->createTemporaryMatch();
        $matchListFromDB = $this->matchReader->getMatchList();

        self::assertCount(2, $matchListFromDB);
        self::assertSame('2020-06-16:2100:FR-DE',
            $this->matchReader->getMatchWhereId('2020-06-16:2100:FR-DE')->getMatchId());
        self::assertSame('FR',
            $this->matchReader->getMatchWhereId('2020-06-16:2100:FR-DE')->getTeam1());
        self::assertSame('DE',
            $this->matchReader->getMatchWhereId('2020-06-16:2100:FR-DE')->getTeam2());
        self::assertSame('2020-06-16 21:00',
            $this->matchReader->getMatchWhereId('2020-06-16:2100:FR-DE')->getMatchDateTime());
        self::assertSame(1,
            $this->matchReader->getMatchWhereId('2020-06-16:2100:FR-DE')->getScoreTeam1());
        self::assertSame(0,
            $this->matchReader->getMatchWhereId('2020-06-16:2100:FR-DE')->getScoreTeam2());

        $this->matchHelperTest->deleteTemporaryMatch();
    }


    public function testGetMatchWhereIdPositive(): void
    {
        $this->matchHelperTest->createTemporaryMatch();
        $matchFromDB = $this->matchReader->getMatchWhereId('2020-06-20:2000:PL-IT');

        self::assertSame('2020-06-20:2000:PL-IT', $matchFromDB->getMatchId());
        self::assertSame('PL', $matchFromDB->getTeam1());
        self::assertSame('IT', $matchFromDB->getTeam2());
        self::assertSame('2020-06-20 20:00', $matchFromDB->getMatchDateTime());
        self::assertNull($matchFromDB->getScoreTeam1());
        self::assertNull($matchFromDB->getScoreTeam2());

        $this->matchHelperTest->deleteTemporaryMatch();
    }

    public function testGetMatchWhereIdNegativ(): void
    {
        $this->matchHelperTest->createTemporaryMatch();
        $matchFromDB = $this->matchReader->getMatchWhereId('9999-99-99:9999:PL-IT');

        self::assertNull($matchFromDB);

        $this->matchHelperTest->deleteTemporaryMatch();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws JsonException
     */
    public function testDataProviderPositive(): void
    {
        $this->matchManager->saveFromJsonToDB($this->matchHelperTest->getJsonData());

        $dataOriginal = json_decode($this->matchHelperTest->getJsonData(), true, 512, JSON_THROW_ON_ERROR);
        $dataFromDb = json_decode($this->matchReader->getMatchListAsJsonDataProvider(), true, 512, JSON_THROW_ON_ERROR);

        $this->compareArrays($dataOriginal, $dataFromDb);

        $this->matchHelperTest->deleteTemporaryMatch();
    }

    private function compareArrays(array $arrayGiven, array $arrayExpected): void
    {
        self::assertSame(count(array_diff_key($arrayGiven, $arrayExpected)), 0);

        foreach ($arrayGiven as $key => $value)
        {
           // self::assertSame($key, array_search($value, $arrayExpected, true)); //null jest podwujnie

            self::assertArrayHasKey($key, $arrayExpected);

            if(is_array($value)) // assertIsArray()
            {
                self::compareArrays($value, $arrayExpected[$key]);
            }
            self::assertSame($value, $arrayExpected[$key]);
        }
    }
}

