<?php

namespace Illuminatech\Validation\Composite\Test;

use Illuminate\Validation\Factory;
use Illuminatech\Validation\Composite\DynamicCompositeRule;

class CompositeRuleTest extends TestCase
{
    /**
     * Data provider for {@see testPasses()}
     *
     * @return array test data.
     */
    public static function dataProviderPasses(): array
    {
        return [
            [20, true],
            ['some', false],
            [-20, false],
            [200, false],
        ];
    }

    /**
     * @dataProvider dataProviderPasses
     *
     * @param  mixed  $value
     * @param  bool  $shouldPass
     */
    public function testPasses($value, bool $shouldPass)
    {
        $rule = new DynamicCompositeRule([
            'integer',
            'min:10',
            'max:100',
        ]);

        $this->assertSame($shouldPass, $rule->passes('foo', $value));
    }

    /**
     * Data provider for {@see testPickupMessage()}
     *
     * @return array test data.
     */
    public static function dataProviderPickupMessage(): array
    {
        return [
            ['some', 'validation.integer'],
            [-10, 'validation.min.numeric'],
            [200, 'validation.max.numeric'],
        ];
    }

    /**
     * @dataProvider dataProviderPickupMessage
     *
     * @param  mixed  $value
     * @param  string  $expectedMessage
     */
    public function testPickupMessage($value, string $expectedMessage)
    {
        $rule = new DynamicCompositeRule([
            'integer',
            'min:10',
            'max:100',
        ]);

        $rule->passes('foo', $value);

        $this->assertSame($expectedMessage, $rule->message());
    }

    /**
     * Data provider for {@see testPickupMessage()}
     *
     * @return array test data.
     */
    public static function dataProviderPickupCustomMessage(): array
    {
        return [
            ['some', 'custom.integer'],
            [-10, 'custom.min.numeric'],
            [200, 'custom.max.numeric'],
        ];
    }

    /**
     * @dataProvider dataProviderPickupCustomMessage
     *
     * @param  mixed  $value
     * @param  string  $expectedMessage
     */
    public function testPickupCustomMessage($value, string $expectedMessage)
    {
        $rule = new DynamicCompositeRule(
            [
                'integer',
                'min:10',
                'max:100',
            ],
            [
                'integer' => 'custom.integer',
                'min' => 'custom.min.numeric',
                'max' => 'custom.max.numeric',
            ]
        );

        $rule->passes('foo', $value);

        $this->assertSame($expectedMessage, $rule->message());
    }

    public function testSetupFactory()
    {
        $rule = new DynamicCompositeRule([]);

        $factory = new Factory($this->app->make('translator'), $this->app);

        $rule->setValidatorFactory($factory);
        $this->assertSame($factory, $rule->getValidatorFactory());
    }

    /**
     * Data provider for {@see testPassesArrayAttribute()}
     *
     * @return array test data.
     */
    public static function dataProviderPassesArrayAttribute(): array
    {
        return [
            // 'item_ids.*' => [new DynamicCompositeRule()],
            ['item_ids.0', 20, true],
            ['item_ids.0', 'some', false],
            ['item_ids.1', 20, true],
            ['item_ids.1', 'some', false],
            // 'items.*.id' => [new DynamicCompositeRule()],
            ['items.0.id', 20, true],
            ['items.0.id', 'some', false],
            ['items.1.id', 20, true],
            ['items.1.id', 'some', false],
        ];
    }

    /**
     * @see https://github.com/illuminatech/validation-composite/issues/3
     *
     * @depends testPasses
     *
     * @dataProvider dataProviderPassesArrayAttribute
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  bool  $shouldPass
     */
    public function testPassesArrayAttribute(string $attribute, $value, bool $shouldPass)
    {
        $rule = new DynamicCompositeRule([
            'integer',
            'min:10',
            'max:100',
        ]);

        $this->assertSame($shouldPass, $rule->passes($attribute, $value));
    }
}
