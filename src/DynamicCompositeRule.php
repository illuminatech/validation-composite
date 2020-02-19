<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\Validation\Composite;

use Illuminate\Contracts\Validation\Factory;

/**
 * DynamicCompositeRule allows definition of the composite rule on the fly.
 *
 * For example:
 *
 * ```php
 * $rule = new DynamicCompositeRule(['string', 'min:8', 'max:200']);
 * ```
 *
 * This rule make sense you wish to extend list of string rules identifiers at service provider level.
 * For example:
 *
 * ```php
 *
 * namespace App\Providers;
 *
 * use Illuminate\Support\ServiceProvider;
 * use Illuminate\Contracts\Validation\Factory;
 * use Illuminatech\Validation\Composite\DynamicCompositeRule;
 *
 * class AppServiceProvider extends ServiceProvider
 * {
 *     public function boot()
 *     {
 *         $this->app->extend('validator', function (Factory $validatorFactory) {
 *             $validatorFactory->extend('password', function ($attribute, $value) {
 *                 return (new DynamicCompositeRule(['string', 'min:8', 'max:200']))->passes($attribute, $value);
 *             });
 *
 *             return $validatorFactory;
 *         });
 *
 *         // ...
 *     }
 * }
 * ```
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class DynamicCompositeRule extends CompositeRule
{
    /**
     * @var array list of the validation rules, which should be combined into this one.
     */
    private $rules;

    /**
     * @var array custom error messages for the validation rules.
     */
    private $messages;

    /**
     * Constructor.
     *
     * @param  array  $rules list of the validation rules, which should be combined into this one.
     * @param  array  $messages custom error messages for the validation rules.
     * @param \Illuminate\Contracts\Validation\Factory|null $validatorFactory validator factory used for slave validator creation.
     */
    public function __construct(array $rules, array $messages = [], ?Factory $validatorFactory = null)
    {
        $this->rules = $rules;
        $this->messages = $messages;

        parent::__construct($validatorFactory);
    }

    /**
     * {@inheritdoc}
     */
    protected function rules(): array
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        return $this->messages;
    }
}
