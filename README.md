# Behat Helpers

[![Latest Stable Version](https://poser.pugx.org/ekino/behat-helpers/v/stable)](https://packagist.org/packages/ekino/behat-helpers)
[![Build Status](https://travis-ci.org/ekino/behat-helpers.svg?branch=master)](https://travis-ci.org/ekino/behat-helpers)
[![Total Downloads](https://poser.pugx.org/ekino/behat-helpers/downloads)](https://packagist.org/packages/ekino/behat-helpers)

This library provides some helpers over Behat.

This is a *work in progress*, so if you'd like something implemented please
feel free to ask for it or contribute to help us!

## Installation

Require it using [Composer](https://getcomposer.org/):

```bash
composer require --dev ekino/behat-helpers
```

And use the helpers you need in your FeatureContext class.

### BaseUrlTrait

Behat handles only one base URL to run the tests suites. In order to test a
multisite-application, this trait allows you to inject a base URL by suite.

```yaml
# behat.yml
default:
    suites:
        suite_1:
            contexts:
                - Tests\Behat\Context\MyFeatureContext:
                    - https://foo.bar.localdev
        suite_2:
            contexts:
                - Tests\Behat\Context\MyFeatureContext:
                    - https://bar.foo.localdev
```

```php
// your feature context

namespace Tests\Behat\Context;

use Behat\MinkExtension\Context\MinkContext;
use Ekino\BehatHelpers\BaseUrlTrait;

class MyFeatureContext extends MinkContext
{
    use BaseUrlTrait;

    public function __construct($baseUrl)
    {
        $this->setBaseUrl($baseUrl);
    }
}
```

### DebugTrait

This trait can be used for debug purposes. It captures both HTML and screenshot
when a step fails, so you can see the page at this moment.

Note that it requires `KernelDictionary` from [behat/symfony2-extension][1].

```php
// your feature context

namespace Tests\Behat\Context;

use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Ekino\BehatHelpers\DebugTrait;

class MyFeatureContext extends MinkContext
{
    use DebugTrait;
    use KernelDictionary;
}
```

You can also profile your tests by adding the tag `behat_helpers_profile` on
the feature or scenario. You'll see the consumed memory and the execution time
for each scenario.

```gherkin
@behat_helpers_profile
Feature: Front test
  I want to be able to access to the application
```

```gherkin
@behat_helpers_profile
Scenario: foo elt should be visible in 2 seconds or less
    Given ...
    Then I wait for "foo" element being visible for 2 seconds
```

### ExtraSessionTrait

This trait provides some helpers over the session.

```php
// your feature context

namespace Tests\Behat\Context;

use Behat\MinkExtension\Context\MinkContext;
use Ekino\BehatHelpers\ExtraSessionTrait;

class MyFeatureContext extends MinkContext
{
    use ExtraSessionTrait;
}
```

```gherkin
Scenario: foo elt should be visible in 2 seconds or less
    Given ...
    Then I wait for "foo" element being visible for 2 seconds
```

### ExtraWebAssertTrait

This trait provides some extra asserts.

```php
// your feature context

namespace Tests\Behat\Context;

use Behat\MinkExtension\Context\MinkContext;
use Ekino\BehatHelpers\ExtraWebAssertTrait;

class MyFeatureContext extends MinkContext
{
    use ExtraWebAssertTrait;
}
```

### ReloadCookiesTrait

This trait aims to write small/simple scenarii and preserve execution time. To
do so, the cookies are reloaded between the scenarii. This can be useful in
case of a multistep form: the first scenario fills the first step and submits
(here the cookies are saved), then the second scenario is executed (the cookies
are reloaded so no need to do the previous step again) and fills the second
step...etc.

```php
// your feature context

namespace Tests\Behat\Context;

use Behat\MinkExtension\Context\MinkContext;
use Ekino\BehatHelpers\ReloadCookiesTrait;

class MyFeatureContext extends MinkContext
{
    use ReloadCookiesTrait;

    /**
     * @When /^I fill the first step$/
     */
    public function fillStep1()
    {
        $this->doOnce(function () {
            $this->iAmOnHomepage();
            $this->fillField('input_step1', 'foo');
            $this->pressButton('Next');
        });
    }

    /**
     * @When /^I fill the second step$/
     */
    public function fillStep2()
    {
        $this->doOnce(function () {
            $this->fillStep1();
            $this->fillField('input_step2', 'bar');
            $this->pressButton('Next');
        });
    }
}
```

```gherkin
Scenario: I can fill the step1
    Given I fill the first step
    Then I should be on "/step2"
    
Scenario: I can fill the step2
    Given I fill the second step
    Then I should be on "/step3"
```

You can add the tag `behat_helpers_no_cache` to avoid cookies being saved/reloaded:

```gherkin
@behat_helpers_no_cache
Scenario: I can fill the step2
    Given I fill the second step
    Then I should be on "/step3"
```

### ReloadDatabaseTrait

This trait allows you to restore the database at the end of a scenario as it
was before the scenario starts. It can be useful if a scenario alters the
database, so the scenarii can be independent.

Of course, it can take a while with a big database.

For now, only MySQL is supported. It requires `mysqldump` to be installed to
export the data, and [doctrine/doctrine-bundle][2] to re-import the dump.

Note that it requires `KernelDictionary` from [behat/symfony2-extension][1].

```php
// your feature context

namespace Tests\Behat\Context;

use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Ekino\BehatHelpers\ReloadDatabaseTrait;

class MyFeatureContext extends MinkContext
{
    use KernelDictionary;
    use ReloadDatabaseTrait;
}
```

```gherkin
@behat_helpers_restore_db
Scenario: I can fill the step2
    Given I fill the second step
    Then I should be on "/step3"
```

### RouterAwareTrait

This helper uses the router from Symfony and so avoids hard-coded URL in your
scenarii.

Note that it requires `KernelDictionary` from [behat/symfony2-extension][1].

```php
// your feature context

namespace Tests\Behat\Context;

use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Ekino\BehatHelpers\RouterAwareTrait;

class MyFeatureContext extends MinkContext
{
    use KernelDictionary;
    use RouterAwareTrait;
}
```

```gherkin
Scenario: I can see "something" when I visit /foo
    Given I am on "my_route_id"
    Then I should see "something"
```

If your route requires some parameters, you can provide them by separating them
to the route identifier with a `;`:

```gherkin
Scenario: I can see "something" when I visit /foo/1/2
    Given I am on "my_route_id;param1=1&param2=2"
    Then I should see "something"
```

### SonataAdminTrait

This trait integrates [sonata-project/admin-bundle][3] with some basics like
interaction with menu, navigation bar, poping, select2... You can combined it
with the [ReloadCookiesTrait](#reloadcookiestrait) in order to login only once
for instance, and with [RouterAwareTrait](#routerawaretrait) to use route ids.

Note that it requires `KernelDictionary` from [behat/symfony2-extension][1].

```php
// your feature context

namespace Tests\Behat\Context;

use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Ekino\BehatHelpers\ReloadCookiesTrait;
use Ekino\BehatHelpers\RouterAwareTrait;
use Ekino\BehatHelpers\SonataAdminTrait;

class MyFeatureContext extends MinkContext
{
    use KernelDictionary;
    use ReloadCookiesTrait;
    use RouterAwareTrait;
    use SonataAdminTrait;

    /**
     * @When /^I login with username "(?P<username>[^"]*)" and password "(?P<password>[^"]*)"$/
     *
     * @param string $username
     * @param string $password
     */
    public function fillLoginForm($username, $password)
    {
        $this->doOnce(function () use ($username, $password) {
            $this->login($username, $password);
        });
    }
}
```

```gherkin
Scenario: I can login and then access to the admin dashboard
    Given I login with username "admin" and password "admin"
    Then I should be on "sonata_admin_dashboard"
    And I should see "Welcome to the admin dashboard"
```

[1]: https://github.com/Behat/Symfony2Extension
[2]: https://github.com/doctrine/DoctrineBundle
[3]: https://github.com/sonata-project/SonataAdminBundle
