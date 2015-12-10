<?php

namespace Exposure\Specification;

use Exposure\Context;

/**
 * This specification will always be satisfied.
 */
class IsEnabled extends CompositeSpecification
{
    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($name, Context $context)
    {
        return true;
    }
}
