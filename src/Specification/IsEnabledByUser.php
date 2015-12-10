<?php

namespace Exposure\Specification;

use Exposure\Context;

class IsEnabledByUser extends CompositeSpecification
{
    protected $users;

    public function __construct(array $users)
    {
        $this->users = $users;
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($name, Context $context)
    {
        if (is_null($context->userIdentity())) {
            return false;
        }

        return in_array($context->userIdentity(), $this->users, true);
    }
}
