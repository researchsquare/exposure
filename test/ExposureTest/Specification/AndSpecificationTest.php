<?php

namespace Exposure\Specification;

use Exposure\Context;
use PHPUnit_Framework_Testcase;

class AndSpecificationTest extends PHPUnit_Framework_Testcase
{
    public function testBooleanLogic()
    {
        $context = new Context(null, null);
        $yes = new IsEnabled();
        $no = $yes->not();

        $spec = $yes->andx($yes);
        $this->assertTrue($spec->isSatisfiedBy('yes-yes', $context));

        $spec = $yes->andx($no);
        $this->assertFalse($spec->isSatisfiedBy('yes-no', $context));

        $spec = $no->andx($no);
        $this->assertFalse($spec->isSatisfiedBy('no-no', $context));
    }
}
