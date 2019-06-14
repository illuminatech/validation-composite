<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2015 Illuminatech
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
     * Constructor.
     *
     * @param  array  $rules list of the validation rules, which should be combined into this one.
     * @param \Illuminate\Contracts\Validation\Factory|null $validatorFactory validator factory used for slave validator creation.
     */
    public function __construct(array $rules, ?Factory $validatorFactory = null)
    {
        $this->rules = $rules;

        parent::__construct($validatorFactory);
    }

    /**
     * {@inheritdoc}
     */
    protected function rules(): array
    {
        return $this->rules;
    }
}
