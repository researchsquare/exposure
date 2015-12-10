<?php

namespace Exposure\Specification;

use Exposure\Feature;

/**
 * This Factory is used to instantiate Feature Specifications.
 *
 * The default behavior for combining specificaitons is OR.
 * Provided an array like:
 *
 *  array(
 *      'user' => array(1, 2, 3),
 *      'user' => array(4, 5, 6),
 *  )
 *
 * any user 1 through 6 will be included.
 */
class FeatureFactory
{
    protected $mappings = [
        Feature::USER => IsEnabledByUser::class,
        Feature::PERCENTAGE => IsEnabledByPercentage::class,
    ];

    /**
     * Add a mapping to the default set.
     *
     * @param string $name The name to match in a provided config.
     * @param string $class The classpath to instantiate.
     */
    public function setMapping($name, $class)
    {
        $this->mappings[$name] = $class;
    }

    /**
     * Parse a config array and return a Feature Specification.
     *
     * @param array $config The config array to use.
     * @return FeatureSpecification
     */
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
