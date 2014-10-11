entity-logger
=============

*By Sébastien Garcia*

This bundle allows you to log every modification made to your entities, just by implementing the ``EntityLoggerInterface`` interface.

## Installation

Using Composer:

    php composer.phar require meyfarth/entity-logger dev-master

Enable the bundle in your kernel :

```php
// app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new \Meyfarth\EntityLoggerBundle\MeyfarthEntityLoggerBundle(),
        );
        
        ...
        
        return $bundles;
    }
```

Update your database :

    php app/console doctrine:schema:update --force
    
This will create a ``meyfarth_entity_log`` table. You can dump your database first using `--dump-sql` option if you want to know the table structure.

## Usage

To log an entity, implement the ``EntityLoggerInterface`` interface in the entities you want to log :
```php
<?php

namespace MyApp\MyBundle\Entity;

use Meyfarth\EntityLoggerBundle\Entity\EntityLoggerInterface;

/**
 * MyEntity
 */
class MyEntity implements EntityLoggerInterface
{
  // Your code
}
```

## Configuration
```yml
# app/config/config.yml
meyfarth_entity_logger:
    enable: true # default false. If not enabled, nothing will be logged
    log:
        create: true  # log on new entities
        update: true  # log on updates, new entities will not be considered as updates
        delete: true  # log on deletion
    user_class: false # log the current user (see below for explainations)
```


If you use the security component with your own user entity (managed by Doctrine in your database), you can automatically log the current user by specifying the user_class parameter:

```yml
# app/config/config.yml
meyfarth_entity_logger:
    user_class: MyApp\MyBundle\Entity\MyUser
```
