<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2015 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\Validation\Composite;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

/**
 * CompositeRule
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
abstract class CompositeRule implements Rule
{
    /**
     * @var string validation error message from particular underlying validator.
     */
    private $message;

    /**
     * Defines list of validation rules, which are combined into this one.
     *
     * @return array validation rules definition.
     */
    abstract protected function rules(): array;

    /**
     * {@inheritdoc}
     */
    public function passes($attribute, $value)
    {
        $validator = Validator::make([$attribute => $value], [
            $attribute => $this->rules(),
        ]);

        if ($validator->fails()) {
            $this->message = $validator->getMessageBag()->first();

            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function message()
    {
        return $this->message;
    }
}
