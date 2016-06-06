class-resolver-bundle
===================

[![Build Status](https://secure.travis-ci.org/kassko/class-resolver-bundle.png?branch=master)](https://travis-ci.org/kassko/class-resolver-bundle)
[![Latest Stable Version](https://poser.pugx.org/kassko/class-resolver-bundle/v/stable.png)](https://packagist.org/packages/kassko/class-resolver-bundle)
[![Total Downloads](https://poser.pugx.org/kassko/class-resolver-bundle/downloads.png)](https://packagist.org/packages/kassko/class-resolver-bundle)
[![Latest Unstable Version](https://poser.pugx.org/kassko/class-resolver-bundle/v/unstable.png)](https://packagist.org/packages/kassko/class-resolver-bundle)

Bundle which integrates [`class-resolver`](https://github.com/kassko/class-resolver) into Symfony. Please for natives features, take a look at the [`class-resolver documentation`](https://github.com/kassko/class-resolver/blob/master/README.md).


## Installation

You can install this component with composer
`composer require kassko/class-resolver-bundle:some_version`

You also need to register the bundle to your kernel:
```php
public function registerBundles()
{
    $bundles = array(
        new Kassko\Bundle\ClassResolverBundle\KasskoClassResolverBundle(),
    );
}
```

## There are two differents ways to use the class-resolver

### Example of semantic configuration

```yaml
kassko_class_resolver:
    container_adapter_class: Kassko\Bundle\ClassResolverBundle\Adapter\Container\SymfonyContainerAdapter # default
    resolver_aliases:
    	alias_one: my_container_resolver_service_one
    	# - {alias_one: my_container_resolver_service_one}
    resolvers:
        container:
            [my_container_resolver_service_one, my_container_resolver_service_two]
            # or
            # - { resolver_service: my_container_resolver_service_one, resolver_aliases: [alias_two] }
            # - { resolver_service: my_container_resolver_service_two }
        map:
            map_one:
                resolver_service: my_map_resolver_service_one
                items:
                    "My\\Namespace": my_service_one
                    "My\\Namespace\\Two": my_service_two
                    # or
                    # - { class: "My\\Namespace", service: my_service_one }
                    # - { class: "My\\Namespace\\Two", service: my_service_two }
        factory_adapter:
            - resolver_service: my_factory_adapter_resolver_service_one
              adapted_service: my_adapted_service_one
              support_method: supports
              resolve_method: resolve
            - resolver_service: my_factory_adapter_resolver_service_two
              adapted_service: my_adapted_service_two
              support_method: supports
              resolve_method: resolve 
            # or with the following syntax
            - {resolver_service: adapter_one, adapted_factory: resolver_one} # etc            
        static_factory_adapter:
            - resolver_service: my_static_factory_adapter_resolver_service_one
              adapted_class: my_static_resolver_class
              support_method: supports
              resolve_method: resolve
            - resolver_service: my_static_factory_adapter_resolver_service_two
              adapted_class: my_static_resolver_class
              support_method: supports
              resolve_method: resolve
            # or with the following syntax
            - {resolver_service: adapter_two, adapted_factory: resolver_two} # etc           
```

### You can define some class resolvers in semantic configuration and use them in you service configuration file

```yaml
<service id="some_service_a" class="stdClass">
    <argument id="my_map_resolver_service_one" type="service" />
</service>

<service id="some_service_b" class="stdClass">
    <argument id="my_factory_adapter_resolver_service" type="service" />
</service>

<service id="some_service_c" class="stdClass">
    <argument id="my_static_factory_adapter_resolver_service" type="service" />
</service>
```

### You can both define some class resolvers on the fly and feed them with pairs [class, service] all from your service configuration file

You register your service like pairs [class, service] to a resolver you create on the fly:

```yaml
<service id="some_service_a" class="stdClass">
    <argument id="my_container_resolver_service_one" type="service" />
    <!-- create a resolver on the fly identified by its group -->
    <tag name kassko_class_resolver.add group="a_group_name_choosen_by_you"> 
</service>

<service id="some_service_b" class="stdClass">
    <argument id="my_container_resolver_service_one" type="service" />
    <!-- use the resolver previously created in the service `some_service_a` -->
    <tag name kassko_class_resolver.add group="a_group_name_choosen_by_you"> 
</service>
```

And you inject the good resolver (identified by it's group) in the concerned services:
```yaml
<service id="some_service_c" class="stdClass">
    <tag name kassko_class_resolver.inject group="a_group_name_choosen_by_you">
</service>
```

A group is a way not to manipulate some service.

This way to do has the advantage to add some new resolvers without changing the semantic configuration and to use work with only one file (the service configuration file).

For more information about this last feature, please read the [more detailed documentation](src/Resources/doc/fr/documentation_fr.md)

### Finally, you can use some class resolvers defined on the semantic configuration and feed them from your service configuration file

You register your service like a pair [class, service] in the already existing resolver `my_container_resolver_service_one`:

```yaml
<service id="some_service_a" class="stdClass">
    <tag name kassko_class_resolver.add service="my_container_resolver_service_one">
</service>
```

You inject the resolver `my_container_resolver_service_one` in a service which need it:

```yaml
<service id="some_service_a" class="stdClass">
    <argument id="my_container_resolver_service_one" type="service" />
</service>
```
