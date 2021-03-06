<?php

namespace fostercommerce\commerceinsights\formatters;

use Craft;
use fostercommerce\commerceinsights\Plugin;
use fostercommerce\commerceinsights\services\ParamParser;
use Tightenco\Collect\Support\Collection;
use yii\web\HttpException;

abstract class BaseFormatter
{
    public static $formatters = [];
    public static $key = false;

    /** @var ParamParser */
    protected $params;

    protected $min;
    protected $max;
    protected $step;
    protected $stepFormat;

    /** @var Collection */
    protected $empty;

    protected $showsCurrency = false;

    /** @var Collection */
    protected $data;

    /** @var Collection */
    protected $groupedData;

    public function __construct($data, $min = null, $max = null)
    {
        $this->params = Plugin::getInstance()->paramParser;
        $this->min = $min ?: $this->params->start();
        $this->max = $max ?: $this->params->end();
        $this->step = $this->params->step();
        $this->stepFormat = $this->params->stepFormat();
        $this->empty = $this->getEmptyCollection();

        $this->data = $data;
    }

    public function groupData($groupByF)
    {
        $this->groupedData = $this->data
            ->mapToGroups(function ($item) use ($groupByF) {
                return [($groupByF($item)) => $item];
            });
    }

    public static function addFormatter($formatter)
    {
        static::$formatters[$formatter::$key] = $formatter;
    }

    /**
     * @param $key
     * @return BaseFormatter
     */
    public static function getFormatter($formatter)
    {
        if (is_null($formatter)) {
            throw new HttpException(400, 'Invalid formatter');
        }

        $key = class_exists($formatter) ? $formatter::$key : $formatter;
        if (!empty(static::$formatters[$key])) {
            return static::$formatters[$key];
        }

        throw new HttpException(501, 'Invalid formatter');
    }

    /**
     * Whether the chart should show a dollar sign
     *
     * @return bool
     */
    public function showsCurrency()
    {
        return $this->showsCurrency;
    }

    public function totals()
    {
        return [];
    }

    /**
     * generate an empty collection because chart.js will not
     * automatically put in 0 values for missing data
     *
     * @return Collection
     */
    protected function getEmptyCollection()
    {
        $empty = collect([]);
        $pointer = strtotime($this->max);
        while ($pointer > strtotime($this->min)) {
            $empty[date($this->stepFormat, $pointer)] = collect([]);
            $pointer = strtotime("-{$this->step} seconds", $pointer);
        }

        return $empty;
    }

    abstract public function format();

    abstract public function csv();
}
