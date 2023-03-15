# Sylius Checkout Plugin

----

[ ![](https://img.shields.io/packagist/l/sherlockode/sylius-checkout-plugin) ](https://packagist.org/packages/sherlockode/sylius-checkout-plugin "License")
[ ![](https://img.shields.io/packagist/v/sherlockode/sylius-checkout-plugin) ](https://packagist.org/packages/sherlockode/sylius-checkout-plugin "Version")
[ ![](https://poser.pugx.org/sherlockode/sylius-checkout-plugin/downloads)](https://packagist.org/packages/sherlockode/sylius-checkout-plugin "Total Downloads")
[ ![Support](https://img.shields.io/badge/support-contact%20author-blue])](https://www.sherlockode.fr/contactez-nous/?utm_source=github&utm_medium=referral&utm_campaign=plugins_checkout)

## Table of Content

***

* [Overview](#overview)
* [Installation](#installation)
    * [Configuration](#configuration)
    * [Usage](#usage)
* [Demo](#demo-sylius-shop)
* [License](#license)
* [Contact](#contact)

# Overview

This plugin allows you to use checkout.com to receive credit card payments on your e-commerce website.

----

# Installation

----

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

----

## Configuration

----

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

----

## Usage

----

### Payment method configuration

Now you only have to create a new payment method based on the Checkout.com type, 
then fill your credentials in the gateway configuration.

![image](https://user-images.githubusercontent.com/12657400/225270680-a782f5d0-6aea-4b05-b315-d1f1b33603bc.png)

----

# Demo Sylius Shop

---

We created a demo app with some useful use-cases of plugins!
Visit [sylius-demo.sherlockode.fr](https://sylius-demo.sherlockode.fr/) to take a look at it. The admin can be accessed under
[sylius-demo.sherlockode.fr/admin/login](https://sylius-demo.sherlockode.fr/admin/login) link.
Plugins that we have used in the demo:

| Plugin name                  | GitHub                                                     | Sylius' Store |
|------------------------------|------------------------------------------------------------|---------------|
| Advance Content Bundle (ACB) | https://github.com/sherlockode/SyliusAdvancedContentPlugin | -             |
| Mondial Relay                | https://github.com/sherlockode/SyliusMondialRelayPlugin    | -             |
| Checkout Plugin              | https://github.com/sherlockode/SyliusCheckoutPlugin        | -             |
| FAQ                          | https://github.com/sherlockode/SyliusFAQPlugin             | -             |

## Additional resources for developers

---
To learn more about our contribution workflow and more, we encourage you to use the following resources:
* [Sylius Documentation](https://docs.sylius.com/en/latest/)
* [Sylius Contribution Guide](https://docs.sylius.com/en/latest/contributing/)
* [Sylius Online Course](https://sylius.com/online-course/)

## License

---

This plugin's source code is completely free and released under the terms of the MIT license.

[//]: # (These are reference links used in the body of this note and get stripped out when the markdown processor does its job. There is no need to format nicely because it shouldn't be seen.)

## Contact

---
If you want to contact us, the best way is to fill the form on [our website](https://www.sherlockode.fr/contactez-nous/?utm_source=github&utm_medium=referral&utm_campaign=plugins_checkout) or send us an e-mail to contact@sherlockode.fr with your question(s). We guarantee that we answer as soon as we can!
