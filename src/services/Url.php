<?php

namespace fostercommerce\commerceinsights\services;

use yii\base\Component;

// TODO: This should be a Variable
class Url extends Component
{
    private $urlParts = [];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->urlParts = parse_url(\Craft::$app->request->absoluteUrl);
    }

    public function create()
    {
        return new static;
    }

    public function __call($key, $args)
    {
        $value = @$args[0];

        if ($value === null) {
            $method = 'get'.ucfirst($key);
            if (method_exists($this, $method)) {
                return call_user_func_array([$this, $method], $args);
            }

            return @$this->urlParts[$key];
        }

        $method = 'set'.ucfirst($key);
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $args);
        }

        $this->urlParts[$key] = $value;
        return $this;
    }

    public function setPath($path)
    {
        $this->urlParts['path'] = '/'.ltrim($path, '/');
        return $this;
    }

    public function query($key=null, $value=null)
    {
        if ($value !== null) {
            return $this->setQuery($key, $value);
        }

        return $this->getQuery($key);
    }

    public function getQuery($key=null)
    {
        if ($key === null) {
            return $this->urlParts['query'];
        }

        $params = [];
        parse_str(@$this->urlParts['query'], $params);
        return @$params[$key];
    }

    public function setQuery($key, $value)
    {
        $params = [];
        parse_str(@$this->urlParts['query'], $params);

        $params[$key] = $value;
        $this->urlParts['query'] = http_build_query($params);

        return $this;
    }

    public function removeQuery($key)
    {
        $params = [];
        parse_str(@$this->urlParts['query'], $params);

        unset($params[$key]);
        $this->urlParts['query'] = http_build_query($params);

        return $this;
    }

    public function removeAllQuery()
    {
        unset($this->urlParts['query']);
        return $this;
    }

    public function unparseUrl()
    {
        $parts = $this->urlParts;

        $scheme   = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
        $host     = isset($parts['host']) ? $parts['host'] : '';
        $port     = isset($parts['port']) ? ':' . $parts['port'] : '';
        $user     = isset($parts['user']) ? $parts['user'] : '';
        $pass     = isset($parts['pass']) ? ':' . $parts['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parts['path']) ? $parts['path'] : '';
        $query    = isset($parts['query']) ? '?' . $parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    public function parts()
    {
        return $this->urlParts;
    }

    public function toString()
    {
        return (string)$this;
    }

    public function __toString()
    {
        return $this->unparseUrl();
    }
}
