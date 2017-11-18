<?php
/**
 * Dida Framework  -- A Rapid Development Framework
 * Copyright (c) Zeupin LLC. (http://zeupin.com)
 *
 * Licensed under The MIT License.
 * Redistributions of files MUST retain the above copyright notice.
 */

namespace Dida\ID;

/**
 * Twitter的雪花算法SnowFlack
 */
class SnowFlack
{
    /**
     * Version
     */
    const VERSION = '20171114';


    /**
     * 使用雪花算法生成一个id。
     *
     * @return string|int   成功返回生成的id，32位系统返回string，64位系统返回int。
     *
     * @desc
     * id由64bit组成。
     * 第1个bit（符号位）留空，只使用剩下的63位。
     * 41位bit用于存放毫秒时间戳。
     * 10位bit用于存放标识本台机器的device_id。（0~1023）
     * 12位bit用于存放自增id。（0-4095）
     */
    public static function make()
    {
        // 检查是32位PHP还是64位PHP
        if (PHP_INT_MAX === 2147483647) {
            return self::int32();
        } else {
            return self::int64();
        }
    }


    /**
     * 32位PHP，只能返回一个字符串，因为结果是64位的。
     *
     * @return string
     */
    protected static function int32()
    {
        // 获取数据
        $msec = self::msecPart();
        $device = self::devicePart();
        $seq = self::seqPart();

        // 转为2进制
        $msec = base_convert($msec, 10, 2);
        $device = base_convert($device, 10, 2);
        $device = str_pad($device, 10, '0', STR_PAD_LEFT);
        $seq = base_convert($seq, 10, 2);
        $seq = str_pad($seq, 12, '0', STR_PAD_LEFT);

        // 拼接结果
        $result = $msec . $device . $seq;

        // 返回
        return base_convert($result, 2, 10);
    }


    /**
     * 64位PHP，直接返回生成的64位整数。
     *
     * @return int
     */
    protected static function int64()
    {
        // 获取数据
        $msec = self::msecPart();
        $device = self::devicePart();
        $seq = self::seqPart();

        // 整数移位操作
        $msec = intval($msec) << 22;
        $device = $device << 12;

        // 返回
        return $msec + $device + $seq;
    }


    /**
     * 获取当前毫秒数的字符串
     *
     * @return string
     */
    protected static function msecPart()
    {
        list($usec, $sec) = explode(' ', microtime());
        $msec = $sec . substr($usec, 2, 3);
        return $msec;
    }


    /**
     * 获取当前设备id
     *
     * @return int
     */
    protected static function devicePart()
    {
        if (defined('DIDA_DEVICE_ID') && is_int(DIDA_DEVICE_ID) && (DIDA_DEVICE_ID >= 0) && (DIDA_DEVICE_ID < 1024)) {
            $device_id = intval(DIDA_DEVICE_ID);
        } else {
            $device_id = 0;
        }

        return $device_id;
    }


    /**
     * 获取序列号(0-4095)
     *
     * @return int
     */
    protected static function seqPart()
    {
        return rand(0, 4095);
    }
}
