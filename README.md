# Exposure

Research Square's Exposure library provides basic feature flagging capabilities, allowing you to selectively expose new features to a subset of your users. This library was inspired by Etsy's [Feature API](https://github.com/etsy/feature).

**This library is very much in its early stages of development and could change dramatically in the future, though we hope to keep the public API fairly stable.**

At present, features can be completely enabled or disabled, enabled for specific users, or enabled for a percentage of users, allowing gradual availability of new features.

Regardless of the method chosen, feature checks are performed with:

```php
if (Exposure\Feature::isEnabled('feature-name')) {
    // feature specific logic.
}
```

## Installation

Install the latest version with:

```bash
$ composer require researchsquare/feature
```

This package isn't currently available in Packagist. Until that happens, you'll need to add the following to composer.json:

```json
{
    "repositories": [
        {
            "type": "git",
            "url": "git@github.com:researchsquare/feature.git"
        }
    ]
}
```

## Integration

To use Exposure in your application, you will need to initialize it with your desired set of features and set the context under which it is operating. How this happens is largely an exercise for you to figure out, but features will typically be defined in your configuration, and Feature and its Context will be initialized during the bootstrapping of your application.

```php
<?php

use Exposure;

// $features is an array of features.
Exposure\Feature::defineFeatures($features);

// $context is an instance of Feature\Context.
Exposure\Feature::setContext($context);

```

## Configuration

### An enabled feature

    $configuration['feature-name'] = true;

### A disabled feature

    $configuration['feature-name'] = false;

### A feature enabled for specific users

    $configuration['feature-name'] = array(
        Exposure\Feature::USER => array(25, 26),
    );

### A feature enabled for 50% of users

    $configuration['feature-name'] = array(
        Exposure\Feature::PERCENTAGE => 50,
    );

Rules can be combined, as well.

### A feature enabled for specific users or 50% of users

    $configuration['feature-name'] = array(
        Exposure\Feature::USER => array(25, 26),
        Exposure\Feature::PERCENTAGE => 50,
    );

## Establishing context

Feature relies on Context to determine whether a feature is enabled. Context provides information about your application environment, such as the current user. Because establishing context will vary from application to application, it's up to the individual application to define. At present, context provides two pieces of information: the current user's identity and bucketing identity. Bucketing identity is used to identify a user regardless of whether they are anonymous or authenticated, and should remain unchanged throughout that process. This ensures feature availability is consistent for a given user. One method of ensuring this consistency is to store the bucketing identity in a cookie, but, again, this is an exercise left up to the application.
