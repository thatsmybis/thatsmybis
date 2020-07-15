<?php

function getDateTime($format = 'Y-m-d H:i:s') {
    return (new \DateTime())->format($format);
}

/**
 * Split a string into an array delimited by newlines.
 *
 * @param $string string
 *
 * @return array
 */
function splitByLine($string) {
    return preg_split("/\r\n|\n|\r/", $string);
}

function slug($string) {
    $slug = substr(Illuminate\Support\Str::slug($string, '-'), 0, 100);
    if ($slug) {
        return $slug;
    } else {
        return '-';
    }
}
