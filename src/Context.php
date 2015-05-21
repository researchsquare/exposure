<?php

/**
 * This file is part of Research Square's Exposure library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Exposure;

/**
 * This class provides the application context a feature needs in order
 * to determine whether it is enabled.
 */
class Context
{
    /**
     * The identity of the current authenticated user.
     *
     * @var string|int
     */
    private $userIdentity;

    /**
     * The identity used for bucketing a user regardless of whether they
     * are anonymous or not. This identity should remain the same before
     * and after authentication.
     *
     * @var string|int
     */
    private $bucketingIdentity;

    public function __construct($userIdentity, $bucketingIdentity)
    {
        $this->userIdentity = $userIdentity;
        $this->bucketingIdentity = $bucketingIdentity;
    }

    public function userIdentity()
    {
        return $this->userIdentity;
    }

    public function bucketingIdentity()
    {
        return $this->bucketingIdentity;
    }
}
