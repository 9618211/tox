<?php
/**
 * Defines the test case for Tox\Data\Kv\kv.
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
 * @copyright © 2012-2013 SZen.in
 * @license   GNU General Public License, version 3
 */

namespace ToxTest\Data\Kv;

use PHPUnit_Framework_TestCase;

require_once __DIR__ . '/../../../../../src/tox/core/assembly.php';
require_once __DIR__ . '/../../../../../src/tox/data/ikv.php';
require_once __DIR__ . '/../../../../../src/tox/data/kv/kv.php';

use Tox\Data\KV;
use Tox;

/**
 * Tests Tox\Data\KV.
 *
 * @internal
 *
 * @package tox.data.kv
 * @author  Qiang Fu <fuqiang007enter@gmail.com>
 */
class KvTest extends PHPUnit_Framework_TestCase
{
    public function testSetting()
    {
        $key = 'key';
        $value = 'sss';
        $expire = 500;
        $o_mockKv = $this->getMockBuilder('Tox\\Data\\Kv\\kv')
                //     ->disableOriginalConstructor()
                ->setMethods(array('setValue'))
                ->getMockForAbstractClass();

        //$o_mockKv = $this->getMockForAbstractClass('Tox\\Data\\Kv\\kv')
        $o_mockKv->expects($this->once())
                ->method('setValue')
                ->with($this->equalTo(md5('memcached' . $key)), $this->equalTo(serialize(array($value))), $this->equalTo($expire))
                ->will($this->returnValue(TRUE));

        $o_mockKv->set($key, $value, $expire);
    }

    public function testGetting()
    {
        $key = 'key';

        $o_mockKv = $this->getMockBuilder('Tox\\Data\\Kv\\kv')
                //     ->disableOriginalConstructor()
                ->setMethods(array('getValue'))
                ->getMockForAbstractClass();

        //$o_mockKv = $this->getMockForAbstractClass('Tox\\Data\\Kv\\kv')
        $o_mockKv->expects($this->once())
                ->method('getValue')
                ->with($this->equalTo(md5('memcached' . $key)))
                ->will($this->returnValue(serialize(array('555'))));

        $o_mockKv->get($key);
    }
    
    // todo
    
}

// vi:ft=php fenc=utf-8 ff=unix ts=4 sts=4 et sw=4 fen fdm=indent fdl=1 tw=120
