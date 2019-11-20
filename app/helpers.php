<?php

function getDateTime($format = 'Y-m-d H:i:s') {
    return (new \DateTime())->format($format);
}
