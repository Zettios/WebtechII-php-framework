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
            $blockNames[$key[1]] = "";
        }

        foreach ($blockNames as $key => $blockName) {
            if (str_contains($body, "blockstart(".$key.")") && str_contains($body, "blockend(".$key.")")) {
                $blockNames[$key] = $this->getSection($body, "blockstart(".$key.")", "blockend(".$key.")");
            }
        }

        return $blockNames;
    }

    public function getSection($string, $start, $end): string
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
        $parent = $this->getTemplates($fileToExtend[1]);
        return $parent;
    }

    public function getTemplates(string $fileToFind, string $dir = "../template"): string
    {
        $foundFiles = array_slice(scandir($dir), 2);
        if (empty($foundFiles)){
            return "";
        } else {
            foreach ($foundFiles as $file) {
                $path = $dir . "/" . $file;
                if (is_dir($path)) {
                    $content = $this->getTemplates($fileToFind, $path);
                    if ($content !== "") {
                        return $content;
                    }
                } else {
                    if ($file === $fileToFind) {
//                        ob_start();
//                        include $path;
//                        return ob_get_clean();

                        return file_get_contents($path);
                    }
                }
            }
        }
        return "";
    }

    public function processArguments(string $body, array $functionArgs): string
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
            if(array_key_exists($arg, $functionArgs)) {
                $body = str_replace($args[$arg], $functionArgs[$arg], $body);
            }
        }

        return $body;
    }

    public function processForloops(string $body, array $functionArgs): string
    {
        $needle = "forloopstart(";
        $lastPos = 0;
        $positions = array();
        $loopNames = array();

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
            $loopNames[$key[1]] = "";
        }

        foreach ($loopNames as $key => $blockName) {
            if (str_contains($body, "forloopstart(".$key.")") && str_contains($body, "forloopend(".$key.")")) {
                $loopNames[$key] = $this->getSection($body, "forloopstart(".$key.")", "forloopend(".$key.")");
            }
        }

        foreach (array_keys($functionArgs) as $functionArgKey) {
            if (array_key_exists($functionArgKey, $loopNames)) {
                $loopBody = $this->createLoopBody($functionArgKey, $functionArgs[$functionArgKey], $loopNames[$functionArgKey]);
                //$loopNames[$functionArgKey] = $loopBody;
                $body = str_replace($loopNames[$functionArgKey], $loopBody, $body);
            }
        }

        return $body;
    }

    public function createLoopBody(string $key, array $values, string $loopBody): string
    {
        $valLength = sizeof($values);
        $contentBase = $this->getStringBetween($loopBody, "forloopstart(".$key.")", "forloopend(".$key.")");
        $forBody = "";


        $needle = "forarg(";
        $lastPos = 0;
        $positions = array();
        $args = array();

        while (($lastPos = strpos($contentBase, $needle, $lastPos))!== false) {
            $positions[] = $lastPos;
            $lastPos = $lastPos + strlen($needle);
        }

        foreach ($positions as $value) {
            $arg = "";
            while ($contentBase[$value] != ")") {
                $arg = $arg.$contentBase[$value];
                $value++;
            }
            $key = explode($needle, $arg);
            $args[$key[1]] = $arg.$contentBase[$value++];
        }


        for ($i = 0 ; $i < $valLength ; $i++) {
            $tempBody = $contentBase;
            foreach (array_keys($args) as $argKey) {
                $tempBody = str_replace($args[$argKey], $values[$i][$argKey], $tempBody);
            }

            $tempBody = preg_replace('~[\r\n]+~', '', $tempBody);
            $forBody .= $tempBody;
        }

        return $forBody;
    }

    public function processCompare(string $body, array $functionArgs): string
    {
        $needle = "compare(";
        $lastPos = 0;
        $positions = array();
        $compares = array();

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
            $compares[$key[1]] = "";
        }

        foreach (array_keys($compares) as $compare) {
            $splitCompare = explode("//", $compare);
            $splitCompare = array_map('trim', $splitCompare);
            if (count($splitCompare)  >= 4) {
                if ($splitCompare[0] === $splitCompare[1]) {
                    $value = $needle.$compare.")";
                    $body = str_replace($value, $splitCompare[2], $body);
                } else {
                    $value = $needle.$compare.")";
                    $body = str_replace($value, $splitCompare[3], $body);
                }
            }
        }

        return $body;
    }


    public function getStringBetween($string, $start, $end): string
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

}