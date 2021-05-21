<?php declare(strict_types=1);

use App\DataFixtures\MatchFixtures;
use App\Repository\MatchDetailRepository;
use App\Service\MatchManager;
use App\Service\MatchReader;
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
    private MatchFixtures $matchFixtures;
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

        $matchFixtures = self::$container->get(MatchFixtures::class);
        $this->matchFixtures = $matchFixtures;

        $this->matchFixtures->load($this->entityManager);
    }

    protected function tearDown(): void
    {
        $this->matchFixtures->truncateTable($this->entityManager);
        parent::tearDown();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws JsonException
     */
    public function testGetMatchListAsJson(): void
    {
        $dataOriginal = json_decode($this->matchFixtures->getJsonData(), true, 512, JSON_THROW_ON_ERROR);
        $dataFromDb = json_decode($this->matchReader->getMatchListAsJson(), true, 512, JSON_THROW_ON_ERROR);

        $this->compareArrays($dataOriginal, $dataFromDb);

        self::assertJsonStringEqualsJsonString($this->matchFixtures->getJsonData(),
            $this->matchReader->getMatchListAsJson());
    }


    private function compareArrays(array $arrayGiven, array $arrayExpected): void
    {
        self::assertSame(count(array_diff_key($arrayGiven, $arrayExpected)), 0);
        self::assertSame(array_keys($arrayGiven), array_keys($arrayExpected));

        foreach ($arrayGiven as $key => $value) {
            // self::assertSame($key, array_search($value, $arrayExpected, true)); //null jest podwujnie

            self::assertArrayHasKey($key, $arrayExpected);

            if (is_array($value)) // assertIsArray()
            {
                self::compareArrays($value, $arrayExpected[$key]);
            }
            self::assertSame($value, $arrayExpected[$key]);
        }
    }
}

