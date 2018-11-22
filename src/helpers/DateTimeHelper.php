<?php
namespace fostercommerce\commerceinsights\helpers;

use DateTime;

class DateTimeHelper
{
    public static function startOf($period = 'w')
    {
        switch ($period) {
            case 'd':
                $date = new DateTime(date('y-m-d'));
                break;
            case 'm':
                $year = date('y');
                $month = date('m');
                $date = new DateTime("{$year}-{$month}-1");
                break;
            case 'q':
                $year = date('y');
                $month = date('m');

                if ($month >= 1 && $month < 4) {
                    $startMonth = 1;
                } elseif ($month >= 4 && $month < 7) {
                    $startMonth = 4;
                } elseif ($month >= 7 && $month < 10) {
                    $startMonth = 7;
                } else {
                    $startMonth = 10;
                }

                $date = new DateTime("{$year}-{$startMonth}-1");
                break;
            case 'y':
                $year = date('y');
                $date = new DateTime("{$year}-1-1");
                break;
            case 'w':
            default:
                $dayOfWeek = date('w');
                $date = (new DateTime(date('y-m-d')))->modify("-{$dayOfWeek} days");
        }

        return $date;
    }

    public static function endOf($period = 'w')
    {
        $date = self::startOf($period);
        switch ($period) {
            case 'd':
                $date->modify('+1 days');
                break;
            case 'm':
                $date->modify('+1 months');
                break;
            case 'q':
                $date->modify('+3 months');
                break;
            case 'y':
                $date->modify('+1 years');
                break;
            case 'w':
            default:
                $date->modify('+1 weeks');
        }

        return $date;
    }

    public static function getDateRange($range = 'this-week')
    {
        $mapping = [
            'today' => function () {
                return [self::startOf('d'), self::endOf('d')];
            },
            'this-week' => function () {
                return [self::startOf('w'), self::endOf('w')];
            },
            'this-month' => function () {
                return [self::startOf('m'), self::endOf('m')];
            },
            'this-quarter' => function () {
                return [self::startOf('q'), self::endOf('q')];
            },
            'this-year' => function () {
                return [self::startOf('y'), self::endOf('y')];
            },
            'yesterday' => function () {
                return [
                    self::startOf('d')->modify('-1 days'),
                    self::endOf('d')->modify('-1 days'),
                ];
            },
            'prev-week' => function () {
                return [
                    self::startOf('w')->modify('-1 weeks'),
                    self::endOf('w')->modify('-1 weeks'),
                ];
            },
            'prev-month' => function () {
                return [
                    self::startOf('m')->modify('-1 months'),
                    self::endOf('m')->modify('-1 months'),
                ];
            },
            'prev-quarter' => function () {
                $x = [
                    self::startOf('q')->modify('-3 months'),
                    self::endOf('q')->modify('-3 months'),
                ];

                return $x;
            },
            'prev-year' => function () {
                return [
                    self::startOf('y')->modify('-1 years'),
                    self::endOf('y')->modify('-1 years'),
                ];
            },
        ];

        return $mapping[$range]();
    }
}
