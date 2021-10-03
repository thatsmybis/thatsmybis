<?php

/**
 * https://stackoverflow.com/a/18781630/1196517
 *
 * @param array      $array
 * @param int|string $position
 * @param mixed      $insert
 */
function array_insert(&$array, $position, $insert) {
    if (is_int($position)) {
        array_splice($array, $position, 0, $insert);
    } else {
        $pos   = array_search($position, array_keys($array));
        $array = array_merge(
            array_slice($array, 0, $pos),
            $insert,
            array_slice($array, $pos)
        );
    }
}

// Based on attendance percentage, return a CSS color class
function getAttendanceColor($percentage = 0) {
    $color = '';

    if ($percentage >= 0.95) {
        $color = 'text-tier-1';
    } else if ($percentage >= 0.9) {
        $color = 'text-tier-2';
    } else if ($percentage >= 0.8) {
        $color = 'text-tier-3';
    } else if ($percentage >= 0.7) {
        $color = 'text-tier-4';
    } else {
        $color = 'text-tier-5';
    }

    return $color;
}

// Takes an integer, converts it to hex
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
        case 1: // Classic
            return 'gold';
        case 2: // TBC
            return 'uncommon';
        case 3: // WOTLK
            return 'mage';
        default:
            return 'white';
    }
}

function getExpansionAbbr($expansionId, $lowercase = null) {
    switch ($expansionId) {
        case 1:
            return $lowercase ? 'classic' : 'Classic';
        case 2:
            return $lowercase ? 'tbc' : 'TBC';
        case 3:
            return $lowercase ? 'wotlk' : 'WoTLK';
        default:
            return '';
    }
}

// Get the list of supported locales
function getLocales() {
    return [
        "da" => "Dansk (incomplete)",
        "de" => "Deutsch",
        "en" => "English",
        "es" => "Español (items only)",
        "fr" => "Français",
        "it" => "Italiano (items only)",
        "no" => "Norsk (incomplete)",
        "pl" => "Polski (no items)",
        "pt" => "Português Brasileiro (items only)",
        "ru" => "Русский",
        "ko" => "한국어 (items only)",
        "cn" => "简体中文 (items only)",
    ];
}

// Check the request for whether or not we stored the bool isAdmin as true or false
function isAdmin() {
    return request()->get('isAdmin');
}

function isGuildAdmin() {
    return request()->get('isGuildAdmin');
}

function isNotYourGuild() {
    return request()->get('isNotYourGuild');
}

function isStreamerMode() {
    return request()->get('isStreamerMode');
}

// Loads the desired Javascript. Switches source based on dev/prod.
function loadScript($file, $type = 'js') {
    return env('APP_ENV') == 'local' ? asset('/' . $type . '/' . $file) : mix($type . '/processed/' . $file);
}

/**
 * - 999 stays 999
 * - 1000 becomes 1k
 * - 1500 becomes 1.5k
 * - 1000000 becomes 1000k
 *
 * @param int $number The number to shorten.
 *
 * @return string
 */
function numToKs($number) {
    if ($number >= 1000) {
        return number_format(($number / 1000), 1) . 'k';
    } else {
        return $number;
    }
}

function slug($string) {
    $slug = substr(Illuminate\Support\Str::slug($string, '-'), 0, 50);
    if ($slug) {
        return $slug;
    } else {
        return '-';
    }
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

/**
 * Expects a float, returns an s-tier with the decimal of the float intact.
 *
 * @param $float
 *
 * @return array
 */
function numToSTier($float) {
    if ($float > 0) {
        $tiers = App\Guild::tiers();

        $whole = floor($float);
        $decimal = $float - $whole;

        $affix = '';
        if ($decimal > 0.66) {
            $affix = '++';
        } else if ($decimal > 0.33) {
            $affix = '+';
        }

        return $tiers[ceil($float)] . $affix;
    } else {
        return '';
    }
}


/**
 * In English, get the time since the given timestamp.
 *
 * eg. '30 seconds' or '5 minutes' or '5 days', etc.
 *
 * @param string $timestamp A unix timestamp from the past
 *
 * @return string
 */
function timeSince ($timestamp) {
    $time = time() - $timestamp; // to get the time since that moment
    $time = ($time < 1) ? 1 : $time;
    $tokens = array (
        31536000 => __('year'),
        2592000 => __('month'),
        604800 => __('week'),
        86400 => __('day'),
        3600 => __('hour'),
        60 => __('minute'),
        1 => __('second')
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
    }
}

/**
 * In English, get the time until the given timestamp.
 *
 * eg. '30 seconds' or '5 minutes' or '5 days', etc.
 *
 * @param string $timestamp A unix timestamp from the past
 *
 * @return string
 */
function timeUntil ($timestamp) {
    $time = $timestamp - time(); // to get the time until that moment
    $time = ($time < 1) ? 1 : $time;
    $tokens = array (
        31536000 => __('year'),
        2592000 => __('month'),
        604800 => __('week'),
        86400 => __('day'),
        3600 => __('hour'),
        60 => __('minute'),
        1 => __('second')
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
    }
}
