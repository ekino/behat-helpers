CHANGELOG
=========

master
------

* Add doc for all available steps
* Add helpers for sonata-project/page-bundle
* Upgrade phpunit to ^8.5

v0.1.1
------

* Add GitHub pull request template
* Fix IwaitPageContains and IWaitPageNotContains

v0.1.0
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
* Add matrix and coveralls tool
* Add coverage coveralls badge
