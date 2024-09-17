<?php declare(strict_types=1);

use Carbon\Carbon;

if (! function_exists('stampRounding')) {
  /**
     * 打刻丸め
     * 45～14分→0分
     * 15～44分→30分
     *
     * @param Carbon $stampTime
     * @return Carbon $roundTime | null
     */
    function stampRounding(?Carbon $stampTime)
    {
      if (is_null($stampTime)) return null;

      if ($stampTime->minute >= 45) {
          $roundTime = $stampTime->addHours(1)->minute(0)->second(0);
      } elseif ($stampTime->minute < 15) {
          $roundTime = $stampTime->minute(0)->second(0);
      } else {
          $roundTime = $stampTime->minute(30)->second(0);
      }
      return $roundTime;
    }
}