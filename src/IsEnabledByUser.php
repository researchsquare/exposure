<?php

namespace Exposure;

use Alsar;

class IsEnabledByUser extends Alsar\Specification\CompositeSpecification
    implements FeatureSpecification
{
    protected $users;

    public function __construct(array $users)
    {
        $this->users = $users;
    }

    public function isSatisfiedBy($candidate)
    {
        if (is_null($candidate->context()->userIdentity())) {
            return false;
        }

        return in_array($candidate->context()->userIdentity(), $this->users, true);
    }
}
