<?php


namespace Actor\Stress;


class FormatOutput
{

    public static function error(string $error): string
    {
        return "<error>" . $error . "</error>";
    }
}