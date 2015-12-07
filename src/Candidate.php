<?php

namespace Exposure;

class Candidate
{
    protected $name;
    protected $context;

    public function __construct($name, Context $context)
    {
        $this->name = $name;
        $this->context = $context;
    }

    public function name()
    {
        return $this->name;
    }

    public function context()
    {
        return $this->context;
    }
}
