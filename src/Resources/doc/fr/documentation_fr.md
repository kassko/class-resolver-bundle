class-resolver-bundle
==================

Bundle intégrant class-resolver dans Symfony.


Configurer la classe à résoudre:

```yml

<service id="cacheService" class="CacheClass">
    <tag name="class_resolver.add"/> <!-- On enregistre auprès du resolveur la classe "CacheClass" et son id de service correspondant "cacheService" -->
</service>

<service id="clientService" class="ClientClass">
    <argument type="service" id="class_resolver"/>

    <tag name="class_resolver.inject"/> <!-- On injecte le résolveur dans le service "clientService" -->
</service>

```

Configurer plusieurs classes à résoudre:
```yml

<service id="cacheService" class="CacheClass">
    <tag name="class_resolver.add"/> <!-- On enregistre auprès du resolveur la classe "CacheClass" et son id de service correspondant "cacheService" -->
</service>

<service id="listenerService" class="ListenerClass">
    <tag name="class_resolver.add"/> <!-- On enregistre auprès du resolveur la classe "ListenerClass" et son id de service correspondant "listenerService" -->
</service>

<service id="clientService" class="ClientClass">
    <argument type="service" id="class_resolver"/>

    <tag name="class_resolver.inject"/> <!-- On injecte le résolveur dans le service "clientService" -->
</service>

```

Travailler avec des résolveurs de classes contextuels.

Plus le résolveur de classes a un annuaire de relations classes/service conséquents, plus les demandes de résolution de nom de classe sont couteuses. Il convient donc d'éviter d'utiliser le même résolveur de classes partout et d'en créer un par thème.

L'exemple précédent peut-être réécrit de la façon suivante.

```yml

<service id="cacheService" class="CacheClass">
    <tag name="class_resolver.add" group="group1"/> <!-- On enregistre auprès d'un resolveur "group1" la classe "CacheClass" et son id de service correspondant "cacheService" -->
</service>

<service id="listenerService" class="ListenerClass">
    <tag name="class_resolver.add" group="group1"/> <!-- On enregistre auprès d'un resolveur "group1" la classe "ListenerClass" et son id de service correspondant "listenerService" -->
</service>

<service id="clientService" class="ClientClass">
    <argument type="service" id="class_resolver"/>

    <tag name="class_resolver.inject" group="group1"/> <!-- On injecte le résolveur "group1" dans le service "clientService" -->
</service>

```

En plus d'optimiser les performances de la résolution du nom de classe. Travailler avec plusieurs résolveurs de classes permet de résoudre des conflits de noms de classes qui pointent le même service.

Eviter des conflits:

Si 2 services ayant le même nom de classe sont taggés, une exception logique sera levée.

Il peut pourtant arriver de se trouver dans ce cas de figure ; avoir deux services avec le même nom de classe mais dans lesquels les dépendances ne sont pas les mêmes.

C'est aussi pour cette raison qu'il est possible d'enregistrer plusieurs résolveurs de classe en travaillant avec des groupes.

```yml

<service id="mailerServiceA" class="MailerClass">
    <argument type="service" id="transportA"/>
    <tag name="class_resolver.add" group="transportA"/>
</service>

<service id="mailerServiceB" class="MailerClass">
    <argument type="service" id="transportB"/>
    <tag name="class_resolver.add" group="transportB"/>
</service>

<service id="clientService" class="ClientClass">

    <argument type="service" id="class_resolver"/>
    <argument type="service" id="class_resolver"/>

    <tag name="class_resolver.inject" group="transportA" index=0/>
    <!-- On injecte le résolveur "transportA" en tant que premier résolveur du service "clientService" -->

    <tag name="class_resolver.inject" group="transportB" index=1/>
    <!-- On injecte le résolveur "transportB" en tant que second résolveur du service "clientService" -->
</service>

```

Quand l'index n'est pas précisé, il vaut 0 par défaut.

```php
<?php

class ClientClass
{
    private $config;
    private $classResolverA;
    private $classResolverB;

    public function __construct($classResolverA, $classResolverB)
    {
        $this->classResolverA = $classResolverA;
        $this->classResolverB = $classResolverB;
    }

    public function clientFunction()
    {
        //...
        $mailerClassA = $config->getMailerClassA();
        $myMailerInstanceA = $classResolverA->resolve($mailerClassA);

        $mailerClassB = $config->getMailerClassB();
        $myMailerInstanceB = $classResolverB->resolve($mailerClassB);
        //...
    }
}

```

Variante avec injection par setters.

```yml

<service id="mailerServiceA" class="MailerClass">
    <argument type="service" id="transportA"/>
    <tag name="class_resolver.add" group="transportA"/>
</service>

<service id="mailerServiceB" class="MailerClass">
    <argument type="service" id="transportB"/>
    <tag name="class_resolver.add" group="transportB"/>
</service>

<service id="clientService" class="ClientClass">

    <call method="setClassResolverA">
        <argument type="service" id="class_resolver"/>
    </call>

    <call method="setClassResolverB">
        <argument type="service" id="some_serviceA"/>
        <argument type="service" id="some_serviceB"/>
        <argument type="service" id="class_resolver"/>
    </call>

    <tag name="class_resolver.inject" group="transportA" method="setClassResolverA" index=0/>
    <tag name="class_resolver.inject" group="transportB" method="setClassResolverB" index=2/>
</service>

```

Idem avec les setters, quand l'index n'est pas précisé, il vaut 0 par défaut.