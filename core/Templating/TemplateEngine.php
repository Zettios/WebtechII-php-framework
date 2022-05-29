<?php

namespace Webtek\Core\Templating;


class TemplateEngine
{
    public function processBlocks(string $child, string $parent): string
    {
        $childBlocks = $this->getBlocks($child);
        $parentBlocks = $this->getBlocks($parent);

        foreach ($parentBlocks as $key => $parentBlock) {
            if (array_key_exists($key, $childBlocks)) {
                $parent = str_replace($parentBlock, $childBlocks[$key], $parent);
                $parent = str_replace("blockstart(".$key.")", "", $parent);
                $parent = str_replace("blockend(".$key.")", "", $parent);
            }
        }

        return $parent;
    }

    public function getBlocks($body): array
    {
        $needle = "blockstart(";
        $lastPos = 0;
        $positions = array();
        $blockNames = array();

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
            $blockNames[$key[1]] = "1";
        }

        foreach ($blockNames as $key => $blockName) {
            $blockNames[$key] = $this->get_string_between($body, "blockstart(".$key.")", "blockend(".$key.")");
        }

        return $blockNames;
    }

    public function get_string_between($string, $start, $end): string
    {
        $startPos = strpos($string, $start);
        $content = "";
        while (substr($string, $startPos, strlen($end)) !== $end) {
            $content .= $string[$startPos];
            $startPos++;
        }
        return $content.$end;
    }

    public function processExtend(string $body): string
    {
        $needle = "extend(";
        $firstPos = strpos($body, $needle);
        $extend = "";
        while ($body[$firstPos] !== ")"){
            $extend = $extend.$body[$firstPos];
            $firstPos++;
        }
        $fileToExtend = explode($needle,$extend);
        return $this->getTemplates($body, $fileToExtend[1]);
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