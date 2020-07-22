<?php


namespace App\Service\Parser;


interface Parser
{
    public function parse(string $message);
}