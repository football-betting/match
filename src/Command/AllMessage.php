<?php declare(strict_types=1);

namespace App\Command;

use App\DataTransferObject\TestDataProvider;
use App\Repository\MatchDetailRepository;
use App\Service\MatchManager;
use App\Service\MatchReader;
use App\Service\Message;
use App\Service\Redis\RedisService;
use App\Tests\Unit\Helper\MatchHelperTest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class AllMessage extends Command
{
    protected static $defaultName = 'test:all:message';

    /**
     * @var \App\Service\Redis\RedisService
     */
    private RedisService $redisService;
    private MatchReader $matchReader; //zmiana ja
    private MatchDetailRepository $matchDetailRepository;
    private MatchManager $matchManager;
    //private MatchHelperTest $matchHelperTest;


    public function __construct(RedisService $redisService,
                                MatchReader $matchReader, //to dopisane
                                MatchDetailRepository $matchDetailRepository,
                                MatchManager $matchManager)
    {
        parent::__construct();
        $this->redisService = $redisService;
        $this->matchReader = $matchReader;
        $this->matchDetailRepository = $matchDetailRepository;
        $this->matchManager = $matchManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Test message');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $infos = $this->redisService->getAll();
        /** @var string $item */
        foreach ($infos as $item) {
            $output->writeln($item);
        }

        return 0;
    }
}
