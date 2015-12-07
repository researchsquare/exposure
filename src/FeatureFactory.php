<?php

namespace Exposure;

class FeatureFactory
{
    protected $mappings = [
        Feature::USER => 'Exposure\IsEnabledByUser',
        Feature::PERCENTAGE => 'Exposure\IsEnabledByPercentage',
    ];

    public function setMapping($name, $class)
    {
        $this->mappings[$name] = $class;
    }

    public function create($config)
    {
        $spec = null;

        foreach ($config as $feature=>$params) {
            $class = $this->mappings[$feature];

            if (isset($spec)) {
                $spec = $spec->orx(new $class($params));
            } else {
                $spec = new $class($params);
            }
        }

        return $spec;
    }
}
