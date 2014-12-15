class-resolver-bundle
==================

[![Latest Stable Version](https://poser.pugx.org/kassko/class-resolver-bundle/v/stable.png)](https://packagist.org/packages/kassko/class-resolver-bundle)
[![Total Downloads](https://poser.pugx.org/kassko/class-resolver-bundle/downloads.png)](https://packagist.org/packages/kassko/class-resolver-bundle)
[![Latest Unstable Version](https://poser.pugx.org/kassko/class-resolver-bundle/v/unstable.png)](https://packagist.org/packages/kassko/class-resolver-bundle)

Bundle which integrates class-resolver into Symfony.
---------------

Add to your composer.json:
```json

"require": {
    "kassko/class-resolver-bundle": "dev-master"
}

```

Register the bundle to your kernel:
```php
public function registerBundles()
{
    $bundles = array(
        new Kassko\Bundle\ClassResolverBundle\KasskoClassResolverBundle(),
    );
}
```
class-resolver allow to create object from its class name.

[class-resolver documentation]()
[Bundle documentation](src/Resources/doc/fr/documentation_fr.md)
