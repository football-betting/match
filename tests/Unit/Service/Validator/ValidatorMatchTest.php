<?php declare(strict_types=1);

use App\DataFixtures\MatchFixtures;
use App\Entity\MatchDetail;
use App\Service\Validator\ValidatorMatch;
use App\Tests\Unit\Helper\MatchHelperTest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ValidatorMatchTest extends KernelTestCase
{
    private ValidatorMatch $validatorMatch;
    private MatchFixtures $matchFixtures;
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $validatorMatch = self::$container->get(ValidatorMatch::class);
        $this->validatorMatch = $validatorMatch;

        $matchFixtures = self::$container->get(MatchFixtures::class);
        $this->matchFixtures = $matchFixtures;

        $this->matchFixtures->load($this->entityManager);
    }

    protected function tearDown(): void
    {
        $this->matchFixtures->truncateTable($this->entityManager);
        parent::tearDown();
    }

    public function testValidHasChanged(): void
    {
        $matchDetailOne = new MatchDetail();
        $matchDetailTwo = [];

        $matchDetailOne->setScoreTeam1(1);
        $matchDetailOne->setScoreTeam2(1);

        $matchDetailTwo['scoreTeam1'] = 9;
        $matchDetailTwo['scoreTeam2'] = 1;

        self::assertTrue($this->validatorMatch->hasChanged($matchDetailOne, $matchDetailTwo));

        $matchDetailOne->setScoreTeam1(1);
        $matchDetailOne->setScoreTeam2(1);


        $matchDetailTwo['scoreTeam1'] = 1;
        $matchDetailTwo['scoreTeam2'] = 9;

        self::assertTrue($this->validatorMatch->hasChanged($matchDetailOne, $matchDetailTwo));

        $matchDetailOne->setScoreTeam1(1);
        $matchDetailOne->setScoreTeam2(1);

        $matchDetailTwo['scoreTeam1'] = 9;
        $matchDetailTwo['scoreTeam2'] = 9;

        self::assertTrue($this->validatorMatch->hasChanged($matchDetailOne, $matchDetailTwo));

    }

    public function testNotValidHasChanged(): void
    {
        $matchDetailOne = new MatchDetail();
        $matchDetailTwo = [];

        $matchDetailOne->setScoreTeam1(1);
        $matchDetailOne->setScoreTeam2(1);

        $matchDetailTwo['scoreTeam1'] = 1;
        $matchDetailTwo['scoreTeam2'] = 1;

        self::assertFalse($this->validatorMatch->hasChanged($matchDetailOne, $matchDetailTwo));
    }
}
