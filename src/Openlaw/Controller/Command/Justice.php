<?php

namespace Openlaw\Controller\Command;

use Openlaw\Controller\Command;
use Openlaw\Silex\Application;

class Justice extends Command
{
    public function fetchBooklet(Application $app)
    {
        $this->protect();
        /** @var \Openlaw\Command\Justice $command */
        $command = $this->cmd('Justice');
        $data = $command->fetchBooklet();
        return $app->json($data);
    }
}

