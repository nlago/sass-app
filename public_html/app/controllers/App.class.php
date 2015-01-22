<?php

/**
 * Created by PhpStorm.
 * User: rdok
 * Date: 10/12/2014
 * Time: 7:49 PM
 */
class App
{

    const WORKING_HOUR_START = 11;
    const WORKING_HOUR_END = 18;
    const NAME = "SASS App";
    const VERSION = "v1.5.0";
    const DATE_FORMAT = "m/d/Y g:i A";
    const PRODUCTION_HOST = "sass-app.in";

    const WORKING_DAY_END = 5;

    static function isWorkingDateTimeOn()
    {
        date_default_timezone_set('Europe/Athens');


        $curWorkingDate = new DateTime();

        $curWorkingHour = intval($curWorkingDate->format('H'));
        $curWorkingDay = intval($curWorkingDate->format('N'));

        // save resources - only run cron at working hours/day (monday - friday)
        if ($curWorkingHour < self::WORKING_HOUR_START || $curWorkingHour > self::WORKING_HOUR_END || $curWorkingDay > self::WORKING_DAY_END) {
            return false;
        }

        return true;
    }

    static function getCurWorkingDate()
    {
        date_default_timezone_set('Europe/Athens');

        $curWorkingDate = new DateTime();

        return $curWorkingDate;
    }

    public static function getDomainName()
    {
        if (strcmp($_SERVER['SERVER_NAME'], self::PRODUCTION_HOST) === 0) return "https://" . self::PRODUCTION_HOST;

        return "http://" . $_SERVER['SERVER_NAME'];
    }
}
