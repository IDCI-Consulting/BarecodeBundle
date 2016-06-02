<?php

namespace IDCI\Bundle\BarcodeBundle\Tests\Type;

use PHPUnit_Framework_TestCase;
use IDCI\Bundle\BarcodeBundle\Type\Type;

/**
 * Class TypeTest
 */
class TypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * testConfigureOptions
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgumentException()
    {
        $type = new Type();
        $type->getDimension('Unknown Type');
    }
}
