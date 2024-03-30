<?php
namespace GDO\Dog;

use Amp\Cancellation;
use Amp\Parallel\Worker\Task;
use Amp\Sync\Channel;

final class WorkerThread implements Task
{

    public bool $running = true;



    public function __construct()
    {
//        parent::__construct();
    }

    public function run(Channel $channel, Cancellation $cancellation): mixed
    {
        sleep(3);
    }

}