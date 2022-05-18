<?php

namespace Webtek\Core\Templating;


class TemplateEngine
{
    public function processBlocks(string $body): string {

        return $body;
    }

    public function processExtends(string $body): string
    {
        $needle = "extend(";
        $lastPos = 0;
        $extends = array();

        while (($lastPos = strpos($body, $needle, $lastPos))!== false) {
            $extend = "";
            while ($body[$lastPos] !== ")"){
                $extend = $extend.$body[$lastPos];
                $lastPos++;
            }
            $key = explode($needle,$extend);
            $extend = $extend.$body[$lastPos++];
            $extends[$key[1]] = $extend;
            $lastPos = $lastPos + strlen($needle);
        }


        foreach (array_keys($extends) as $file) {
            $extendContent = $this->getTemplates($body, $file);
            if ($extendContent === "") {
                $extendContent = "[ Template ". $file." not found ]";
            }
            $body = str_replace($extends[$file], $extendContent, $body);
        }
        return $body;
    }

    public function getTemplates(string $body, string $fileToFind, string $dir = "../template"): string
    {
        $foundFiles = array_slice(scandir($dir), 2);
        if (empty($foundFiles)){
            return $body;
        } else {
            foreach ($foundFiles as $file) {
                $path = $dir . "/" . $file;
                if (is_dir($path)) {
                    $content = $this->getTemplates($body, $fileToFind, $path);
                    if ($content !== "") {
                        return $content;
                    }
                } else {
                    if ($file === $fileToFind) {
                        return file_get_contents($path);
                    }
                }
            }
        }

        return "";
    }

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
}