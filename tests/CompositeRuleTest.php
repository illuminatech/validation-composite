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
    public function dataProviderPasses(): array
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
    public function dataProviderPickupMessage(): array
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

    public function testSetupFactory()
    {
        $rule = new DynamicCompositeRule([]);

        $factory = new Factory($this->app->make('translator'), $this->app);

        $rule->setValidatorFactory($factory);
        $this->assertSame($factory, $rule->getValidatorFactory());
    }
}
