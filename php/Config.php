<?php

class Config
{
    private $config;

    public function __construct()
    {
        $this->config = parse_ini_file(CONFIG_FILE, true);
    }

    public function exists($key, $value)
    {
        return isset($this->config[$key]) ? (isset($this->config[$key][$value]) ? true : false) : false;
    }

    public function get($key, $value)
    {
        //return isset($this->config[$key]) ? (isset($this->config[$key][$value]) ? $this->config[$key][$value] : '') : '';
        return $this->exists($key, $value) ? $this->config[$key][$value] : '';
    }
}
