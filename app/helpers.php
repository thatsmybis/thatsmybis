<?php

function getHexColorFromDec($color) {
    if ($color) {
        $color = dechex($color);

        // If it's too short, keep adding prefixed zero's till it's long enough
        while (strlen($color) < 6) {
            $color = '0' . $color;
        }
    } else {
        $color = 'FFF';
    }
    return '#' . $color;
}

// Gets date+time formated like 2020-12-31 23:59:59
function getDateTime($format = 'Y-m-d H:i:s') {
    return (new \DateTime())->format($format);
}

// Gets a CSS color for an expansion
function getExpansionColor($expansionId) {
    switch ($expansionId) {
        case 1:
            return 'gold';
        case 2:
            return 'uncommon';
        case 3:
            return 'mage';
        default:
            return 'white';
    }
}

// Check the request for whether or not we stored the bool isSuperAdmin as true or false
function isStreamerMode() {
    return request()->get('isStreamerMode');
}

// Check the request for whether or not we stored the bool isSuperAdmin as true or false
function isSuper() {
    return request()->get('isSuperAdmin');
}

// Loads the desired Javascript. Switches source based on dev/prod.
function loadScript($file, $type = 'js') {
    return env('APP_ENV') == 'local' ? asset('/' . $type . '/' . $file) : mix($type . '/processed/' . $file);
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
    $slug = substr(Illuminate\Support\Str::slug($string, '-'), 0, 50);
    if ($slug) {
        return $slug;
    } else {
        return '-';
    }
}
