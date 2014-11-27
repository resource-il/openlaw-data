<?php

namespace Openlaw\Command;

trait Utils
{
    protected function sanitizeString($string = '', $extra_filters = [])
    {
        if (!empty($extra_filters)) {
            // Make your own filters
            foreach ($extra_filters as $extra_filter) {
                $string = str_replace($extra_filter[0], $extra_filter[1], $string);
            }
        }
        // Replace strings with strings
        $string = str_replace(['–', '―'], ['-', '-'], $string);
        // Replace un-wanted single quotes with simple single quote
        $string = str_replace(['‘', '`'], '\'', $string);
        // Replace un-wanted double-quotes with simple double-quote
        $string = str_replace(['”', '”', '“', '״'], '"', $string);
        // Remove strings
        $string = str_replace(['=', '\\', '?', json_decode('"\u200f"')], '', $string);
        // Replace unicode spaces with simple space
        $string = str_replace([json_decode('"\u2002"'), json_decode('"\u2003"')], ' ', $string);
        // Add space between two enclosed texts
        $string = str_replace(')(', ') (', $string);
        // Replace multiple spaces with one space
        $string = preg_replace('/[ \t]+/', ' ', $string);
        // Remove white spaces from beginning and end
        $string = trim($string);

        return $string;
    }
}
