<?php

namespace fostercommerce\commerceinsights\twig;

class Extension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_Filter('wrap', function ($arr) {
                return array_map(function ($item) {
                    if (is_numeric($item)) {
                        return $item;
                    }

                    return '"' . $item . '"';
                }, $arr);
            }),
        ];
    }
}
