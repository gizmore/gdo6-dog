<?php
namespace GDO\Dog;

use Amp\Process\Process;
use Amp\Parallel\Worker\WorkerPool;
use Amp\Parallel\Worker;

final class DogWorker
{

    private static WorkerThread $thread;

    public static WorkerPool $POOL;

//    private array $pipes = [];
//
//    private $process;
//
//    public static int $procID = -1;
//
//    public static function isChild(): bool
//    {
//        return self::$procID === 0;
//    }

    public static function init(): bool
    {
        require __DIR__ . '/vendor/autoload.php';
        self::$POOL = Worker\workerPool();
//        self::$POOL = new WorkerPool();

//        self::$thread = new WorkerThread();
//        self::$thread->start();
        self::run();
        return true;
//        $pid = pcntl_fork();trie
//        switch ($pid)
//        {
//            case -1:
//                return false;
//            case 0:
//                self::$procID = 0;
//                break;
//            default:
//                self::$procID = $pid;
//                break;
//        }
    }

    /**
     * child method
     */
    public static function run()
    {

//        \parallel\run([WorkerThread::class, 'run']);
//        self::$thread->start();
//        while (true)
//        {
//            echo "WOrker\n";
//            usleep(500000);
//        }
    }

    /**
     *
     */
//    public function start(): bool
//    {
//        $descriptorspec = [
//            0 => ['socket', 'r'],  // stdin is a pipe that the child will read from
//            1 => ['socket', 'w'],  // stdout is a pipe that the child will write to
//            2 => ['socket', 'w'],  // stderr is a pipe that the child will write to
//        ];
//        $path = Module_Dog::instance()->filePath('bin/dog_worker.php');
//        if (!($this->process = proc_open(['php', escapeshellarg($path)], $descriptorspec, $this->pipes)))
//        {
//            return false;
//        }
//
//        if (!stream_set_blocking($this->pipes[1], false))
//        {
//            return false;
//        }
//
//        return true;
//    }
//
//    public function send(): bool
//    {
//
//    }
//
//    public function getMessage(): DOG_Message
//    {
//
//    }

}
