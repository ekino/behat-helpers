CHANGELOG
=========

master
------

* Allow symfony/stopwatch ^2.8
* Fixed iWaitForCssElementBeingVisible / iWaitForCssElementBeingInvisible:
  * throw \RuntimeException to make test fail
  * make sure element is visible or not
* Enable CS rule to add backslash for compiler-optimized functions
* Id ending popin selector is now required for Sonata popin rules
* Drop php5.6 support
* Upgrade phpunit to ^7.5
* Disallow reloading cookies if reset cookies is called
* Add phpstan analysis
* Enable strict typing
