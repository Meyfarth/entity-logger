entity-logger
=============

Enable the bundle in your kernel :

TODO


If you use the security component with your own user entity (managed by Doctrine in your database), you can automatically log the current user by specifying the user_class parameter:

TODO

Don't forget to update your database schema to add the relation :

php app/console doctrine:schema:update --force
