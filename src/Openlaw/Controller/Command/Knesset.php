<?php

namespace Openlaw\Controller\Command;

use Openlaw\Controller\Command;
use Openlaw\Silex\Application;

class Knesset extends Command
{
    public function fetchBooklet(Application $app)
    {
        $this->protect();
        /** @var \Openlaw\Command\Knesset $command */
        $command = $this->cmd('Knesset');
        $data = $command->fetchBooklet();
        return $app->json($data);
    }
}

