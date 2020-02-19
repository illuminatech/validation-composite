Upgrading Instructions for Laravel Composite Validation
=======================================================

!!!IMPORTANT!!!

The following upgrading instructions are cumulative. That is,
if you want to upgrade from version A to version C and there is
version B between A and C, you need to following the instructions
for both A and B.

Upgrade from 1.1.0
------------------

* Signature of the `DynamicCompositeRule` constructor has been changed, adding extra `$messages` parameter.
  Make sure to upgrade your code in case you are using `DynamicCompositeRule`.


Upgrade from 1.0.1
------------------

* "illuminate/support" package requirements were raised to 6.0. Make sure to upgrade your code accordingly.
