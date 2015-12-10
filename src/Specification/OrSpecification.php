<?php
namespace Exposure\Specification;

use Exposure\Context;

class OrSpecification extends CompositeSpecification
{
    /**
     * @var FeatureSpecification
     */
    protected $one;

    /**
     * @var FeatureSpecification
     */
    protected $other;

    /**
     * @param FeatureSpecification $x
     * @param FeatureSpecification $y
     */
    public function __construct(FeatureSpecification $x, FeatureSpecification $y)
    {
        $this->one   = $x;
        $this->other = $y;
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($name, Context $context)
    {
        return $this->one->isSatisfiedBy($name, $context)
            || $this->other->isSatisfiedBy($name, $context);
    }
}
