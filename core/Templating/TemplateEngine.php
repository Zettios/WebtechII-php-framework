<?php

namespace Webtek\Core\Templating;

use Psr\Http\Message\ResponseInterface;
use Webtek\Core\Http\ServerRequest;

class TemplateEngine
{
    public function processArguments(string $body, array $queryArgs): string
    {
        $needle = "arg(";
        $lastPos = 0;
        $positions = array();
        $args = array();

        while (($lastPos = strpos($body, $needle, $lastPos))!== false) {
            $positions[] = $lastPos;
            $lastPos = $lastPos + strlen($needle);
        }

        foreach ($positions as $value) {
            $arg = "";
            while ($body[$value] != ")") {
                $arg = $arg.$body[$value];
                $value++;
            }
            $key = explode($needle, $arg);
            $args[$key[1]] = $arg.$body[$value++];
        }

        foreach (array_keys($args) as $arg) {
            if(array_key_exists($arg, $queryArgs)) {
                $body = str_replace($args[$arg], $queryArgs[$arg], $body);
            }
        }

        return $body;
    }


    public function processBlocks2(ResponseInterface $response)
    {
        $body = $response->getTextBody();
        $needleBlock = "{%";
        $lastPos = 0;
        $blocks = array();
        $positionsBlock = array();

        $array = str_split($body);
        print_r($array);

    }

    public function processBlocks(ServerRequest $serverRequest): ResponseInterface
    {
        $body = $response->getTextBody();
        $needleBlock = "{%";
        $lastPos = 0;
        $blocks = array();
        $positionsBlock = array();


        while (($lastPos = strpos($body, $needleBlock, $lastPos)) !== false) {
            $positionsBlock[] = $lastPos;
            $lastPos = $lastPos + strlen($needleBlock);
        }

        foreach ($positionsBlock as $currentPos) {
            $pos = $currentPos;
            $block = "";
            while ($body[$pos].$body[$pos+1] !== "%}") {
                $block .= $body[$pos];
                $pos++;

            }
            $blocks[] = $block."%}";
        }

        foreach ($blocks as $block){
            $body = str_replace($block, "Pogie", $body);
        }

        return $response->withTextBody($body);
    }
}