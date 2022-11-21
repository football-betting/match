<?php declare(strict_types=1);

namespace App\Messenger;

use App\DataTransferObject\MatchDetailDataProvider;
use App\DataTransferObject\MatchListDataProvider;
use App\Service\Redis\RedisService;
use Symfony\Component\Messenger\MessageBusInterface;

class MatchMessageHandler
{
    private RedisService $redisService;
    private MessageBusInterface $messageBus;


    public function __construct(RedisService $redisService, MessageBusInterface $messageBus)
    {
        $this->redisService = $redisService;
        $this->messageBus = $messageBus;
    }

    public function __invoke(MatchDetailDataProvider $message): void
    {
        $ident = $message->getMatchId();
        $matchFromRedis = $this->redisService->get($ident);
        if ($matchFromRedis === '') {
            $this->redisService->set($ident, json_encode($message->toArray(), JSON_THROW_ON_ERROR));
            $this->sendMatchs();

            return;
        }

        $matchDetailDataProviderRedis = new MatchDetailDataProvider();
        $matchDetailDataProviderRedis->fromArray(json_decode($matchFromRedis, true));

        if ($message->getScoreTeam1() !== $matchDetailDataProviderRedis->getScoreTeam1() ||
            $message->getScoreTeam2() !== $matchDetailDataProviderRedis->getScoreTeam2()
        ) {
            $this->redisService->set($ident, json_encode($message->toArray(), JSON_THROW_ON_ERROR));
            $this->sendMatchs();
        }
    }

    private function sendMatchs()
    {
        $matchs = $this->redisService->getAll();

        $matchListDataProvider = new MatchListDataProvider();
        foreach ($matchs as $match) {
            $matchDetailDataProvider = new MatchDetailDataProvider();
            $matchDetailDataProvider->fromArray(json_decode($match, true));

            $matchListDataProvider->addData($matchDetailDataProvider);
        }

        $this->messageBus->dispatch($matchListDataProvider);
    }
}
