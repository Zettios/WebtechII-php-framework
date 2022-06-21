<?php

namespace Webtek\Core\Routing;

abstract class AbstractController
{
    protected static function render(string $templateToFind, array $args): array
    {
        return [self::getTemplates($templateToFind), $args];
    }

    protected static function getTemplates(string $templateToFind, string $dir = "../template"): string
    {
        $templates = array_slice(scandir($dir), 2);

        if (empty($templates)){
            return "";
        } else {
            foreach ($templates as $template){
                $path = $dir."/".$template;
                if (is_dir($path)){
                    $template = self::getTemplates($templateToFind, $path);
                    if ($template !== ""){
                        return $template;
                    }
                } else {
                    if (str_ends_with($template, ".html") || str_ends_with($template, ".php")) {
                        if ($template === $templateToFind){
//                            ob_start();
//                            include $path;
//                            return ob_get_clean();

                            return file_get_contents($path);
                        }
                    }
                }
            }
            return "";
        }
    }
}