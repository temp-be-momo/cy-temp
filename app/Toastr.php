<?php

namespace App;

/**
 * Helper class to display notifications with toastr.js
 * https://cylab.be/blog/219/notifications-with-toastrjs-and-laravel
 *
 * @author Thibault Debatty
 */
class Toastr
{
    public static function info(string $msg)
    {
        session()->push('toastr-info', $msg);
    }

    public static function warning(string $msg)
    {
        session()->push('toastr-warning', $msg);
    }

    public static function success(string $msg)
    {
        session()->push('toastr-success', $msg);
    }

    public static function error(string $msg)
    {
        session()->push('toastr-error', $msg);
    }
}
