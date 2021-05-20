<?php declare(strict_types=1);

use App\Entity\MatchDetail;
use App\Repository\MatchDetailRepository;
use App\Service\MatchManager;
use App\Service\MatchReader;
use App\Service\Validator\ValidatorMatch;
use App\Tests\Unit\Helper\MatchHelperTest;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ValidatorMatchTest extends KernelTestCase
{
    private MatchDetailRepository $matchDetailRepository;
    private ValidatorMatch $validatorMatch;
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

        $validatorMatch = self::$container->get(ValidatorMatch::class);
        $this->validatorMatch = $validatorMatch;

        $this->matchHelperTest = new MatchHelperTest();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->matchHelperTest->deleteTemporaryMatch();
    }

    public function testValidHasChanged(): void
    {
        $matchDetailOne = new MatchDetail();
        $matchDetailTwo = new MatchDetail();

        $matchDetailOne->setScoreTeam1(1);
        $matchDetailOne->setScoreTeam2(1);

        $matchDetailTwo->setScoreTeam1(9);
        $matchDetailTwo->setScoreTeam2(1);

        self::assertTrue($this->validatorMatch->hasChanged($matchDetailOne, $matchDetailTwo));

        $matchDetailOne->setScoreTeam1(1);
        $matchDetailOne->setScoreTeam2(1);

        $matchDetailTwo->setScoreTeam1(1);
        $matchDetailTwo->setScoreTeam2(9);

        self::assertTrue($this->validatorMatch->hasChanged($matchDetailOne, $matchDetailTwo));

        $matchDetailOne->setScoreTeam1(1);
        $matchDetailOne->setScoreTeam2(1);

        $matchDetailTwo->setScoreTeam1(9);
        $matchDetailTwo->setScoreTeam2(9);

        self::assertTrue($this->validatorMatch->hasChanged($matchDetailOne, $matchDetailTwo));

    }

    public function testNotValidHasChanged(): void
    {
        $matchDetailOne = new MatchDetail();
        $matchDetailTwo = new MatchDetail();

        $matchDetailOne->setScoreTeam1(1);
        $matchDetailOne->setScoreTeam2(1);

        $matchDetailTwo->setScoreTeam1(1);
        $matchDetailTwo->setScoreTeam2(1);

        self::assertFalse($this->validatorMatch->hasChanged($matchDetailOne, $matchDetailTwo));
    }

}
