
Charcoal Validator
==================

Powerful data validation.

# Table of content

- [How to install](#how-to-install)
    - [Dependencies](#dependencies)
- [Example usage](#example-usage)
- [The validator](#the-validator)
- [Rules](#rules)
    - ~~[Choice](#choice-rule)~~
    - ~~[Color](#color-rule)~~
    - [Date](#date-rule)
    - [Email](#email-rule)
    - [Empty](#empty-rule)
    - ~~[Ip](#ip-rule)~~
    - [Filesize](#filesize-rule)
    - [Filetype](#filetype-rule)
    - [Length](#length-rule)
    - [Null](#null-rule)
    - ~~[Password](#password-rule)~~
    - ~~[Phone](#phone-rule)~~
    - [Regexp](#regexp-rule)
    - [Url](#url-rule)
- [Validation Results](#validation-results)
- [Development](#development)
    - [Development dependencies](#development-dependencies)
    - [Coding Style](#coding-style)
    - [Authors](#authors)
    - [Changelog](#changelog)

# How to install

The preferred (and only supported) way of installing charcoal-rule is with **composer**:

```shell
composer require locomotivemtl/charcoal-rule
```

## Dependencies

- `PHP 7.2+`
- `ext-json`
 - For result serialization.
- `ext-mb`
  - For multibytes (unicode) string length validation.
- `ext-fileinfo`
  - For reading file's mimetype information.

# Example usage

```php

$validator = new Validator([
	new NullRule(),
	new LengthRule([ 'min' => 3, 'max' => 200 ])
],
[
	new LengthRule([ 'max' => 150 ])
]);

$stringToValidate = 'foobar';
$result = $validator($stringToValidate);
echo $result->isValid();
```

# The Validator

The validator is the main interface to all validations. Its goal is to link various validators to perform series of  validations in a unified way. It also allows to split the validations by level (_error_, _warning_ or _info_).

It has a single function, `validate()`, which is also invokable, and returns a `Validation` object.

  ```php
public function __construct(
	array $errorRules,
	array $warningRules,
	array $infoRules
);
public function __invoke($val): Validation;
```

## The Validation object

The `Validation` object is a collection of results (`\Charcoal\Validator\Result`) for each validation levels.  It is typically not instantiated directly, but returned from invoking a `Validator` instance.

  ```php
public function results(
	  ?string $level=null,
	  bool $returnFailures=true,
	  bool $returnSkipped=false,
	  bool $returnSuccess=false
) : array;
public function isValid(?string $level=null) : bool;
public function isSkipped(): bool;
```


## Validator options

Validation runners are stateless; all options must be passed directly to the constructor.

The only options available are the different types of rules to use. See the [example](#)

# Rules

Every rule is stateless, all _options_ must be passed directly to the constructor.

Every rule have only one public method: `process($val)`, which can also be invoked directly.
It always returns a `Result` object.

## Result

Result holds the result code after processing a rule on.a value. It is typically not instantiated directly, but returned from invoking a `Rule` instance.

```php
public function getType(): string;
public function getCode(): string;
public function getMessage(): string;
public function isValid() : bool;
public function isSkipped(): bool;
```


## Available rules

- ~~[Choice](#choice-rule)~~
- ~~[Color](#color-rule)~~
- [Date](#date-rule)
- [Email](#email-rule)
- [Empty](#empty-rule)
- [Filesize](#filesize-rule)
- [Filetype](#filetype-rule)
- ~~[Ip](#ip-rule)~~
- [Length](#length-rule)
- [Null](#null-rule)
- ~~[Password](#password-rule)~~
- ~~[Phone](#phone-rule)~~
- [Regexp](#regexp-rule)
- [Url](#url-rule)

## Date Validator

The date validator ensures a value is a date-compatible string or a DateTime object and, optionally, in a specific
range between `min` and `max`.

### Options

| Option         | Type      | Default     | Description |
| -------------- | --------- | ----------- | ----------- |
| **min** | `integer` | `0`         | The minimum date. If 0, empty or null, then there is no minimal constraint.
| **max** | `integer` | `0`         | The maximum date. If 0, empty or null, then there is no maximal constraint.
| **checkType** | `bolean`  | `true`      | Whether to fail on invalid date type (if true), or skip (if false).

### Result messages

| Code                            | Description |
| --------------------------------| ----------- |
| **date.failure.min** | Failure if the given date is before than than the minimal constraint.
| **date.failure.max** | Failure if the given date is after the maximal constraint.
| **date.failure.invalid-type** | Failure f the given value is not a valid date. (`check_type` is true).
| **date.skipped.invalid-type** | Skipped if the given value is not a valid date. (`check_type` is false).
| **date.skipped.empty-val** | Skipped if the given value is null or empty.
| **date.success** | Success if all validation (min, max, and date type) pass.

## Email Validator

The email validator ensures a value is a proper email address.

### Options

| Option            | Type      | Default       | Description |
| ----------------- | --------- | ------------- | ----------- |
| **mx** | `boolean` | `false`       | Additional MX record validation.

### Result messages

| Code                            | Description |
| --------------------------------| ----------- |
| **email.failure.invalid-type** | Failure if the given value is not a string or can't be cast to string.
| **email.failure.invalid-email** | Failure if the given string is not a valid email.
| **email.failure.invalid-mx** | Failure if the MX resolution fails.
| **email.skipped.empty-val** | Skipped if the given value is null or empty.
| **email.success** | Success if all validation (type, email and mx, if applicable) pass.

## Empty Validator

The empty validator ensures a value is **not** "empty".
In PHP, _empty_ means `''`, `[]` (an empty array), `0`, `'0'`, `false` or `null`.
Any object instance is not considered empty.

### Options

| Option            | Type      | Default       | Description |
| ----------------- | --------- | ------------- | ----------- |
| **require_empty** | `boolean` | `false`       | Set to `true` to ensure value **is** empty.

### Result Messages

| Code                            | Description |
| --------------------------------| ----------- |
| **empty.failure.is-empty** | Failure if the given value is empty. (`require_empty` is false, default).
| **empty.failure.is-not-empty** | Failure if the given value is not empty (`require_empty` is true).
| **empty.success.is-empty** | Success if the given value is empty. (`require_empty` is true).
| **empty.success.is-not-empty** | Success if the given value is not empty. (`require_empty` is false, default).

## Length Validator

The length validator ensures a string (or a _string-object_) is of a certain length.

This validator skips null or empty strings. Use the [Null Validator](#null-rule) or the [Empty Validator](#empty-rule) to check for those cases, if need.

This validator works with unicode by default (using `mb_strlen` for comparison). It can be disabled by setting the `unicode` option to false.

### Options

| Option            | Type      | Default       | Description |
| ----------------- | --------- | ------------- | ----------- |
| **min** | `integer` | `0`           | The minimum length. If 0, do not check.
| **max** | `integer` | `0`           | The maximum length. If 0, do not check.
| **unicode** | `boolean` | `true`           | Count unicode (multibytes) character as only 1 character.

> Using the `unicode` flag uses the `mb_strlen` to calculate strin length. Therefore, the `mb` PHP extension is required.

### Result messages

| Code                            | Description |
| --------------------------------| ----------- |
| **length.failure.min** | Failure if the given value is longer than minimal constraint.
| **length.failure.max** |
| **length.skipped.no-min-max** |
| **length.skipped.empty-val** |
| **length.skipped.invalid-type** |
| **length.success** |

## Null Validator

The empty validator ensures a value is **not** _null_.

It can also performs the opposite validation (ensuring a value **is** _null_) by setting the `require_null` option to `true`.

### Options

| Option            | Type      | Default       | Description |
| ----------------- | --------- | ------------- | ----------- |
| **require_null** | `boolean` | `false`       | Set to `true` to ensure value **is** null.

### Result messages

| Message                         | Description |
| --------------------------------| ----------- |
| **null.failure.is-null** |
| **null.failure.is-not-null** |
| **null.success.is-null** |
| **null.success.is-not-null** |

## Pattern Validator

The pattern validator ensures a string (or a _string-object_) is of a certain length.

### Options

| Option            | Type      | Default       | Description |
| ----------------- | --------- | ------------- | ----------- |
| **pattern** | `string`  | `''`          | The regexp.

### Messages

| Message                         | Description |
| --------------------------------| ----------- |
| **regexp.failure.no-match** |
| **regexp.skipped.no-pattern** |
| **regexp.skipped.empty-val** |
| **regexp.skipped.invalid-type** |
| **regexp.success** |

# Custom Rules

It's very simple to add a custom rule to a Validator constructor. The abstract `\Charcoal\Validator\Rule` can be  extended to create custom rule service. Alternatively callback rules are supported as long as they have the  following signature:

```php
use \Charcoal\Validator\Result;
function(mixed $value) : Result;
```

Here is an example of a callback custom rule:

```php
use \Charcoal\Validator\Validator;
use \Charcoal\Validator\NullRule;
use \Charcoal\Validator\Result;

$validator = new Validator([
	new NullRule(),
	function($value) : Result  {
		if (!is_string($value)) {
			 return new Result(
				 Result::TYPE_SKIPPED,
				 'fullstop.skipped.invalid-type',
				 'Value is not a string.'
				);
		}
		if (substr($val, -1) !== '.') {
			return new Result(
				Result::TYPE_FAILURE,
				'fullstop.error.no-fullstop',
				'The sentence does not end with a full stop.'
			);
		} else {
			return new Result(
				Result::TYPE_SUCCESS,
				'fullstop.success.fullstop',
				'The sentence ends with a full stop.'
			);
		}
	}
]);
$validationResult = $validator($val);
$success = $validationResult->isValid();
$results = $validationResult->results(Validator::LEVEL_ERROR);
```

It is also possible to use an instance of `Rule`, which provides some helper functions to  generate responses and messages:

```php
use \Charcoal\Validator\Validator;
use \Charcoal\Validator\NullRule;
use \Charcoal\Validator\Rule;
use \Charcoal\Validator\Result;

class FullStopRule extends Rule
{
	public function process($val): Result {
		if (!is_string($val)) {
			return $this->skip('fullstop.skipped.invalid-type');
		}
		if (substr($val, -1) !== '.') {
			return $this->failure('fullstop.error.no-fullstop');
		} else {
			return $this->success('fullstop.success.fullstop');
		}

	}

	public function messages(): array
	{
		return [
			'fullstop.skipped.invalid-type' => '',
			'fullstop.error.no-fullstop' => '',
			'fullstop.success.fullstop' => ''
		];
	}
};

$validator = new Validator([
	new NullRule(),
	new FullStopRule()
]);

$validationResult = $validator($val);
$success = $validationResult->isValid();
$results = $validationResult->results(Validator::LEVEL_ERROR);
```


# Development

To install the development environment:

```shell
$ composer install --prefer-source
```

Run tests with

```shell
$ composer test
```

## Development dependencies

- `phpunit/phpunit`
- `squizlabs/php_codesniffer`
- `satooshi/php-coveralls`

## Continuous Integration

| Service | Badge | Description |
| ------- | ----- | ----------- |
| [Travis](https://travis-ci.org/locomotivemtl/charcoal-rule) | [![Build Status](https://travis-ci.org/locomotivemtl/charcoal-rule.svg?branch=master)](https://travis-ci.org/locomotivemtl/charcoal-rule) | Runs code sniff check and unit tests. Auto-generates API documentation. |
| [Scrutinizer](https://scrutinizer-ci.com/g/locomotivemtl/charcoal-rule/) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/locomotivemtl/charcoal-rule/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/locomotivemtl/charcoal-rule/?branch=master) | Code quality checker. Also validates API documentation quality. |
| [Coveralls](https://coveralls.io/github/locomotivemtl/charcoal-rule) | [![Coverage Status](https://coveralls.io/repos/github/locomotivemtl/charcoal-rule/badge.svg?branch=master)](https://coveralls.io/github/locomotivemtl/charcoal-rule?branch=master) | Unit Tests code coverage. |
| [Sensiolabs](https://insight.sensiolabs.com/projects/5c21a1cf-9b21-41c8-82a8-90fbad808a20) | [![SensioLabsInsight](https://insight.sensiolabs.com/projects/ /mini.png)](https://insight.sensiolabs.com/projects/5c21a1cf-9b21-41c8-82a8-90fbad808a20) | Another code quality checker, focused on PHP. |

## Coding Style

The Charcoal-Validator module follows the Charcoal coding-style:

- [_PSR-4_](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md), autoloading is therefore provided by _Composer_.

- [_PSR-12_](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-12-extended-coding-style-guide.md)
- [_phpDocumentor_](http://phpdoc.org/) comments.
- Read the [phpcs.xml](phpcs.xml) file for all the details on code style.

> Coding style validation / enforcement can be performed with `composer phpcs`.
> An auto-fixer is also available with `composer phpcbf`.

> This module should also throw no error when running `phpstan analyse --level=max src/ tests/` 👍.

## Authors

- [Locomotive](https://locomotive.ca)

# License

**The MIT License (MIT)**

_Copyright © 2016 Locomotive inc._
> See [Authors](#authors).

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
