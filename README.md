# Behat Helpers

[![Latest Stable Version](https://poser.pugx.org/ekino/behat-helpers/v/stable)](https://packagist.org/packages/ekino/behat-helpers)
[![Build Status](https://travis-ci.org/ekino/behat-helpers.svg?branch=master)](https://travis-ci.org/ekino/behat-helpers)
[![Coverage Status](https://coveralls.io/repos/ekino/behat-helpers/badge.svg?branch=master&service=github)](https://coveralls.io/github/ekino/behat-helpers?branch=master)
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

| Step | Regex |
| --- | --- |
| I wait for 2 seconds | `/^I wait for (\d+) seconds?$/` |
| I wait for "foo" element being visible for 2 seconds | `/^I wait for "([^"]*)" element being visible for (\d+) seconds$/` |
| I wait for "Foo" element being invisible for 2 seconds | `/^I wait for "([^"]*)" element being invisible for (\d+) seconds$/` |
| I scroll to 123 and 987 | `/^I scroll to (\d+) and (\d+)?$/` |
| I wait 3 seconds that page contains text "Foo" | `/^I wait (\d+) seconds that page contains text "([^"]*)"$/` |
| I wait 3 seconds that page not contains text "Bar" | `/^I wait (\d+) seconds that page not contains text "([^"]*)"$/` |
| I click on button containing "Foo" | `/^I click on (?:link\|button) containing "(?P<text>[^"]*)"$/` |

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

| Step | Regex |
| --- | --- |
| the "Foo" element should have attribute "Bar" | `/^the "(?P<element>[^"]*)" element should have attribute "(?P<value>(?:[^"]\|\\")*)"$/` |
| I click the "Foo" element| `/^I click the "(?P<element>[^"]*)" element$/` |
| I should see at least 2 "Bar" elements | `/^(?:\|I )should see at least (?P<num>\d+) "(?P<element>[^"]*)" elements?$/` |

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

You can add the tag `behat_helpers_reset_cache` to clear cookies previously saved:

```gherkin
@behat_helpers_reset_cache
Scenario: I am on /step1 if previous cookies are reset
    Given I fill the second step
    Then I should be on "/step3"
    But I am on "/step1"
```

| Step | Regex |
| --- | --- |
| I fill the first step | `/^I fill the first step$/` |
| I fill the second step | `/^I fill the second step$/` |

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

| Step | Regex |
| --- | --- |
| I open the menu "Foo" | `/^I open the menu "([^"]*)"$/` |
| I should see "Foo" action in navbar | `/^I should see "([^"]*)" action in navbar$/` |
| I should not see "Foo" action in navbar | `/^I should not see "([^"]*)" action in navbar$/` |
| I click on "Foo" action in navbar | `/^I click on "([^"]*)" action in navbar$/` |
| clicking on the "Foo" element should open a popin "Bar" | `/^clicking on the "([^"]*)" element should open a popin "([^"]*)"$/` |
| the popin "Foo" should be closed | `/^the popin "([^"]*)" should be closed$/` |
| the popin "Foo" should not be opened | `/^the popin "([^"]*)" should not be opened$/` |
| the popin "Foo" should be opened | `/^the popin "([^"]*)" should be opened$/` |
| I set the select2 field "Foo" to "Bar" | `/^(?:\|I )set the select2 field "(?P<field>(?:[^"]\|\\")*)" to "(?P<textValues>(?:[^"]\|\\")*)"$/` |
| I set the select2 value "Foo" for "Bar" | `/^(?:\|I )set the select2 value "(?P<textValues>(?:[^"]\|\\")*)" for "(?P<field>(?:[^"]\|\\")*)"$/` |


### SonataPageAdminTrait

This trait integrates [sonata-project/page-bundle][4] with some basics like interaction with container, block...
You can combined it with the [ReloadCookiesTrait](#reloadcookiestrait) and with [RouterAwareTrait](#routerawaretrait) to use route ids.

```php
// your feature context

namespace Tests\Behat\Context;

use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Ekino\BehatHelpers\SonataPageAdminTrait;

class MyFeatureContext extends MinkContext
{
    use KernelDictionary;
    use ReloadCookiesTrait;
    use RouterAwareTrait;
    use SonataPageAdminTrait;
}
```

```gherkin
Scenario: I can see "Simple text block" when I add a SimpleTextBlockService
Given     I login
Then      I am on "admin_app_sonata_page_compose;id=1"
And       I open the container by text "Content"
And       I add the block "Simple text" with the name "Foo"
And       I open the block "Foo"
And       The block "Foo" should be opened
And       I rename the block "Foo" with "Bar"
And       I submit the block "Foo"
And       The block "Bar" should be closed
And       I should see 1 blocks
And       I delete the block "Bar"
```

| Step | Regex |
| --- | --- |
| I open the container by text "Content" | `/^I open the container "([^"]*)"$/` |
| I add the block "Simple text" with the name "Foo" | `/^I add the block "([^"]*)" with the name "([^"]*)"$/` |
| I go to the tab "English" of the block "Foo" | `/^I go to the tab "([^"]*)" of the block "([^"]*)"$/` |
| I should see 6 blocks | `/^I should see (\d+) blocks$/` |
| I open the block "Foo" | `/^I open the block "([^"]*)"$/` |
| I submit the block "Foo" | `/^I submit the block "([^"]*)"$/` |
| I delete the block "Foo" | `/^I delete the block "([^"]*)"$/` |
| I rename the block "Foo" with "Bar" | `/^I rename the block "([^"]*)" with "([^"]*)"$/` |
| The block "Foo" should be opened | `/^The block "([^"]*)" should be opened$/` |
| The block "Foo" should be closed | `/^The block "([^"]*)" should be closed$/` |


[1]: https://github.com/Behat/Symfony2Extension
[2]: https://github.com/doctrine/DoctrineBundle
[3]: https://github.com/sonata-project/SonataAdminBundle
[4]: https://github.com/sonata-project/SonataPageBundle
