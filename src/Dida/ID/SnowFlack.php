<?php
/**
 * Dida Framework  -- A Rapid Development Framework
 * Copyright (c) Zeupin LLC. (http://zeupin.com)
 *
 * Licensed under The MIT License.
 * Redistributions of files MUST retain the above copyright notice.
 */

namespace Dida\ID;

class SnowFlack
{
    const VERSION = '20171114';


    public static function make()
    {
        if (PHP_INT_MAX === 2147483647) {
            return self::int32();
        } else {
            return self::int64();
        }
    }


    protected static function int32()
    {
        $msec = self::msecPart();
        $device = self::devicePart();
        $seq = self::seqPart();

        $msec = base_convert($msec, 10, 2);
        $device = base_convert($device, 10, 2);
        $device = str_pad($device, 10, '0', STR_PAD_LEFT);
        $seq = base_convert($seq, 10, 2);
        $seq = str_pad($seq, 12, '0', STR_PAD_LEFT);

        $result = $msec . $device . $seq;

        return base_convert($result, 2, 10);
    }


    protected static function int64()
    {
        $msec = self::msecPart();
        $device = self::devicePart();
        $seq = self::seqPart();

        $msec = intval($msec) << 22;
        $device = $device << 12;

        return $msec + $device + $seq;
    }


    protected static function msecPart()
    {
        list($usec, $sec) = explode(' ', microtime());
        $msec = $sec . substr($usec, 2, 3);
        return $msec;
    }


    protected static function devicePart()
    {
        if (defined('DIDA_DEVICE_ID') && is_int(DIDA_DEVICE_ID) && (DIDA_DEVICE_ID >= 0) && (DIDA_DEVICE_ID < 1024)) {
            $device_id = intval(DIDA_DEVICE_ID);
        } else {
            $device_id = 0;
        }

        return $device_id;
    }


    protected static function seqPart()
    {
        return rand(0, 4095);
    }
}
