<?php
defined('MOODLE_INTERNAL') || die();

function local_purpleproflujotheme_before_http_headers() {
    global $PAGE;
    $PAGE->requires->css('/local/purpleproflujotheme/css/style.css');
}