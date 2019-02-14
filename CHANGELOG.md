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
