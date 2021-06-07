<?php declare(strict_types=1);

namespace App\Tests\Acceptance\Messenger;

use App\DataTransferObject\MatchDetailDataProvider;
use App\Messenger\MatchMessageHandler;
use App\Service\Redis\RedisService;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MatchMessageHandlerTest extends KernelTestCase
{
    private ?MatchMessageHandler $handler;
    private ?Connection $entityManager;

    private ?RedisService $redis;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->entityManager = self::$container
            ->get('doctrine.dbal.default_connection');


        $this->handler = static::$container->get(\App\Messenger\MatchMessageHandler::class);
        $this->redis = static::$container->get(RedisService::class);

    }

    protected function tearDown(): void
    {
        $this->deleteAllMessage();
        $this->redis->deleteAll();

        $this->entityManager->close();
        $this->entityManager = null;

        parent::tearDown();
    }

    public function testFirstMatch()
    {
        $handler = $this->handler;

        $matchDetailDataProvider = new MatchDetailDataProvider();
        $matchDetailDataProvider->setMatchId('2020-06-16:2100-FR-DE');
        $matchDetailDataProvider->setMatchDatetime('2020-06-16 21:00');
        $matchDetailDataProvider->setTeam1('FR');
        $matchDetailDataProvider->setTeam2('DE');

        $handler($matchDetailDataProvider);

        $message = $this->getMessageInfo();

        self::assertCount(1, $message);
        self::assertSame('match.to.calculation', $message[0]['queue_name']);

        $checkMatchDetailDataProvider = new MatchDetailDataProvider();
        $checkMatchDetailDataProvider->fromArray(\Safe\json_decode($message[0]['body'], true)['data'][0]);

        self::assertSame($matchDetailDataProvider->getMatchId(), $checkMatchDetailDataProvider->getMatchId());
        self::assertSame($matchDetailDataProvider->getMatchDatetime(), $checkMatchDetailDataProvider->getMatchDatetime());
        self::assertSame($matchDetailDataProvider->getScoreTeam1(), $checkMatchDetailDataProvider->getScoreTeam1());
        self::assertSame($matchDetailDataProvider->getScoreTeam2(), $checkMatchDetailDataProvider->getScoreTeam2());
        self::assertSame($matchDetailDataProvider->getTeam1(), $checkMatchDetailDataProvider->getTeam1());
        self::assertSame($matchDetailDataProvider->getTeam2(), $checkMatchDetailDataProvider->getTeam2());

        $this->deleteAllMessage();

        $matchDetailDataProvider2 = new MatchDetailDataProvider();
        $matchDetailDataProvider2->setMatchId('2020-16-12:2100-PL-EN');
        $matchDetailDataProvider2->setMatchDatetime('2020-16-12:2100');
        $matchDetailDataProvider2->setTeam1('PL');
        $matchDetailDataProvider2->setTeam2('EN');

        $handler($matchDetailDataProvider2);

        $message = $this->getMessageInfo();

        self::assertCount(1, $message);

        $checkMatchDetailDataProvider = new MatchDetailDataProvider();
        $checkMatchDetailDataProvider->fromArray(\Safe\json_decode($message[0]['body'], true)['data'][1]);

        self::assertSame($matchDetailDataProvider2->getMatchId(), $checkMatchDetailDataProvider->getMatchId());
        self::assertSame($matchDetailDataProvider2->getMatchDatetime(), $checkMatchDetailDataProvider->getMatchDatetime());
        self::assertSame($matchDetailDataProvider2->getScoreTeam1(), $checkMatchDetailDataProvider->getScoreTeam1());
        self::assertSame($matchDetailDataProvider2->getScoreTeam2(), $checkMatchDetailDataProvider->getScoreTeam2());
        self::assertSame($matchDetailDataProvider2->getTeam1(), $checkMatchDetailDataProvider->getTeam1());
        self::assertSame($matchDetailDataProvider2->getTeam2(), $checkMatchDetailDataProvider->getTeam2());

        $checkMatchDetailDataProvider = new MatchDetailDataProvider();
        $checkMatchDetailDataProvider->fromArray(\Safe\json_decode($message[0]['body'], true)['data'][0]);

        self::assertSame($matchDetailDataProvider->getMatchId(), $checkMatchDetailDataProvider->getMatchId());
        self::assertSame($matchDetailDataProvider->getMatchDatetime(), $checkMatchDetailDataProvider->getMatchDatetime());
        self::assertSame($matchDetailDataProvider->getScoreTeam1(), $checkMatchDetailDataProvider->getScoreTeam1());
        self::assertSame($matchDetailDataProvider->getScoreTeam2(), $checkMatchDetailDataProvider->getScoreTeam2());
        self::assertSame($matchDetailDataProvider->getTeam1(), $checkMatchDetailDataProvider->getTeam1());
        self::assertSame($matchDetailDataProvider->getTeam2(), $checkMatchDetailDataProvider->getTeam2());

        $this->deleteAllMessage();

        $handler($matchDetailDataProvider2);

        $message = $this->getMessageInfo();

        self::assertCount(0, $message);

        $matchDetailDataProvider = new MatchDetailDataProvider();
        $matchDetailDataProvider->setMatchId('2020-06-16:2100-FR-DE');
        $matchDetailDataProvider->setMatchDatetime('2020-06-16 21:00');
        $matchDetailDataProvider->setTeam1('FR');
        $matchDetailDataProvider->setTeam2('DE');
        $matchDetailDataProvider->setScoreTeam1(1);
        $matchDetailDataProvider->setScoreTeam2(0);

        $handler($matchDetailDataProvider);

        $message = $this->getMessageInfo();

        self::assertCount(1, $message);

        $checkMatchDetailDataProvider = new MatchDetailDataProvider();
        $checkMatchDetailDataProvider->fromArray(\Safe\json_decode($message[0]['body'], true)['data'][1]);

        self::assertSame($matchDetailDataProvider2->getMatchId(), $checkMatchDetailDataProvider->getMatchId());
        self::assertSame($matchDetailDataProvider2->getMatchDatetime(), $checkMatchDetailDataProvider->getMatchDatetime());
        self::assertSame($matchDetailDataProvider2->getScoreTeam1(), $checkMatchDetailDataProvider->getScoreTeam1());
        self::assertSame($matchDetailDataProvider2->getScoreTeam2(), $checkMatchDetailDataProvider->getScoreTeam2());
        self::assertSame($matchDetailDataProvider2->getTeam1(), $checkMatchDetailDataProvider->getTeam1());
        self::assertSame($matchDetailDataProvider2->getTeam2(), $checkMatchDetailDataProvider->getTeam2());

        $checkMatchDetailDataProvider = new MatchDetailDataProvider();
        $checkMatchDetailDataProvider->fromArray(\Safe\json_decode($message[0]['body'], true)['data'][0]);

        self::assertSame($matchDetailDataProvider->getMatchId(), $checkMatchDetailDataProvider->getMatchId());
        self::assertSame($matchDetailDataProvider->getMatchDatetime(), $checkMatchDetailDataProvider->getMatchDatetime());
        self::assertSame($matchDetailDataProvider->getScoreTeam1(), $checkMatchDetailDataProvider->getScoreTeam1());
        self::assertSame($matchDetailDataProvider->getScoreTeam2(), $checkMatchDetailDataProvider->getScoreTeam2());
        self::assertSame($matchDetailDataProvider->getTeam1(), $checkMatchDetailDataProvider->getTeam1());
        self::assertSame($matchDetailDataProvider->getTeam2(), $checkMatchDetailDataProvider->getTeam2());

        $matchDetailDataProvider = new MatchDetailDataProvider();
        $matchDetailDataProvider->setMatchId('2020-06-16:2100-FR-DE');
        $matchDetailDataProvider->setMatchDatetime('2020-06-16 21:00');
        $matchDetailDataProvider->setTeam1('FR');
        $matchDetailDataProvider->setTeam2('DE');
        $matchDetailDataProvider->setScoreTeam1(1);
        $matchDetailDataProvider->setScoreTeam2(2);

        $this->deleteAllMessage();

        $handler($matchDetailDataProvider);

        $message = $this->getMessageInfo();

        self::assertCount(1, $message);

        $checkMatchDetailDataProvider = new MatchDetailDataProvider();
        $checkMatchDetailDataProvider->fromArray(\Safe\json_decode($message[0]['body'], true)['data'][1]);

        self::assertSame($matchDetailDataProvider2->getMatchId(), $checkMatchDetailDataProvider->getMatchId());
        self::assertSame($matchDetailDataProvider2->getMatchDatetime(), $checkMatchDetailDataProvider->getMatchDatetime());
        self::assertSame($matchDetailDataProvider2->getScoreTeam1(), $checkMatchDetailDataProvider->getScoreTeam1());
        self::assertSame($matchDetailDataProvider2->getScoreTeam2(), $checkMatchDetailDataProvider->getScoreTeam2());
        self::assertSame($matchDetailDataProvider2->getTeam1(), $checkMatchDetailDataProvider->getTeam1());
        self::assertSame($matchDetailDataProvider2->getTeam2(), $checkMatchDetailDataProvider->getTeam2());

        $checkMatchDetailDataProvider = new MatchDetailDataProvider();
        $checkMatchDetailDataProvider->fromArray(\Safe\json_decode($message[0]['body'], true)['data'][0]);

        self::assertSame($matchDetailDataProvider->getMatchId(), $checkMatchDetailDataProvider->getMatchId());
        self::assertSame($matchDetailDataProvider->getMatchDatetime(), $checkMatchDetailDataProvider->getMatchDatetime());
        self::assertSame($matchDetailDataProvider->getScoreTeam1(), $checkMatchDetailDataProvider->getScoreTeam1());
        self::assertSame($matchDetailDataProvider->getScoreTeam2(), $checkMatchDetailDataProvider->getScoreTeam2());
        self::assertSame($matchDetailDataProvider->getTeam1(), $checkMatchDetailDataProvider->getTeam1());
        self::assertSame($matchDetailDataProvider->getTeam2(), $checkMatchDetailDataProvider->getTeam2());
    }

    private function getMessageInfo(): array
    {
        $sql = "SELECT * FROM messenger_messages";
        $stmt = $this->entityManager->prepare($sql);
        return $stmt->executeQuery()->fetchAllAssociative();
    }

    private function deleteAllMessage(): void
    {
        $this->entityManager->executeStatement('DELETE FROM messenger_messages');
    }
}
