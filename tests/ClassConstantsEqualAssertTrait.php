<?php

namespace Tests;

use PHPUnit\Framework\AssertionFailedError;

/**
 * Trait ClassConstantsEqualAssertTrait
 *
 * @package Tests
 */
trait ClassConstantsEqualAssertTrait
{
    /**
     * Assert that constants of stub class equals to original class constants.
     *
     * @param string $originalClass
     * @param string $stubClass
     * @param string $errorMessage
     * @throws AssertionFailedError
     */
    public function assertClassConstantsEqual(string $originalClass, string $stubClass, string $errorMessage = '')
    {
        $originalConstants = (new \ReflectionClass($originalClass))->getConstants();
        $stubConstants = (new \ReflectionClass($stubClass))->getConstants();

        foreach ($originalConstants as $originalConstantName => $originalConstantValue) {
            if (isset($stubConstants[$originalConstantName])) {
                $this->assertEquals($stubConstants[$originalConstantName], $originalConstantValue, $errorMessage);
            } else {
                $this->fail(sprintf(
                    'Stub class [%s] does not include constant [%s] from original class [%s].',
                    $stubClass,
                    $originalConstantName,
                    $originalClass
                ));
            }
        }
    }
}
