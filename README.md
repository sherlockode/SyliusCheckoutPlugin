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

To complete the installation, don't forget to publish assets:

```shell
$ php bin/console assets:install
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

That's it ! Now you can enable the Checkout payment method in your admin panel.
