<?php
namespace leen\phphelper\time;

/**
 * 时间处理相关
 * @Author: Leen
 * @Date:   2021-01-04 16:38:59
 * @Email:  leeningln@163.com
 * @Last Modified By : Leen
 */
class Time
{

    private static $_instance = null;
    private function __construct()
    {}
    private function __clone()
    {}

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * [timeHandle 将制定日期往前或往后推一段时间，支持批量处理]
     * @method   timeHandle
     * @param    [type]                   $times  [时间戳数组]
     * @param    [type]                   $offset [时间偏移量]
     * @param    string                   $type   [偏移类型，+:往后偏移; -:往前偏移;]
     * @return   [type]                           [description]
     * @DateTime 2021-01-04T16:49:52+0800
     * @Author   Leen
     */
    public static function timeHandle($times, $offset = 60 * 60 * 24, $type = '-')
    {
        $timeArr = [];
        foreach ($times as $key => $value) {
            switch ($type) {
                case '+':
                    $timeArr[$key] = $value - $offset;
                    break;
                case '-':
                default:
                    $timeArr[$key] = $value - $offset;
                    break;
            }
        }
        return $timeArr;
    }

    /**
     * [createDateArr 生成指定日期段做为value的数组，或多维数组]
     * @method   createDateArr
     * @param    [type]                   $start_time  [开始日期，可以是时间戳也可以是日期格式（至少包含年月日）]
     * @param    [type]                   $end_time    [结束日期，可以是时间戳也可以是日期格式（至少包含年月日）]
     * @param    string                   $date_format [日期格式，如Ymd]
     * @param    [type]                   $step        [间隔秒数，如：60*60*24]
     * @param    array                    $field       [二维数组中的字段]
     * @return   [type]                                [description]
     * @DateTime 2021-01-04T16:50:27+0800
     * @Author   Leen
     */
    public static function createDateArr($start_time, $end_time, $date_format = 'Ymd', $step = 60 * 60 * 24, $field = [])
    {
        $start_time = strtotime($start_time) ? strtotime($start_time) : $start_time;
        $end_time = strtotime($end_time) ? strtotime($end_time) : $end_time;
        if (abs($start_time - $end_time) >= $step) {
            $arr = range($start_time, $end_time, $step);
        } else {
            $arr = [$start_time];
        }
        $date_arr = [];
        // 方法一：
        array_walk($arr, function (&$item, $key) use ($field, &$date_arr, $date_format) {
            // $date_arr[date($date_format, $item)] = $field;       // 生成指定日期段做为key的数组
            $date_arr[] = date($date_format, $item); // 生成指定日期段做为value的数组
        });
        // 方法二：
        /*$arr = array_map(function($item) use($field, &$date_arr, $date_format){
        // $date_arr[$item] = $field;       // 生成指定日期段做为key的数组
        $date_arr[] = date($date_format, $item); // 生成指定日期段做为value的数组
        }, $arr);*/
        return $date_arr;
    }

    /**
     * [createDateAndFieldsArr 生成指定日期段做为key，指定内容做为value的数组，或多维数组]
     * @method   createDateAndFieldsArr
     * @param    [type]                   $start_time  [description]
     * @param    [type]                   $end_time    [description]
     * @param    string                   $date_format [description]
     * @param    [type]                   $step        [description]
     * @param    array                    $fields      [description]
     * @return   [type]                                [description]
     * @DateTime 2021-06-16T17:26:02+0800
     * @Author   Leen
     */
    public static function createDateAndFieldsArr($start_time, $end_time, $date_format = 'Ymd', $step = 60 * 60 * 24, $fields = [])
    {
        $start_time = strtotime($start_time) ? strtotime($start_time) : $start_time;
        $end_time = strtotime($end_time) ? strtotime($end_time) : $end_time;
        if (abs($start_time - $end_time) >= $step) {
            $arr = range($start_time, $end_time, $step);
        } else {
            $arr = [$start_time];
        }
        $date_arr = [];
        // 方法一：
        array_walk($arr, function (&$item, $key) use ($fields, &$date_arr, $date_format) {
            $date_arr[date($date_format, $item)] = $fields;       // 生成指定日期段做为key的数组
            // $date_arr[] = date($date_format, $item); // 生成指定日期段做为value的数组
        });
        // 方法二：
        /*$arr = array_map(function($item) use($fields, &$date_arr, $date_format){
        // $date_arr[$item] = $fields;       // 生成指定日期段做为key的数组
        $date_arr[] = date($date_format, $item); // 生成指定日期段做为value的数组
        }, $arr);*/
        return $date_arr;
    }

    /**
     * [getDate 根据时区获取对应时区当前的日期]
     * 常用时区及编码如下：
     *     Asia/Shanghai – 上海
     *     Asia/Chongqing – 重庆
     *     Asia/Urumqi – 乌鲁木齐
     *     Asia/Hong_Kong – 香港
     *     Asia/Macao – 澳门
     * @method   getDate
     * @param    string                   $timeZone   [时区，如：utc, cst表示四个时区，使用北京时间可以用Asia/Shanghai]
     * @param    string                   $dateFormat [日期格式]
     * @return   [type]                               [description]
     * @DateTime 2021-01-06T15:02:54+0800
     * @Author   Leen
     */
    public static function getDate($timeZone = 'UTC', $dateFormat = 'Y-m-d H:i:s')
    {
        $timeZone = !empty($timeZone) ? $timeZone : date_default_timezone_get();
        $d = new \DateTime();
        $d->setTimezone(new \DateTimeZone($timeZone));
        return $d->format($dateFormat);
    }

    /**
     * [dateFormat 格式化日期]
     * @method   dateFormat
     * @param    [type]                   $date       [日期]
     * @param    string                   $dateFormat [格式]
     * @return   [type]                               [description]
     * @DateTime 2021-01-06T15:04:44+0800
     * @Author   Leen
     */
    public static function dateFormat($date, $dateFormat = 'Ymd')
    {
        return date($dateFormat, strtotime($date));
    }

    /**
     * [judgeDate 验证日期是否是合法的日期]
     * @method   judgeDate
     * @param    string                   $date_format [description]
     * @param    [type]                   $date        [description]
     * @return   [type]                                [description]
     * @DateTime 2021-01-06T15:05:26+0800
     * @Author   Leen
     */
    public static function judgeDate($date_format = 'Y-m-d', $date)
    {
        if ($date == date($date_format, strtotime($date))) {
            return 1;
        }
        return 0;
    }

    /**
     * [current_time  返回当前 Unix 时间戳和微秒数]
     * @method   current_time
     * @return   [type]                   [description]
     * @DateTime 2021-01-06T15:26:22+0800
     * @Author   Leen
     *
     * 查看性能用法：
     * $start_time = current_time();
     * for ($i=0; $i < 100000; $i++) {
     *     test($account, $str, $fun_name);
     * }
     * $end_time = current_time();
     * $use_time = number_format(($end_time-$start_time), 3) * 1000;        //所用时长的毫秒数
     * echo '<BR>总耗时：'.$use_time.'ms';
     */
    public static function current_time()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

    /**
     * [checkDate 检查是否是日期格式]
     * @method   checkDate
     * @param    [type]                   $date_time [description]
     * @return   [type]                              [description]
     * @DateTime 2021-05-11T10:21:12+0800
     * @Author   Leen
     */
    public static function checkDate($date_time)
    {
        try {
            // if (strtotime($date_time) === strtotime(date('Y-m-d H:i:s', strtotime($date_time))) && strtotime($date_time) <= 2147483647) {
            if ($date_time === date('Y-m-d H:i:s', strtotime($date_time))) {
                return true;
            }
        } catch (\Exception $e) { }

        return false;
    }

    /**
     * [checkTime 检查是否是时间戳格式]
     * @method   checkTime
     * @param    [type]                   $date_time [description]
     * @return   [type]                              [description]
     * @DateTime 2021-05-11T10:21:30+0800
     * @Author   Leen
     */
    public static function checkTime($date_time)
    {
        try {
            if ($date_time === strtotime(date('Y-m-d H:i:s', $date_time)) || (ctype_digit($date_time) && $date_time <= 2147483647)) {
                return true;
            }
        } catch (\Exception $e) { }

        return false;
    }

    /**
     * [checkDateOrTime 检查是日期类型还是时间戳类型]
     * 临界值
     * 时间戳：2147483648
     * 日期：2038-01-19 03:14:07
     * @method   checkDateOrTime
     * @param    [type]                   $date_time [description]
     * @return   [type]                              [description]
     * @DateTime 2021-05-11T11:01:37+0800
     * @Author   Leen
     */
    public static function checkDateOrTime($date_time)
    {
        $type = '';
        switch (true) {
            case self::checkDate($date_time):
                $type = 'date';
                break;
            case self::checkTime($date_time):
                $type = 'time';
                break;
        }

        return $type;
    }

    /**
     * [dtPeriod 获取由指定时间往前推一段时间的日期数据]
     * @method   dtPeriod
     * @param    string                   $value [description]
     * @return   [type]                          [datetime]
     * @DateTime 2021-07-27T16:57:11+0800
     * @Author   Leen
     */
    public static function dtPeriod($date, $fmt = 'Y-m-d', $period=60 * 60 * 24)
    {
        return self::checkDateOrTime($date) == 'time' ? (date($fmt, ($date - $period))) : date($fmt, (strtotime($date) - $period) );
    }

    /**
     * [timeToDate 将时间戳转换为日期，支持超过int最大值2147483647的时间戳]
     * @method   timeToDate
     * @param    [type]                   $time     [时间戳]
     * @param    string                   $date_fmt [日期格式，如:Y-m-d H:i:s]
     * @param    string                   $timezone [时区，如:UTC、PRC、Asia/Shanghai]
     * @return   [type]                             [description]
     * @DateTime 2021-09-10T11:35:22+0800
     * @Author   Leen
     */
    public static function timeToDate($time, $date_fmt='Y-m-d H:i:s', $timezone='Asia/Shanghai')
    {
        $dateTimeObj = new \DateTime("@$time");
        $dateTimeObj->setTimeZone(new \DateTimeZone($timezone));
        return $dateTimeObj->format($date_fmt);
    }

    /**
     * [dateToTime 将日期转换为时间戳，可支持2038-01-19 11:14:07以后的日期]
     * @method   dateToTime
     * @param    [type]                   $date     [日期]
     * @param    string                   $timezone [时区，如:UTC、PRC、Asia/Shanghai]
     * @return   [type]                             [description]
     * @DateTime 2021-09-10T11:37:15+0800
     * @Author   Leen
     */
    public static function dateToTime($date, $timezone='UTC')
    {
        $dateTimeObj = new \DateTime("$date");
        $dateTimeObj->setTimeZone(new \DateTimeZone($timezone));
        return $dateTimeObj->format('U');
    }

    /**
     * [getWholeWeek 获取周的开始时间和结束时间(时间戳)]
     * @method   getWholeWeek
     * @param    integer                  $multiple [倍数，0:当前周;1:下一周; -1:前一周;]
     * @return   [type]                             [description]
     * @DateTime 2021-12-15T18:31:27+0800
     * @Author   Leen
     */
    public static function getWholeWeek($multiple=0)
    {
        return ['start_time'=>mktime(0, 0 , 0, date("m"),date("d")-date("w")+1+(7 * $multiple),date("Y")), 'end_time'=>mktime(23, 59, 59, date("m"),date("d")-date("w")+7+(7 * $multiple),date("Y"))];
    }

    /**
     * [getFirDayByMon 根据月份获取月份第一天]
     * @Author   Leen
     * @DateTime 2022-04-27T18:24:56+0800
     * @param    [type]                   $month [月份，如2022-04]
     * @param    string                   $fmt   [返回的日期格式，如：Y-m-d]
     * @return   [type]                   [description]
     */
    public static function getFirDayByMon($month, $fmt = 'Y-m-d')
    {
        //指定月份的第一天
        // $month = '2020-04';
        return date($fmt, strtotime("first day of {$month}"));
    }

    /**
     * [getLastDayByMon 根据月份获取月份最后一天]
     * @Author   Leen
     * @DateTime 2022-04-27T18:27:11+0800
     * @param    [type]                   $month [月份，如2022-04]
     * @param    string                   $fmt   [返回的日期格式，如：Y-m-d]
     * @return   [type]                   [description]
     */
    public static function getLastDayByMon($month, $fmt = 'Y-m-d')
    {
        return date($fmt, strtotime("{$month} +1 month -1 day"));
    }
}
