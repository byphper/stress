<?php


namespace Actor\Stress;


use Symfony\Component\Console\Helper\FormatterHelper;

class FormatOutput
{

    public static function section(string $title, string $content): string
    {
        $helper = new FormatterHelper();
        return $helper->formatSection($title, $content);
    }
}