<?php

$socket = fopen('/dev/ttyUSB0', 'w+');
fwrite($socket, "inited 0\n");
fclose($socket);
$fan1 = $fan2 = $temp1 = $temp2 = $hdd0 = $hdd1 = $progress = $raid = 0;

while(1) {
    $socket = fopen('/dev/ttyUSB0', 'w+');
    $lines = null;
    exec('/usr/bin/sensors', $lines);
foreach($lines as $line) {
    if (preg_match('|CPU FAN Speed:\s+(\d+) RPM|', $line, $m)) {
        if ($fan1 !== $m[1]) {
            print "fan1 " . $m[1] . "\n";
            fwrite($socket, 'fan1 ' . $m[1] . "\n");
        }
        $fan1 = $m[1];
    }
    if (preg_match('|CHASSIS FAN Speed:\s+(\d+)|', $line, $m)) {
        if ($fan2 !== $m[1]) {
            print "fan2 " . $m[1] . "\n";
            fwrite($socket, 'fan2 ' . $m[1] . "\n");
        }
        $fan2 = $m[1];
    }
    if (preg_match('|temp1:\s+\+(\d+)\.0|', $line, $m)) {
        if ($temp1 !== $m[1]) {
            print "temp1 " . $m[1] . "\n";
            fwrite($socket, 'temp1 ' . $m[1] . "\n");
        }
        $temp1 = $m[1];
    }
    if (preg_match('|CPU Temperature:\s+\+(\d+)\.0|', $line, $m)) {
        if ($temp2 !== $m[1]) {
            print "temp2 " . $m[1] . "\n";
            fwrite($socket, 'temp2 ' . $m[1] . "\n");
        }
        $temp2 = $m[1];
    }
}
    $result = null;
    exec('/usr/sbin/smartctl  -a /dev/sdb | /bin/grep Temperature_Celsius', $result);
    if (preg_match('|(\d+)$|', $result[0], $m)) {
        if ($hdd0 !== $m[1]) {
            print "hdd0 " . $m[1] . "\n";
            fwrite($socket, 'hdd0 ' . $m[1] . "\n");
        }
        $hdd0 = $m[1];
    }
    $result = null;
    exec('/usr/sbin/smartctl  -a /dev/sdc | /bin/grep Temperature_Celsius', $result);
    if (preg_match('|(\d+)$|', $result[0], $m)) {
        if ($hdd1 !== $m[1]) {
            print "hdd1 " . $m[1] . "\n";
            fwrite($socket, 'hdd1 ' . $m[1] . "\n");
        }
        $hdd1 = $m[1];
    }
    $result = null;
    exec('/usr/bin/df -h | /bin/grep mnt', $result);
    if (preg_match('|(\d+)\%\s+\/mnt|', $result[0], $m)) {
        if ($progress !== $m[1]) {
            print "progress " . $m[1] . "\n";
            fwrite($socket, 'progress ' . $m[1] . "\n");
        }
        $progress = $m[1];
    }
    $result = null;
    if ($raid !== 1) {
        fwrite($socket, 'raid 1' . "\n");
    }
    $raid = 1;
    sleep(1);
    fclose($socket);
}
