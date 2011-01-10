<?php

/**
 * Copyright (c) 2010 Gradwell dot com Ltd.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Gradwell dot com Ltd nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     Phin_Project
 * @subpackage  CommandLineLib
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.phin-tool.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phin_Project\CommandLineLib;

use Phin_Project\ValidationLib\MustBeString;
use Phin_Project\ValidationLib\MustBeInteger;

class ParsedSwitchTest extends \PHPUnit_Framework_TestCase
{
        public function testCanCreate()
        {
                // first, we need a definition
                $name = 'Fred';
                $desc = 'Fred is a jolly fellow';

                $def = new DefinedSwitch($name, $desc);
                $this->assertEquals($def->name, $name);
                $this->assertEquals($def->desc, $desc);

                // now, we use the definition to create
                // the ParsedSwitch
                $obj = new ParsedSwitch($def);

                // did it work?
                $this->assertEquals($name, $obj->name);
                $this->assertTrue(is_array($obj->values));
                $this->assertEquals(0, count($obj->values));
                $this->assertEquals(0, $obj->invokes);
                $this->assertEquals(false, $obj->isUsingDefaultValue);

                // did the parsed switch remember its defintion?
                $this->assertSame($def, $obj->definition);
        }

        public function testCanIncreaseInvokeCount()
        {
                // first, we need a definition
                $name = 'Fred';
                $desc = 'Fred is a jolly fellow';

                $def = new DefinedSwitch($name, $desc);
                $this->assertEquals($def->name, $name);
                $this->assertEquals($def->desc, $desc);

                // now, we use the definition to create
                // the ParsedSwitch
                $obj = new ParsedSwitch($def);

                $this->assertEquals(0, $obj->invokes);
                $obj->addToInvokeCount();
                $this->assertEquals(1, $obj->invokes);
        }

        public function testCanAddAValue()
        {
                // first, we need a definition
                $name = 'Fred';
                $desc = 'Fred is a jolly fellow';

                $def = new DefinedSwitch($name, $desc);
                $this->assertEquals($def->name, $name);
                $this->assertEquals($def->desc, $desc);

                // now, we use the definition to create
                // the ParsedSwitch
                $obj = new ParsedSwitch($def);

                $this->assertTrue(is_array($obj->values));
                $this->assertEquals(0, count($obj->values));

                $obj->addValue('alice');
                $this->assertEquals(1, count($obj->values));
                $this->assertEquals('alice', $obj->values[0]);

                $obj->addValue('joanne');
                $this->assertEquals(2, count($obj->values));
                $this->assertEquals('joanne', $obj->values[1]);
        }

        public function testCanRetrieveFirstValue()
        {
                // first, we need a definition
                $name = 'Fred';
                $desc = 'Fred is a jolly fellow';

                $def = new DefinedSwitch($name, $desc);
                $this->assertEquals($def->name, $name);
                $this->assertEquals($def->desc, $desc);

                // now, we use the definition to create
                // the ParsedSwitch
                $obj = new ParsedSwitch($def);

                $this->assertTrue(is_array($obj->values));
                $this->assertEquals(0, count($obj->values));

                $obj->addValue('alice');
                $this->assertEquals(1, count($obj->values));
                $this->assertEquals('alice', $obj->values[0]);

                $obj->addValue('joanne');
                $this->assertEquals(2, count($obj->values));
                $this->assertEquals('joanne', $obj->values[1]);

                // okay ... so can we retrieve the first value correctly?
                $this->assertEquals('alice', $obj->getFirstValue());
        }

        public function testGetFirstValueReturnsNullWhenNoValues()
        {
                // first, we need a definition
                $name = 'Fred';
                $desc = 'Fred is a jolly fellow';

                $def = new DefinedSwitch($name, $desc);
                $this->assertEquals($def->name, $name);
                $this->assertEquals($def->desc, $desc);

                // now, we use the definition to create
                // the ParsedSwitch
                $obj = new ParsedSwitch($def);

                $this->assertTrue(is_array($obj->values));
                $this->assertEquals(0, count($obj->values));

                // getFirstValue() should return null now
                $this->assertEquals(null, $obj->getFirstValue());
        }

        public function testCanSetIsUsingDefaultValue()
        {
                // first, we need a definition
                $name = 'Fred';
                $desc = 'Fred is a jolly fellow';

                $def = new DefinedSwitch($name, $desc);
                $this->assertEquals($def->name, $name);
                $this->assertEquals($def->desc, $desc);

                // now, we use the definition to create
                // the ParsedSwitch
                $obj = new ParsedSwitch($def);

                $this->assertFalse($obj->isUsingDefaultValue);
                $obj->setIsUsingDefaultValue();
                $this->assertTrue($obj->isUsingDefaultValue);
        }

        public function testValidationRetunsEmptyArrayIfNothingToValidate()
        {
                // first, we need a definition
                $name = 'Fred';
                $desc = 'Fred is a jolly fellow';

                $def = new DefinedSwitch($name, $desc);
                $this->assertEquals($def->name, $name);
                $this->assertEquals($def->desc, $desc);

                // now, we use the definition to create
                // the ParsedSwitch
                $obj = new ParsedSwitch($def);

                $result = $obj->validateValues();
                $this->assertTrue(is_array($result));
                $this->assertEquals(0, count($result));
        }

        public function testValidationReturnsEmptyArrayIfAllValidationsPass()
        {
                // first, we need a definition
                $name = 'Fred';
                $desc = 'Fred is a jolly fellow';

                $def = new DefinedSwitch($name, $desc);
                $this->assertEquals($def->name, $name);
                $this->assertEquals($def->desc, $desc);
                $def->setWithRequiredArg('harry', 'harry does love his food');
                $def->setArgValidator(new MustBeString());

                // now, we use the definition to create
                // the ParsedSwitch
                $obj = new ParsedSwitch($def);

                // add a value for the validator
                $obj->addValue('trout');

                // run the validation
                $result = $obj->validateValues();
                $this->assertTrue(is_array($result));
                $this->assertEquals(0, count($result));
        }

        public function testValidationReturnsArrayOfErrorsIfValidationsFail()
        {
                // first, we need a definition
                $name = 'Fred';
                $desc = 'Fred is a jolly fellow';

                $def = new DefinedSwitch($name, $desc);
                $this->assertEquals($def->name, $name);
                $this->assertEquals($def->desc, $desc);
                $def->setWithRequiredArg('harry', 'harry does love his food');
                $def->setArgValidator(new MustBeInteger());

                // now, we use the definition to create
                // the ParsedSwitch
                $obj = new ParsedSwitch($def);

                // add a value for the validator
                $obj->addValue('trout');

                // run the validation
                $result = $obj->validateValues();
                $this->assertTrue(is_array($result));
                $this->assertEquals(1, count($result));
        }
}