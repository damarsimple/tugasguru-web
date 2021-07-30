<?php

namespace App\Misc;


class AppConfig
{

    public $fileName;

    public $data = [];

    public function __construct()
    {
        $fileName = base_path() . '/config.json';

        $this->fileName = $fileName;

        $contents = file_get_contents($fileName);

        $this->data = json_decode($contents);
    }

    public function get(string $key)
    {
        return $this->data->{$key};
    }

    public function set(string $key, string|int|float $value)
    {
        $this->data->{$key} = $value;

        $this->serialize();
    }

    public function serialize()
    {
        $stream = fopen($this->fileName, "w+");

        fwrite($stream, json_encode($this->data));

        fclose($stream);
    }
}
