<?php

namespace Exposure\Specification;

use Exposure\Context;
use PHPUnit_Framework_Testcase;

class OrSpecification extends PHPUnit_Framework_Testcase
{
    public function testBooleanLogic()
    {
        $context = new Context(null, null);
        $yes = new IsEnabled();
        $no = $yes->not();

        $spec = $yes->orx($yes);
        $this->assertTrue($spec->isSatisfiedBy('yes-yes', $context));

        $spec = $yes->orx($no);
        $this->assertTrue($spec->isSatisfiedBy('yes-no', $context));

        $spec = $no->orx($no);
        $this->assertFalse($spec->isSatisfiedBy('no-no', $context));
    }
}
