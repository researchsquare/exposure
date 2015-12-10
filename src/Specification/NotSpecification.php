<?php
namespace Exposure\Specification;

use Exposure\Context;

class NotSpecification extends CompositeSpecification
{
    /**
     * @var FeatureSpecification
     */
    protected $specification;

    /**
     * @param FeatureSpecification $specification
     */
    public function __construct(FeatureSpecification $specification)
    {
        $this->specification = $specification;
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($name, Context $context)
    {
        return !$this->specification->isSatisfiedBy($name, $context);
    }
}
