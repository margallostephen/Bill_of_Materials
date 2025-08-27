<?php
require_once PHP_UTILS_PATH . 'randomNumGenerator.php';

function getAjaxPath($file)
{
    echo AJAX_PATH . $file . randomNum();
}

function getJSUtil($file)
{
    echo JS_UTILS_PATH . $file . randomNum();
}

function getJSHelper($file)
{
    echo JS_HELPERS_PATH . $file . randomNum();
}

function getJSFile($file)
{
    echo JS_PATH . $file . randomNum();
}

function getCSSFile($file)
{
    echo CSS_PATH . $file . randomNum();
}

function getImagePath($file)
{
    echo IMAGES_URL . $file . randomNum();
}
