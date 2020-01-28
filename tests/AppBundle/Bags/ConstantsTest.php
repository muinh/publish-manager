<?php

namespace Tests\App\Bags;

use App\SystemEvents;
use Tests\ClassConstantsEqualAssertTrait;
use App\Bags\{MessageBag, RequestFieldsBag};
use Tests\App\Stub\{
    MessageBag as TestMessageBag,
    RequestFieldsBag as TestRequestFieldsBag,
    SystemEvents as TestSystemEvents
};
use PHPUnit\Framework\{TestCase, AssertionFailedError};

/**
 * Class ConstantsTest
 *
 * @package Tests\App\Bags
 */
class ConstantsTest extends TestCase
{
    use ClassConstantsEqualAssertTrait;

    /**
     * Test that constants of original and stub classes are equal.
     *
     * @dataProvider classesToCheck
     * @param string $originalClass
     * @param string $stubClass
     * @throws AssertionFailedError
     */
    public function testClassConstantsEqual(string $originalClass, string $stubClass)
    {
        $this->assertClassConstantsEqual($originalClass, $stubClass);
    }

    /**
     * Classes where need to check that constants equal.
     *
     * @return array
     */
    public function classesToCheck() : array
    {
        return [
            [MessageBag::class, TestMessageBag::class],
            [RequestFieldsBag::class, TestRequestFieldsBag::class],
            [SystemEvents::class, TestSystemEvents::class],
        ];
    }
}
