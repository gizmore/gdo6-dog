<?php
namespace GDO\Dog;

final class Worker
{

    private array $pipes = [];

    private $process;

    /**
     *
     */
    public function start(): bool
    {
        $descriptorspec = [
            0 => ['socket', 'r'],  // stdin is a pipe that the child will read from
            1 => ['socket', 'w'],  // stdout is a pipe that the child will write to
            2 => ['socket', 'w'],  // stderr is a pipe that the child will write to
        ];
        $path = Module_Dog::instance()->filePath('bin/dog_worker.php');
        if (!($this->process = proc_open(['php', escapeshellarg($path)], $descriptorspec, $this->pipes)))
        {
            return false;
        }

        if (!stream_set_blocking($this->pipes[1], false))
        {
            return false;
        }

        return true;
    }

    public function send(): bool
    {

    }

    public function getMessage(): DOG_Message
    {

    }

}
