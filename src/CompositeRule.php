<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\Validation\Composite;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Factory;

/**
 * CompositeRule allows uniting several validation rules into a single one for easy re-usage.
 *
 * For example:
 *
 * ```php
 * use Illuminatech\Validation\Composite\CompositeRule;
 *
 * class PasswordRule extends CompositeRule
 * {
 *     protected function rules(): array
 *     {
 *         return ['string', 'min:8', 'max:200'];
 *     }
 * }
 *
 * class AvatarRule extends CompositeRule
 * {
 *     protected function rules(): array
 *     {
 *         return ['file', 'mimes:png,jpg,jpeg', 'max:1024'];
 *     }
 * }
 *
 * // ...
 *
 * $request->validate([
 *     'password' => ['required', new PasswordRule],
 *     'avatar' => ['required', new AvatarRule],
 * ]);
 * ```
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
abstract class CompositeRule implements Rule
{
    /**
     * @var \Illuminate\Contracts\Validation\Factory validator factory used for slave validator creation.
     */
    private $validatorFactory;

    /**
     * @var string validation error message from particular underlying validator.
     */
    private $message;

    /**
     * Constructor.
     *
     * @param \Illuminate\Contracts\Validation\Factory|null $validatorFactory validator factory used for slave validator creation.
     */
    public function __construct(?Factory $validatorFactory = null)
    {
        if ($validatorFactory !== null) {
            $this->setValidatorFactory($validatorFactory);
        }
    }

    /**
     * Defines list of the validation rules, which are combined into this one.
     *
     * @return array validation rules definition.
     */
    abstract protected function rules(): array;

    /**
     * Defines custom error messages for the validation rules defined at {@see rules()}.
     * @since 1.2.0
     *
     * @return array error messages.
     */
    protected function messages(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function passes($attribute, $value): bool
    {
        $data = [];

        Arr::set($data, $attribute, $value); // ensure correct validation for array attributes like 'item_ids.*' or 'items.*.id'

        $validator = $this->getValidatorFactory()->make(
            $data,
            [
                $attribute => $this->rules(),
            ],
            $this->messages()
        );

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

    /**
     * @return \Illuminate\Contracts\Validation\Factory validator factory used for slave validator creation.
     */
    public function getValidatorFactory(): Factory
    {
        if ($this->validatorFactory === null) {
            $this->validatorFactory = Validator::getFacadeRoot();
        }

        return $this->validatorFactory;
    }

    /**
     * @param \Illuminate\Contracts\Validation\Factory $validatorFactory validator factory used for slave validator creation.
     * @return static self reference.
     */
    public function setValidatorFactory(Factory $validatorFactory): self
    {
        $this->validatorFactory = $validatorFactory;

        return $this;
    }
}
