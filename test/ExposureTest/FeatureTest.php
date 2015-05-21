<?php

namespace ExposureTest;

use Exposure;
use PHPUnit_Framework_Testcase;
use Psr\Log;

class FeatureTest extends PHPUnit_Framework_Testcase
{
    /**
     * A data provider driving the isEnabled test.
     */
    public function featureProvider()
    {
        $features = array();

        // Enabled always.
        $features['enabled-always'] = array(
            true,
            new Exposure\Context(null, null),
            true,
        );

        // Disabled always.
        $features['disabled-always'] = array(
            false,
            new Exposure\Context(null, null),
            false,
        );

        // Enabled for a specific user.
        $features['enabled-user'] = array(
            array(
                Exposure\Feature::USER => array(25, 26),
            ),
            new Exposure\Context(25, null),
            true,
        );

        // Disabled for a non-matching user.
        $features['disabled-user-non-matching'] = array(
            array(
                Exposure\Feature::USER => array(25, 26),
            ),
            new Exposure\Context(27, null),
            false,
        );

        // Disabled when the context provides no user identity.
        $features['disabled-user-null'] = array(
            array(
                Exposure\Feature::USER => array(25, 26),
            ),
            new Exposure\Context(null, null),
            false,
        );

        // Enabled for 50% of users based on bucketing identity.
        $features['enabled-percentage'] = array(
            array(
                Exposure\Feature::PERCENTAGE => 50,
            ),
            new Exposure\Context(null, 'bucketing-identity'),
            true,
        );

        // Disabled for 50% of users based on non-matching bucketing identity.
        $features['disabled-percentage-non-matching'] = array(
            array(
                Exposure\Feature::PERCENTAGE => 50,
            ),
            new Exposure\Context(null, '-non-matching-bucketing-identity'),
            false,
        );

        // Disabled when the context provides no bucketing identity.
        $features['disabled-percentage-null'] = array(
            array(
                Exposure\Feature::PERCENTAGE => 50,
            ),
            new Exposure\Context(null, null),
            false,
        );

        // Enabled for a specific user OR 50% of users based on bucketing identity.
        $features['enabled-user-or-percentage'] = array(
            array(
                Exposure\Feature::PERCENTAGE => 50,
                Exposure\Feature::USER => array(25, 345),
            ),
            new Exposure\Context(25, 'non-matching-bucketing-identity'),
            true,
        );

        return $features;
    }

    /**
     * @dataProvider featureProvider
     */
    public function testIsEnabled($feature, $context, $expectation)
    {
        Exposure\Feature::defineFeatures(array('feature' => $feature));
        Exposure\Feature::setContext($context);

        $this->assertEquals($expectation, Exposure\Feature::isEnabled('feature'));
    }

    public function testIsEnabledReturnsFalseForUndefinedFeature()
    {
        Exposure\Feature::defineFeatures(array());
        $this->assertFalse(Exposure\Feature::isEnabled('undefined-feature'));
    }

    public function testIsEnabledLogsTrueResultCorrectly()
    {
        $feature = 'feature';
        $status = 'enabled';
        $method = Exposure\Feature::USER;
        $user = 25;
        $bucket = null;

        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->setMethods(array('info'))
            ->getMock();

        $logger->expects($this->once())
            ->method('info')
            ->with($this->equalTo("$feature $status by $method for $user $bucket."));

        Exposure\Feature::defineFeatures(array($feature => array($method => array($user))));
        Exposure\Feature::setContext(new Exposure\Context($user, $bucket));
        Exposure\Feature::setLogger($logger);
        Exposure\Feature::isEnabled($feature);
    }
}
