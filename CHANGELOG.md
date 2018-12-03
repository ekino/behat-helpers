CHANGELOG
=========

master
------

* Allow symfony/stopwatch ^2.8
Fixed iWaitForCssElementBeingVisible / iWaitForCssElementBeingInvisible:
  * throw \RuntimeException to make test fail
  * make sure element is visible or not
