<?php

namespace Openlaw\Controller;

use Openlaw\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Command extends Controller
{
    /**
     * @param string $name
     * @return \Openlaw\Silex\Provider\Command
     */
    protected function cmd($name = '')
    {
        static $commands;

        $class = '\\Openlaw\\Command';
        if (!empty($name)) {
            $class .= '\\' . $name;
        }

        if (!isset($commands[$class])) {
            $commands[$class] = new $class();
        }

        return $commands[$class];
    }

    /**
     * @param array $ip_list
     */
    protected function protect(array $ip_list = [])
    {
        $ip_list = array_merge([
            '127.0.0.1',
            '::1',
          ], $ip_list);

        if (!in_array($_SERVER['REMOTE_ADDR'], $ip_list)) {
            throw new AccessDeniedHttpException('Access denied');
        }
    }
}
