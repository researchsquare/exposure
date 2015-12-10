<?php

namespace Exposure\Specification;

use Exposure\Context;

interface FeatureSpecification
{
    /**
     * @return boolean
     */
    public function isSatisfiedBy($name, Context $context);

    /**
     * @param FeatureSpecification $other
     *
     * @return FeatureSpecification
     */
    public function andx(FeatureSpecification $other);

    /**
     * @param FeatureSpecification $other
     *
     * @return FeatureSpecification
     */
    public function orx(FeatureSpecification $other);

    /**
     * @return FeatureSpecification
     */
    public function not();
}
