<?php

namespace Exposure\Specification;

use AB2_Selector_HashRandomizer;
use Exposure\Context;

class IsEnabledByPercentage extends CompositeSpecification
{
    protected $percentage;

    public function __construct($percentage)
    {
        $this->percentage = $percentage;
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($name, Context $context)
    {
        if (is_null($context->bucketingIdentity())) {
            return false;
        }

        // Map a combination of the feature name and feature identity to
        // an integer in the closed interval [1, 100].

        // Prime the randomizer with the feature's name to ensure that a single
        // user does not have every feature enabled or disabled at once.
        $randomizer = new AB2_Selector_HashRandomizer($name);

        $n = $randomizer->randomize($context->bucketingIdentity()) * 100;

        // Ensure we have an integer greater than 0.
        $n = max(1, ceil($n));

        return ($n <= $this->percentage);
    }
}
