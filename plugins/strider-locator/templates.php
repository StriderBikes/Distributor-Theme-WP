<?php
/*
 *
 */

class StriderLocTemplates
{
    private static $instance;

    final private function __construct()
    {
    }

    final private function __clone()
    {
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}