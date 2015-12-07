<?php

namespace Exposure;

use Alsar;

interface FeatureSpecification extends Alsar\Specification\Specification
{
    /**
     * @param $candidate Candidate : a value object representing the feature flag.
     * @return boolean
     */
    public function isSatisfiedBy($candidate);
}
