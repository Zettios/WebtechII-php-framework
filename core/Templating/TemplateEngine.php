<?php

namespace Webtek\Core\Templating;


class TemplateEngine
{
    // ========= Extend functions =========

    /**
     * Finds if the page extends another file and gets it.
     * @param string $body The page to show
     * @return string
     */
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

    /**
     * Recursively looks through the ../template folder to find the file to extend.
     * @param string $fileToFind the name of the extend file
     * @param string $dir the directory where it's located
     * @return string
     */
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
                        return file_get_contents($path);
                    }
                }
            }
        }
        return "";
    }

    // ========= Block functions =========

    /**
     * Looks in the child and parent all blocks and replaces them inside the parent (extended) file.
     * @param string $child the page to show to the user
     * @param string $parent the page that the page to show extends
     * @return string
     */
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

    /**
     * Find the blocks inside a file and saves them into an array
     * @param string $body the body to find the blocks in
     * @return array
     */
    public function getBlocks(string $body): array
    {
        $blockNames = $this->getFunctionNames("blockstart(", $body);

        foreach ($blockNames as $key => $blockName) {
            if (str_contains($body, "blockstart(".$key.")") && str_contains($body, "blockend(".$key.")")) {
                $blockNames[$key] = $this->getSection($body, "blockstart(".$key.")", "blockend(".$key.")");
            }
        }

        return $blockNames;
    }

    /**
     * Gets the whole block and everything inside it and saves it.
     * @param string $page the page to get the block section from
     * @param string $start the string where the block starts
     * @param string $end the string where the block ends
     * @return string
     */
    public function getSection(string $page, string $start, string $end): string
    {
        $startPos = strpos($page, $start);
        $content = "";
        while (substr($page, $startPos, strlen($end)) !== $end) {
            $content .= $page[$startPos];
            $startPos++;
        }
        return $content.$end;
    }

    // ========= Argument functions =========

    /**
     * Finds all arguments inside the page to show and replaces them with the arguments given inside the controller.
     * @param string $body the page to show
     * @param array $functionArgs arguments given inside the controller that called this page
     * @return string
     */
    public function processArguments(string $body, array $functionArgs): string
    {
        $args = $this->createArgs("arg(", $body);

        foreach (array_keys($args) as $arg) {
            if(array_key_exists($arg, $functionArgs)) {
                $body = str_replace($args[$arg], $functionArgs[$arg], $body);
            }
        }

        return $body;
    }

    // ========= Loop functions =========

    /**
     * Looks for all loops inside the page to show, creates them and replaces them inside the file.
     * @param string $body the page to show
     * @param array $functionArgs arguments given inside the controller
     * @return string
     */
    public function processForloops(string $body, array $functionArgs): string
    {
        $loopNames = $this->getFunctionNames("forloopstart(", $body);

        foreach ($loopNames as $key => $blockName) {
            if (str_contains($body, "forloopstart(".$key.")") && str_contains($body, "forloopend(".$key.")")) {
                $loopNames[$key] = $this->getSection($body, "forloopstart(".$key.")", "forloopend(".$key.")");
            }
        }

        foreach (array_keys($functionArgs) as $functionArgKey) {
            if (array_key_exists($functionArgKey, $loopNames)) {
                $loopBody = $this->createLoopBody($functionArgKey, $functionArgs[$functionArgKey], $loopNames[$functionArgKey]);
                $body = str_replace($loopNames[$functionArgKey], $loopBody, $body);
            }
        }

        return $body;
    }

    /**
     * Creates the whole loop body and the arguments inside them
     * @param string $key the name of the loop
     * @param array $values the values that need to placed inside the loop
     * @param string $loopBody the given body inside the loop block
     * @return string
     */
    public function createLoopBody(string $key, array $values, string $loopBody): string
    {
        $valLength = sizeof($values);
        $contentBase = $this->getStringBetween($loopBody, "forloopstart(".$key.")", "forloopend(".$key.")");
        $forBody = "";

        $args = $this->createArgs("argfor(", $contentBase);

        for ($i = 0 ; $i < $valLength ; $i++) {
            $tempBody = $contentBase;
            foreach (array_keys($args) as $argKey) {
                if (key_exists($argKey, $values[$i])) {
                    $tempBody = str_replace($args[$argKey], $values[$i][$argKey], $tempBody);
                }
            }

            $tempBody = preg_replace('~[\r\n]+~', '', $tempBody);
            $forBody .= $tempBody;
        }
        return $forBody;
    }

    /**
     * Gets a string inside the given string.
     * Excludes the given start and end.
     * @param string $string the string to get the string from
     * @param string $start the start of the string that will be excluded
     * @param string $end the end of the string that will be excluded
     * @return string
     */
    public function getStringBetween(string $string, string $start, string $end): string
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    // ========= Compare functions =========

    /**
     * Looks for any compares inside the page, processes them and replaces them
     * @param string $body the body to search and replace in
     * @param array $functionArgs arguments given in the controller
     * @return string
     */
    public function processCompare(string $body, array $functionArgs): string
    {
        $needle = "compare(";
        $compares = $this->getFunctionNames($needle, $body);

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

    // ========= General functions =========

    /**
     * This function finds blocks/loops/compares inside the page and keeps track of them.
     * @param string $needle needle used to find defined operators inside the page
     * @param string $body the body the find them in
     * @return array
     */
    public function getFunctionNames(string $needle, string $body): array
    {
        $lastPos = 0;
        $positions = array();
        $names = array();

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
            $names[$key[1]] = "";
        }
        return $names;
    }

    /**
     * This function finds (loop) arguments inside the page and keeps track of them.
     * @param string $needle needle used to find defined operators inside the page
     * @param string $body the body the find them in
     * @return array
     */
    public function createArgs(string $needle, string $body): array
    {
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

        return $args;
    }
}