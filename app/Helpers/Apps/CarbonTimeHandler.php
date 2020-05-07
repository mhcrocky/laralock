<?php

/**
 * use libraries
 */

use Illuminate\Support\Carbon;

/**
 * use models
 */

/** */

function Carbon_atomConvertDateTime($datetime)
{
    return Carbon::parse($datetime)->format('Y-m-d\TH:i:s.uP');
}
/**
 * convert datetime
 * for DB
 *
 * @param date $datetime
 * @return void
 */
function Carbon_DBConvertDateTime($datetime)
{
    return Carbon::parse($datetime)->toDateTimeString();
}

/**
 * get date time now
 * for DB
 *
 * @return void
 */
function Carbon_DBtimeNow()
{
    return Carbon::now()->toDateTimeString();
}

/**
 * get date now
 * for DB
 *
 * @return void
 */
function Carbon_DBtimeToday()
{
    return Carbon::today()->toDateTimeString();
}

/**
 * get full date time now
 * for human
 *
 * @return void
 */
function Carbon_HumanFullDateTimeNow()
{
    return Carbon::now()->format('l, d F Y H:i:s');
}

/**
 * parse full date time
 * for human
 *
 * @param date $datetime
 * @return void
 */
function Carbon_HumanFullDateTime($datetime)
{
    return Carbon::parse($datetime)->format('l, d F Y H:i:s');
}

/**
 * parse date time
 * for human
 *
 * @param date $datetime
 * @return void
 */
function Carbon_HumanDateTime($datetime)
{
    return Carbon::parse($datetime)->format('d F Y H:i:s');
}

/**
 * convert  date time to date only
 * for DB
 *
 * @param date $datetime
 * @return void
 */
function Carbon_DBDateParse($datetime)
{
    return Carbon::parse($datetime)->format('Y-m-d');
}

/**
 * convert datetime to date only
 * for Human
 *
 * @param date $datetime
 * @return void
 */
function Carbon_HumanDateParse($datetime)
{
    return Carbon::parse($datetime ? $datetime : Carbon_DBtimeToday())->format('d F Y');
}

/**
 * convert datetime to display simple date
 * for Human
 *
 * @param date $datetime
 * @return void
 */
function Carbon_HumanDateSimpleDisplayParse($datetime = '')
{
    return Carbon::parse($datetime ? $datetime : Carbon_DBtimeNow())->format('D, M j');
}

/**
 * convert date time to interval time
 *
 * @param date $datetime
 * @return void
 */
function Carbon_diffForHumans($datetime)
{
    return Carbon::parse($datetime)->diffForHumans();
}

/**
 * get range date time
 * for Human
 *
 * @param date $start
 * @param date $end
 * @return void
 */
function Carbon_HumanRangeDateTimeDuration($start, $end)
{
    $checkStart = DBDateParse($start);
    $checkEnd = DBDateParse($end);
    if ($checkStart == $checkEnd) {
        return Carbon::parse($start)->format('l, d F Y') . '. ' . Carbon::parse($start)->format('H:i') . ' - ' . Carbon::parse($end)->format('H:i');
    } else {
        return Carbon::parse($start)->format('l, d F Y H:i') . ' - ' . Carbon::parse($end)->format('l, d F Y H:i');
    }
}

/**
 * get date yesterday by specified date
 *
 * @param date $datetime
 * @param integer $days
 * @return void
 */
function Carbon_DateSubYesterday($datetime, $days = 1)
{
    return Carbon::parse($datetime)->subDay($days)->toDateTimeString();
}

/**
 * get range date yesterday by today
 *
 * @param string $rangedate
 * @return void
 */
function Carbon_RangeDateYesterday($rangedate)
{
    if ($rangedate == 'today') {
        return Carbon_dateSubYesterday(Carbon_DBtimeToday(), 0);
    } elseif ($rangedate == 'yesterday') {
        return Carbon_dateSubYesterday(Carbon_DBtimeToday(), 1);
    } elseif ($rangedate == 'last-week') {
        return Carbon_dateSubYesterday(Carbon_DBtimeToday(), 7);
    } elseif ($rangedate == 'last-month') {
        return Carbon_dateSubYesterday(Carbon_DBtimeToday(), 30);
    } else {
        return Carbon_dateSubYesterday(Carbon_DBtimeToday(), 10000);
    }
}