<?php
namespace GDO\Dog;
use GDO\Core\Application;

final class Dog extends Application
{
    public function isCLI() { return true; }
    
    public function mainloop()
    {
        $servers = DOG_Server::table();
        foreach ($servers->all() as $server)
        {
            $this->mainloopServer($server);
        }
    }
    
    private function mainloopServer(DOG_Server $server)
    {
        $connector = $server->getConnector();
        
    }
    
}
