<?php
namespace GDO\Dog\Provider;

use GDO\Dog\Connector;

class Netcat extends Connector
{
    
}

Connector::register(new Netcat());
