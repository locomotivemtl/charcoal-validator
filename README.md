Charcoal Validator
==================

# Example usage

```php

$validatorRunner = new ValidatorRunner([
	'errors' => [
		new NullValidator(),
		new LengthValidator([
			'min' => 3,
			'max' => 64
		])
	]
]);

$stringToValidate = 'foobar'
$result = $validatorRunner->validate($stringToValidate);
echo $validatorRunner->isValid();

```

# The Runner

The runner is the main interface to all validations. It has the following interface:

- `validate($val)`
- `results()`
- `errors()`
- `warnings()`
- `infos()`
- `isValid()`

By default, `results()`, `errors()`, `warnings()` and `infos()` return only invalid results (failed validations). It is possible to also retrieve skipped results and valid results with:

```php
$ignoreSkipped = false;
$ignoreValid = false;
$results = $runner->results($ignoreSkipped, $ignoreValid);
```

# Validators

Every validator is stateless, all options must be passed directly to the constructor.

Every validator have only one method: `validate($val)`. It always returns a _response object_

# Response

# Available validators

- ~~[Choice](#choice-validator)~~
- ~~[Color](#color-validator)~~
- ~~[Email](#email-validator)~~
- [Empty](#empty-validator)
- ~~[Ip](#ip-validator)~~
- [Length](#length-validator)
- [Null](#null-validator)
- ~~[Password](#password-validator)~~
- [Phone](#phone-validator)
- [Regexp](#regexp-validator)
- ~~[Url](#url-validator)~~

## Empty Validator

The empty validator ensures a value is **not** "empty". In PHP, empty means `''`, `[]` (an empty array), `0` or `false` or `null`.

Options:

- `require_empty` defaults to `false`. Set to `true` to ensure value **is** empty.

## Length Validator

The length validator ensures a string (or a _string-object_) is of a certain length.

This validator skips null or empty strings. Use the [Null Validator](#null-validator) or the [Empty Validator](#empty-validator) to check for those cases, if need.

This validator works with unicode by default (using `mb_strlen`). It can be disabled by setting the `unicode` option to false.

Options:

- `min`
- `max`
- `unicode` defaults to `true`.


## Null Validator

The empty validator ensures a value is **not** "null".

Options:

- `require_null` defaults to `false`. Set to `true` to ensure value **is** null.

## Regexp Validator

The regexp validator ensures a string (or a _string-object_) is of a certain length.

Options:

- `pattern`
