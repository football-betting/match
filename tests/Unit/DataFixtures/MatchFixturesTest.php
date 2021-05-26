<?php declare(strict_types=1);

use App\DataFixtures\MatchFixtures;
use App\Repository\MatchDetailRepository;
use App\Service\MatchManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MatchFixturesTest extends KernelTestCase
{
    private MatchDetailRepository $matchDetailRepository;
    private MatchManager $matchManager;
    private MatchFixtures $matchFixtures;
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

        $matchManager = self::$container->get(MatchManager::class);
        $this->matchManager = $matchManager;

        $matchFixtures = self::$container->get(MatchFixtures::class);
        $this->matchFixtures = $matchFixtures;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @throws JsonException
     */
    public function testLoad(): void
    {
        $this->matchFixtures->truncateTable($this->entityManager);
        self::assertCount(0, $this->matchDetailRepository->findAll());

        $this->matchFixtures->load($this->entityManager);
        self::assertCount(2, $this->matchDetailRepository->findAll());


        $this->matchFixtures->load($this->entityManager);
        self::assertCount(2, $this->matchDetailRepository->findAll());


        $this->matchManager->saveFromJsonToDB($this->matchFixtures->getJsonDataUpdate());
        self::assertCount(3, $this->matchDetailRepository->findAll());
    }

    /**
     * @throws JsonException
     */
    public function testOnlyLoad(): void
    {
        $this->matchFixtures->truncateTable($this->entityManager);
        $this->matchManager->saveFromJsonToDB($this->matchFixtures->getJsonData());
        self::assertCount(2, $this->matchDetailRepository->findAll());

        $this->matchManager->saveFromJsonToDB($this->matchFixtures->getJsonData());
        self::assertCount(2, $this->matchDetailRepository->findAll());

        $this->matchManager->saveFromJsonToDB($this->matchFixtures->getJsonDataUpdate());
        self::assertCount(3, $this->matchDetailRepository->findAll());

        $this->matchFixtures->truncateTable($this->entityManager);
        $this->matchManager->saveFromJsonToDB($this->matchFixtures->getJsonDataUpdate());
        self::assertCount(3, $this->matchDetailRepository->findAll());


    }
}
