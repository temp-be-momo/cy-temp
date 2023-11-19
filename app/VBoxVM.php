<?php

namespace App;

use Cylab\Vbox\VBox;

/**
 * Description of VM
 *
 * @author tibo
 */
class VBoxVM
{

    private static $vbox = null;

    /**
     *
     * @return \Cylab\Vbox\VBox;
     */
    public static function vbox() : VBox
    {
        if (self::$vbox === null) {
            self::connect();
        }

        return self::$vbox;
    }

    /**
     * Force reconnection to VirtualBox
     * @return VBox
     */
    public static function connect() : VBox
    {
        self::$vbox = new VBox(
            config('vbox.user'),
            config('vbox.password'),
            "http://" . config('vbox.host') . ":18083"
        );

        return self::$vbox;
    }

    /**
     *
     * @return array
     */
    public static function all() : array
    {
        $vms = self::vbox()->allVMs();

        usort($vms, function (\Cylab\Vbox\VM $vm1, \Cylab\Vbox\VM $vm2) {
            return strnatcmp($vm1->getName(), $vm2->getName());
        });

        return $vms;
    }

    /**
     *
     * @param string $uuid
     * @return \Cylab\Vbox\VM
     */
    public static function find(string $uuid) : \Cylab\Vbox\VM
    {
        return self::vbox()->findVM($uuid);
    }

    /**
     *
     * @return int
     */
    public static function count() : int
    {
        return count(self::vbox()->allVMs());
    }

    private static $ALLOWED = ["en", "et", "wl"];

    public static function hostInterfaces() : array
    {
        $interfaces = self::vbox()->host()->networkInterfaces();
        usort($interfaces, function ($a, $b) {
            return strcmp($a->name(), $b->name());
        });

        $interfaces = array_filter($interfaces, function ($i) {
            $prefix = substr($i->name(), 0, 2);
            return in_array($prefix, self::$ALLOWED);
        });

        return $interfaces;
    }
}
