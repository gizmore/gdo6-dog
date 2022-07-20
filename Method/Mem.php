<?php
namespace GDO\Dog\Method;

use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\Perf\GDT_PerfBar;
use GDO\Util\FileUtil;

/**
 * Show performance statistics.
 * @author gizmore
 */
final class Mem extends DOG_Command
{
    public $trigger = 'mem';
    
    public function isWebMethod() { return true; }

    public function dogExecute(DOG_Message $message)
    {
        $data = GDT_PerfBar::data();
        $data2 = array(
            $data['gdoFiles'],
            FileUtil::humanFilesize($data['memory_real']),
            FileUtil::humanFilesize($data['memory_max']),
            $data['dbReads'],
            $data['dbWrites'],
        );
        $message->rply('dog_mem', $data2);
    }
	
}
