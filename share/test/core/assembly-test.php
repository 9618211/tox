<?php
/**
 * Defines the test case for Tox\Core\Assembly.
 *
 * This file is part of Tox.
 *
 * Tox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Tox is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tox.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright © 2012-2013 PHP-Tox.org
 * @license   GNU General Public License, version 3
 */

namespace Tox\Core;

use Exception as PHPException;

use PHPUnit_Framework_TestCase;

require_once __DIR__ . '/../../../src/core/assembly.php';

require_once __DIR__ . '/../../../src/core/exception.php';
require_once __DIR__ . '/../../../src/core/@exception/propertyreaddenied.php';
require_once __DIR__ . '/../../../src/core/@exception/propertywritedenied.php';

/**
 * Tests Tox\Core\Assembly.
 *
 * @internal
 *
 * @package tox.core
 * @author  Snakevil Zen <zsnakevil@gmail.com>
 */
class AssemblyTest extends PHPUnit_Framework_TestCase
{
    public function testGetterMechanismSupported()
    {
        try {
            $s_prop = md5(microtime());
            $o_mock = $this->getMock(
                'Tox\\Core\\AssemblyDummyA',
                array('toxIsMagicPropReadable', 'toxGet' . $s_prop)
            );
            $o_mock->expects($this->once())->method('toxIsMagicPropReadable')
                ->with($this->equalTo($s_prop))
                ->will($this->returnValue(true));
            $o_mock->expects($this->once())->method('toxGet' . $s_prop);
            $o_mock->$s_prop;
        } catch (Exception $ex) {
            $this->fail();
        }
    }

    /**
     * @depends testGetterMechanismSupported
     */
    public function testRegularPairOfPropAndGetterWouldWorkFine()
    {
        try {
            $o_obj = new AssemblyDummyA;
            $this->assertNull($o_obj->ok);
        } catch (Exception $ex) {
            $this->fail();
        }
    }

    /**
     * @depends testRegularPairOfPropAndGetterWouldWorkFine
     * @expectedException Tox\Core\PropertyReadDeniedException
     */
    public function testPropertyMustBeDeclaredForGetterMechanism()
    {
        $o_obj = new AssemblyDummyA;
        $o_obj->noProp;
    }

    /**
     * @depends testRegularPairOfPropAndGetterWouldWorkFine
     * @expectedException Tox\Core\PropertyReadDeniedException
     */
    public function testGetterMustBeDeclaredForGetterMechanism()
    {
        $o_obj = new AssemblyDummyA;
        $o_obj->noGetter;
    }

    /**
     * @depends testRegularPairOfPropAndGetterWouldWorkFine
     * @expectedException Tox\Core\PropertyReadDeniedException
     */
    public function testPrivatePropAgainstGetterMechanism()
    {
        $o_obj = new AssemblyDummyA;
        $o_obj->privProp;
    }

    /**
     * @depends testRegularPairOfPropAndGetterWouldWorkFine
     * @expectedException Tox\Core\PropertyReadDeniedException
     */
    public function testPublicGetterAgainstGetterMechanism()
    {
        $o_obj = new AssemblyDummyA;
        $o_obj->pubGetter;
    }

    public function testSetterMechanismSupported()
    {
        try {
            $s_prop = md5(microtime());
            $f_value = microtime(true);
            $o_mock = $this->getMock('Tox\\Core\\AssemblyDummyA', array('toxIsMagicPropWritable', 'toxSet' . $s_prop));
            $o_mock->expects($this->once())->method('toxIsMagicPropWritable')
                ->with($this->equalTo($s_prop))
                ->will($this->returnValue(true));
            $o_mock->expects($this->once())->method('toxSet'. $s_prop)
                ->with($this->equalTo($f_value));
            $o_mock->$s_prop = $f_value;
        } catch (Exception $ex) {
            $this->fail();
        }
    }

    /**
     * @depends testSetterMechanismSupported
     * @depends testRegularPairOfPropAndGetterWouldWorkFine
     */
    public function testRegularPairOfPropAndSetterWouldWorkFine()
    {
        try {
            $f_value = microtime(true);
            $o_obj = new AssemblyDummyA;
            $o_obj->ok = $f_value;
            $this->assertEquals($f_value, $o_obj->ok);
        } catch (Exception $ex) {
            $this->fail();
        }
    }

    /**
     * @depends testSetterMechanismSupported
     * @expectedException Tox\Core\PropertyWriteDeniedException
     */
    public function testPropertyMustBeDeclaredForSetterMechanism()
    {
        $o_obj = new AssemblyDummyA;
        $o_obj->noProp = 1;
    }

    /**
     * @depends testSetterMechanismSupported
     * @expectedException Tox\Core\PropertyWriteDeniedException
     */
    public function testSetterMustBeDeclaredForSetterMechanism()
    {
        $o_obj = new AssemblyDummyA;
        $o_obj->noSetter = 1;
    }

    /**
     * @depends testSetterMechanismSupported
     * @expectedException Tox\Core\PropertyWriteDeniedException
     */
    public function testPrivatePropAgainstSetterMechanism()
    {
        $o_obj = new AssemblyDummyA;
        $o_obj->privProp = 1;
    }

    /**
     * @depends testSetterMechanismSupported
     * @expectedException Tox\Core\PropertyWriteDeniedException
     */
    public function testPublicSetterAgainstSetterMechanism()
    {
        $o_obj = new AssemblyDummyA;
        $o_obj->pubSetter = 1;
    }

    /**
     * @depends testSetterMechanismSupported
     */
    public function testDifferentTypesKeepTheirOwnPropertiesInformations()
    {
        try {
            $f_value = microtime(true);
            $o_obj1 = new AssemblyDummyA;
            $o_obj2 = new AssemblyDummyB;
            $o_obj1->ok = $f_value;
            $o_obj2->foo = $f_value;
            $this->assertEquals($f_value, $o_obj1->ok);
            $this->assertNull($o_obj2->foo);
        } catch (PHPException $ex) {
            $this->fail();
        }
    }

    /**
     * @depends testGetterMechanismSupported
     */
    public function testPreGet()
    {
        $o_obj = $this->getMock('Tox\\Core\\AssemblyDummyA', array('toxPreGet', 'toxGetOk'));
        $o_obj->expects($this->at(0))->method('toxPreGet')
            ->with($this->equalTo('ok'));
        $o_obj->expects($this->at(1))->method('toxGetOk');
        $o_obj->ok;
    }

    /**
     * @depends testSetterMechanismSupported
     */
    public function testPostAndPostSet()
    {
        $s_val1 = microtime();
        $s_val2 = microtime();
        $o_obj = $this->getMock('Tox\\Core\\AssemblyDummyA', array('toxPreSet', 'toxSetOk', 'toxPostSet'));
        $o_obj->expects($this->at(0))->method('toxPreSet')
            ->with($this->equalTo('ok'), $this->equalTo($s_val1))
            ->will($this->returnValue($s_val2));
        $o_obj->expects($this->at(1))->method('toxSetOk')
            ->with($this->equalTo($s_val2));
        $o_obj->expects($this->at(2))->method('toxPostSet')
            ->with($this->equalTo('ok'));
        $o_obj->ok = $s_val1;
    }
}

/**
 * Represents as an assembly for mocking test.
 *
 * @internal
 *
 * @package tox.core
 * @author  Snakevil Zen <zsnakevil@gmail.com>
 *
 * @property mixed $ok
 */
class AssemblyDummyA extends Assembly
{
    /**
     * Retrieves and sets some value.
     *
     * @var mixed
     */
    protected $ok;

    /**
     * Handles to retrieve some value.
     *
     * @return mixed
     */
    protected function toxGetOk()
    {
        return $this->ok;
    }

    /**
     * Handles to set some value.
     *
     * @param  mixed $value
     * @return void
     */
    protected function toxSetOk($value)
    {
        $this->ok = $value;
    }

    /**
     * Acts as an illegal getter for no corresponding property.
     *
     * @return void
     */
    protected function toxGetNoProp()
    {
    }

    /**
     * Acts as an illegal setter for no corresponding property.
     *
     * @param  mixed $value
     * @return void
     */
    protected function toxSetNoProp($value)
    {
    }

    /**
     * Acts as an illegal property for no corresponding getter.
     *
     * @var NULL
     */
    protected $noGetter;

    /**
     * Acts as an illegal property for no corresponding setter.
     *
     * @var NULL
     */
    protected $noSetter;

    /**
     * Acts as an illegal property for private visibility.
     *
     * @var NULL
     */
    private $privProp;

    /**
     * Acts as an illegal getter for the corresponding property is private.
     *
     * @return void
     */
    protected function toxGetPrivProp()
    {
    }

    /**
     * Acts as an illegal setter for the corresponding property is private.
     *
     * @param  mixed $value
     * @return void
     */
    protected function toxSetPrivProp($value)
    {
    }

    /**
     * Acts as an illegal property for the corresponding getter is public.
     *
     * @var NULL
     */
    protected $pubGetter;

    /**
     * Acts as an illegal getter for the public visibility.
     *
     * @return void
     */
    public function toxGetPubGetter()
    {
    }

    /**
     * Acts as an illegal property for the corresponding setter is public.
     *
     * @var NULL
     */
    protected $pubSetter;

    /**
     * Acts as an illegal setter for the public visibility.
     *
     * @param  mixed $value
     * @return void
     */
    public function toxSetPubSetter($value)
    {
    }
}

/**
 * Represents as another assembly for mocking test.
 *
 * @internal
 *
 * @package tox.core
 * @author  Snakevil Zen <zsnakevil@gmail.com>
 *
 * @property NULL $foo
 */
class AssemblyDummyB extends Assembly
{
    /**
     * Retrieves and sets some value.
     *
     * @var mixed
     */
    protected $foo;

    /**
     * Handles to retrieve some value.
     *
     * @return mixed
     */
    protected function toxGetFoo()
    {
    }

    /**
     * Handles to set some value.
     *
     * @param  mixed $value
     * @return void
     */
    protected function toxSetFoo($value)
    {
    }
}

// vi:ft=php fenc=utf-8 ff=unix ts=4 sts=4 et sw=4 fen fdm=indent fdl=1 tw=120
