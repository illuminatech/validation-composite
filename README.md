<p align="center">
    <a href="https://github.com/illuminatech" target="_blank">
        <img src="https://avatars1.githubusercontent.com/u/47185924" height="100px">
    </a>
    <h1 align="center">Laravel Composite Validation</h1>
    <br>
</p>

This extension allows uniting several Laravel validation rules into a single one for easy re-usage.

For license information check the [LICENSE](LICENSE.md)-file.

[![Latest Stable Version](https://img.shields.io/packagist/v/illuminatech/validation-composite.svg)](https://packagist.org/packages/illuminatech/validation-composite)
[![Total Downloads](https://img.shields.io/packagist/dt/illuminatech/validation-composite.svg)](https://packagist.org/packages/illuminatech/validation-composite)
[![Build Status](https://github.com/illuminatech/validation-composite/workflows/build/badge.svg)](https://github.com/illuminatech/validation-composite/actions)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist illuminatech/validation-composite
```

or add

```json
"illuminatech/validation-composite": "*"
```

to the require section of your composer.json.


Usage
-----

The same sequence of the validation rules may repeat over the application many times. For example: you may have a set of
restrictions related to the user's password, like it should be at least 8 symbols long, but shorter then 200 to fit the
database field reserved for its storage. Your program may also allow user to upload an image to be his avatar, but in order
to make it safe, you should validate uploaded file mime type and size.
Thus validation for the user profile form may looks like following:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:200'],
            'avatar' => ['required', 'file', 'mimes:png,jpg,jpeg', 'max:1024'],
            // ...
        ]);
        
        // ...
    }
}
```

The problem is: validation for user's password or avatar may appear in several different places. For example: password
can be setup at sign-up process, during password reset and so on. You may also have a separated administration panel,
which allows system administrator to adjust existing user's record or create a new one. Thus you will have to duplicate
all these validation rules many times throughout your project source code. In case requirements change, for example:
we decide that password length should be at least 10 symbols instead of 8, or disallow '*.png' files from avatar - you'll
have to manually changes validation rules at all those places.

This extension allows uniting several validation rules into a single one for easy re-usage. For the example above you
should create 2 separated validation rule classes extending `Illuminatech\Validation\Composite\CompositeRule`:

```php
<?php

namespace App\Rules;

use Illuminatech\Validation\Composite\CompositeRule;

class PasswordRule extends CompositeRule
{
    protected function rules(): array
    {
        return ['string', 'min:8', 'max:200'];
    }
}

class AvatarRule extends CompositeRule
{
    protected function rules(): array
    {
        return ['file', 'mimes:png,jpg,jpeg', 'max:1024'];
    }
}
```

Here method `rules()` defines list of validation rules, which will be applied by defined rule internally.
Now we can rewrite the form validation in following way:

```php
<?php

namespace App\Http\Controllers;

use App\Rules\AvatarRule;
use App\Rules\PasswordRule;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'password' => ['required', new PasswordRule],
            'avatar' => ['required', new AvatarRule],
            // ...
        ]);
        
        // ...
    }
}
```

With such approach you can change validation for the 'password' and 'avatar' at the single place.

In case composite validation rule fails, validator instance will pick up an error message from the particular sub-rule.
For example:

```php
<?php

use App\Rules\PasswordRule;
use Illuminate\Support\Facades\Validator;

$validator = Validator::make(
    ['password' => 'short'],
    [
        'password' => ['required', new PasswordRule],
    ]
);

if ($validator->fails()) {
    echo $validator->errors()->first('password'); // outputs 'The password must be at least 8 characters.'
}
```

> Note: do not use rules like 'sometimes', 'required', 'required_with', 'required_without' and so on in the composite rule.
  These are processed at the different validation level and thus will have no effect or may behave unexpectedly. 

You may define composite validation rules using [validation factory extensions](https://laravel.com/docs/validation#using-extensions) feature.
For such case you may use `Illuminatech\Validation\Composite\DynamicCompositeRule`. For example:

```php
<?php

namespace App\Providers;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminatech\Validation\Composite\DynamicCompositeRule;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->extend('validator', function (Factory $validatorFactory) {
            $validatorFactory->extend('password', function ($attribute, $value) {
                return (new DynamicCompositeRule(['string', 'min:8', 'max:200']))->passes($attribute, $value);
            });
            
            $validatorFactory->extend('avatar', function ($attribute, $value) {
                return (new DynamicCompositeRule(['file', 'mimes:png,jpg,jpeg', 'max:1024']))->passes($attribute, $value);
            });

            return $validatorFactory;
        });
        
        // ...
    }
}
```

Note that with such approach automatic pick up of the validation error message becomes impossible, and you will have to setup
it explicitly in language files.

You may specify [custom error messages](https://laravel.com/docs/validation#custom-error-messages) per each validation rule used in the composite one,
overriding `messages()` method. For example:

```php
<?php

namespace App\Rules;

use Illuminatech\Validation\Composite\CompositeRule;

class PasswordRule extends CompositeRule
{
    protected function rules(): array
    {
        return ['string', 'min:8', 'max:200'];
    }

    protected function messages(): array
    {
        return [
            'string' => 'Only string is allowed.',
            'min' => ':attribute is too short.',
            'max' => ':attribute is too long.',
        ];
    }
}
```
