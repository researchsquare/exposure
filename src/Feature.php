<?php

/**
 * This file is part of Research Square's Exposure library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Exposure;

use AB2_Selector_HashRandomizer;
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
            // $config['feature'] = array(Feature::PERCENTAGE => 50);
            if (array_key_exists(self::PERCENTAGE, $feature) && is_int($feature[self::PERCENTAGE])) {
                $enabled = self::isEnabledByPercentage($name, $feature[self::PERCENTAGE]);
                if ($enabled) {
                    self::log($name, $enabled, self::PERCENTAGE);
                    return true;
                }
            }

            // $config['feature'] = array(Feature::USER => array(25, 26));
            if (array_key_exists(self::USER, $feature) && is_array($feature[self::USER])) {
                $enabled = self::isEnabledByUser($feature[self::USER]);
                if ($enabled) {
                    self::log($name, $enabled, self::USER);
                    return true;
                }
            }
        }

        return false;
    }

    protected static function isEnabledByPercentage($name, $percentage)
    {
        if (is_null(self::$context->bucketingIdentity())) {
            return false;
        }

        // Map a combination of the feature name and feature identity to
        // an integer in the closed interval [1, 100].

        // Prime the randomizer with the feature's name to ensure that a single
        // user does not have every feature enabled or disabled at once.
        $randomizer = new AB2_Selector_HashRandomizer($name);

        $n = $randomizer->randomize(self::$context->bucketingIdentity()) * 100;

        // Ensure we have an integer greater than 0.
        $n = max(1, ceil($n));

        return ($n <= $percentage);
    }

    protected static function isEnabledByUser(array $users)
    {
        if (is_null(self::$context->userIdentity())) {
            return false;
        }

        return in_array(self::$context->userIdentity(), $users, true);
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
