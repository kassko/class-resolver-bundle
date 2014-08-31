class-resolver-bundle
==================

Bundle wich integrates class-resolver into Symfony.
---------------

Add to your composer.json:
```json

"require": {
    "kko/class-resolver-bundle": "dev-master"
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