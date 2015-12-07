<?php

/**
 * This file is part of Research Square's Exposure library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Exposure;

use Exception;
use Psr\Log;

/**
 * This class provides the main entry point into the Exposure library. Once
 * features have been defined and a context set, feature checks are done via
 * the isEnabled method.
 */
class Feature
{
    const PERCENTAGE = 'percentage';
    const USER = 'user';

    private static $context;

    private static $features = array();

    private static $logger;

    private static $factory;

    /**
     * Define the features to consider.
     *
     * @param array $features The set of features to consider.
     */
    public static function defineFeatures(array $features)
    {
        self::$features = $features;
    }

    /**
     * Set the context under which feature checks will operate.
     *
     * @param Context $context The context to use.
     */
    public static function setContext(Context $context)
    {
        self::$context = $context;
    }

    /**
     * Set an optional logger to log the results of feature checks.
     *
     * @param Log\LoggerInterface $logger The logger to use.
     */
    public static function setLogger(Log\LoggerInterface $logger)
    {
        self::$logger = $logger;
    }

    public static function setFactory(FeatureFactory $factory)
    {
        self::$factory = $factory;
    }

    private static function factory()
    {
        if (is_null(self::$factory)) {
            self::$factory = new FeatureFactory();
        }

        return self::$factory;
    }

    /**
     * Return whether the specified feature is enabled.
     *
     * @param string $name The name of the feature.
     *
     * @return boolean
     */
    public static function isEnabled($name)
    {
        if (is_null(self::$context)) {
            throw new Exception('A context must be set before use.');
        }

        if (!array_key_exists($name, self::$features)) {
            return false;
        }

        $feature = self::$features[$name];

        // $config['feature'] = true;
        if (is_bool($feature)) {
            return $feature;
        }

        if (is_array($feature)) {
            $feature = self::factory()->create($feature);
        }

        $candidate = new Candidate($name, self::$context);

        $enabled = $feature->isSatisfiedBy($candidate);
        if ($enabled) {
            self::log($name, $enabled, get_class($feature));
            return true;
        }

        return false;
    }

    protected static function log($feature, $status, $method) {
        if (self::$logger) {
            $status = ($status) ? 'enabled' : 'disabled';
            $user = self::$context->userIdentity();
            $bucket = self::$context->bucketingIdentity();
            self::$logger->info("$feature $status by $method for $user $bucket.");
        }
    }
}
