# Sylius Checkout Plugin

## Overview

This plugin allows you to use checkout.com to receive credit card payments on your e-commerce website.

## Installation

Install the plugin with composer:

```shell
$ composer require sherlockode/sylius-checkout-plugin
```

If your project does not use autoload, you have to enable the bundle yourself:

```php
// config/bundle.php

return [
    ...
    
    Sherlockode\SyliusCheckoutPlugin\SherlockodeSyliusCheckoutPlugin::class => ['all' => true],
];
```

## Configuration

Update your sylius installation by importing bundle configuration:

```yaml
# config/packages/_sylius.yaml

imports:
    # ...

    - { resource: "@SherlockodeSyliusCheckoutPlugin/Resources/config/config.yaml" }
```

Then import routes:

```yaml
# config/routes.yaml

sherlockode_sylius_checkout_plugin:
    resource: "@SherlockodeSyliusCheckoutPlugin/Resources/config/routing.xml"
```

Update your webpack configuration to add an entry in shop config:

```javascript
// Shop config
Encore
    // ...
    .addEntry('sherlockode-checkout', './vendor/sherlockode/sylius-checkout-plugin/src/Resources/public/js/entry.js')
```

## Usage

### Payment method configuration

Now you only have to create a new payment method based on the Checkout.com type, 
then fill your credentials in the gateway configuration.
