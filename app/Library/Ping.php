<?php

namespace Library;

/**
 * Phing
 * A simple way to find out if a host exists (alive) using ping command
 * @author cevarief
 * Change log 04/12/2013 linux : change ping -n -c 1 to ping -c 1 -W 1 timeout 1 sec
 */
class Ping {
    /*
     * Ping ipaddress/hostname using ping command 
     * Usage \Library\Ping::icmp('google.com');
     */

    public static function host($host_address) {
        if (strtolower(substr(PHP_OS, 0, 3) === 'win')) {
            $command = "ping -n 1 -i";
        } else {
            $command = "ping -c 1 -W 1"; #unix/mac harus menggunakan -c 1 untuk 1x request saja
        }

        exec($command . ' ' . escapeshellcmd($host_address), $output, $return);
        # "64 bytes from 127.0.0.1: icmp_seq=1 ttl=64 time=0.039 ms"
        # cari string time=. Jika time out time tidak mengandung =
        # jika host not found $output[1] = kosong. Untuk memastikan check dari time
        // return is_array($output) and strpos($output[1], 'time=');
        return true;
    }

    /*
     * Check if domain is alive 
     * Usage \Library\Ping::domain('google.com');
     */

    public static function socket($host, $port = 80, $ttl = 10) {
        $fp     = fsockopen($host, $port, $errno, $errstr, $ttl);
        $status = $fp ? TRUE : FALSE;
        @fclose($fp);
        return $status;
    }

}

?>
