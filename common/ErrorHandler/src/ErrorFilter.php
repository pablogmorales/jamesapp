<?php

namespace Ddm\ErrorHandler;

class ErrorFilter {

    protected $class;

    protected $method;

    protected $filters = [];

    protected $valid = false;

    public function __construct($class, $method, array $filters)
    {
        $this->class = $class;
        $this->method = $method;
        $this->filters = $filters;
    }

    public function run($params) {
        $this->valid = !(reset($this->filters) === false && key($this->filters) === null);
        if ($this->valid) {
            $next = current($this->filters);
            return call_user_func($next, $this->class, $params, $this);
        }
        return false;
    }

    public function __invoke($params)
    {
        $this->valid = !(next($this->filters) === false && key($this->filters) === null);
        if ($this->valid) {
            $next = current($this->filters);
            return call_user_func($next, $this->class, $params, $this);
        }
        return false;
    }

    /**
     * @deprecated
     * @see ErrorFilter::__invoke()
     */
    public function next($self = null, $params = null, $chain = null)
    {
        return $this->__invoke($params);
    }
}