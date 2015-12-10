<?php
namespace Exposure\Specification;

abstract class CompositeSpecification implements FeatureSpecification
{
    /**
     * {@inheritdoc}
     */
    public function andx(FeatureSpecification $other)
    {
        return new AndSpecification($this, $other);
    }

    /**
     * {@inheritdoc}
     */
    public function orx(FeatureSpecification $other)
    {
        return new OrSpecification($this, $other);
    }

    /**
     * {@inheritdoc}
     */
    public function not()
    {
        return new NotSpecification($this);
    }
}
