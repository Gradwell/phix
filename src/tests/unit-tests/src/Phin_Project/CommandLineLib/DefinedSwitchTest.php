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

class DefinedSwitchTest extends \PHPUnit_Framework_TestCase
{
        public function testCanCreateDefinedSwitch()
        {
                $name = 'Fred';
                $desc = 'Fred is a jolly fellow';

                $obj = new DefinedSwitch($name, $desc);
                $this->assertEquals($obj->name, $name);
                $this->assertEquals($obj->desc, $desc);
        }

        public function testCanCreateWithShortSwitch()
        {
                $name = 'help';
                $desc = 'display this help message';
                $shortSwitch = 'h';

                $obj = new DefinedSwitch($name, $desc);
                $obj->setWithShortSwitch($shortSwitch);

                // has it worked?
                $this->assertEquals($obj->name, $name);
                $this->assertEquals($obj->desc, $desc);
                $this->assertTrue($obj->testHasShortSwitch($shortSwitch));
        }

        public function testCanCreateWithMultipleShortSwitches()
        {
                $name = 'help';
                $desc = 'display this help message';
                $shortSwitches = array('h', '?');

                $obj = new DefinedSwitch($name, $desc);
                foreach ($shortSwitches as $shortSwitch)
                {
                        $obj->setWithShortSwitch($shortSwitch);
                }

                // has it worked?
                $this->assertEquals($obj->name, $name);
                $this->assertEquals($obj->desc, $desc);
                foreach ($shortSwitches as $shortSwitch)
                {
                        $this->assertTrue($obj->testHasShortSwitch($shortSwitch));
                }
        }

        public function testCannotCreateShortSwitchThatStartsWithHyphen()
        {
                $name = 'help';
                $desc = 'display this help message';
                $shortSwitches = array('-h', '--?');

                $obj = new DefinedSwitch($name, $desc);
                foreach ($shortSwitches as $shortSwitch)
                {
                        $hasAsserted = false;
                        try
                        {
                                $obj->setWithShortSwitch($shortSwitch);
                        }
                        catch (\Exception $e)
                        {
                                $hasAsserted = true;
                        }

                        $this->assertTrue($hasAsserted);
                }
        }

        public function testCanCreateWithLongSwitch()
        {
                $name = 'help';
                $desc = 'display this help message';
                $longSwitch = 'help';

                $obj = new DefinedSwitch($name, $desc);
                $obj->setWithLongSwitch($longSwitch);

                // has it worked?
                $this->assertEquals($obj->name, $name);
                $this->assertEquals($obj->desc, $desc);
                $this->assertTrue($obj->testHasLongSwitch($longSwitch));

        }

        public function testCanCreateWithMultipleLongSwitches()
        {
                $name = 'help';
                $desc = 'display this help message';
                $longSwitches = array('help', '?');

                $obj = new DefinedSwitch($name, $desc);
                foreach ($longSwitches as $longSwitch)
                {
                        $obj->setWithlongSwitch($longSwitch);
                }

                // has it worked?
                $this->assertEquals($obj->name, $name);
                $this->assertEquals($obj->desc, $desc);
                foreach ($longSwitches as $longSwitch)
                {
                        $this->assertTrue($obj->testHasLongSwitch($longSwitch));
                }
        }

        public function testCannotCreateLongSwitchesThatStartWithHyphen()
        {
                $name = 'help';
                $desc = 'display this help message';
                $longSwitches = array('--help', '-?');

                $obj = new DefinedSwitch($name, $desc);
                foreach ($longSwitches as $longSwitch)
                {
                        $hasAsserted = false;
                        try
                        {
                                $obj->setWithlongSwitch($longSwitch);
                        }
                        catch (\Exception $e)
                        {
                                $hasAsserted = true;
                        }

                        $this->assertTrue($hasAsserted);
                }
        }

        public function testCanCreateWithMultipleMixedSwitches()
        {
                $name = 'help';
                $desc = 'display this help message';
                $longSwitches  = array('help', '?');
                $shortSwitches = array('h', '?');

                $obj = new DefinedSwitch($name, $desc);
                foreach ($shortSwitches as $shortSwitch)
                {
                        $obj->setWithShortSwitch($shortSwitch);
                }
                foreach ($longSwitches as $longSwitch)
                {
                        $obj->setWithlongSwitch($longSwitch);
                }

                // has it worked?
                $this->assertEquals($obj->name, $name);
                $this->assertEquals($obj->desc, $desc);
                foreach ($shortSwitches as $shortSwitch)
                {
                        $this->assertTrue($obj->testHasShortSwitch($shortSwitch));
                }
                foreach ($longSwitches as $longSwitch)
                {
                        $this->assertTrue($obj->testHasLongSwitch($longSwitch));
                }
        }

        public function testCanCreateARepeatableSwitch()
        {
                $name = 'help';
                $desc = 'display this help message';
                $shortSwitch = 'h';

                $obj = new DefinedSwitch($name, $desc);
                $obj->setWithShortSwitch($shortSwitch)
                    ->setSwitchIsRepeatable();

                // has it worked?
                $this->assertEquals($obj->name, $name);
                $this->assertEquals($obj->desc, $desc);
                $this->assertTrue($obj->testHasShortSwitch($shortSwitch));
                $this->assertTrue($obj->testIsRepeatable());
        }

        public function testCanCreateWithOptionalArgument()
        {
                $name = 'help';
                $desc = 'display this help message';
                $shortSwitch = 'h';

                $argName = '<command>';
                $argDesc = 'The <command> you want help with';

                $obj = new DefinedSwitch($name, $desc);
                $obj->setWithShortSwitch($shortSwitch)
                    ->setWithOptionalArg($argName, $argDesc);

                // has it worked?
                $this->assertEquals($obj->name, $name);
                $this->assertEquals($obj->desc, $desc);
                $this->assertTrue($obj->testHasShortSwitch($shortSwitch));
                $this->assertTrue($obj->testHasOptionalArgument());
                $this->assertFalse($obj->testHasRequiredArgument());
        }

        public function testCanCreateWithRequiredArgument()
        {
                $name = 'help';
                $desc = 'display this help message';
                $shortSwitch = 'h';

                $argName = '<command>';
                $argDesc = 'The <command> you want help with';

                $obj = new DefinedSwitch($name, $desc);
                $obj->setWithShortSwitch($shortSwitch)
                    ->setWithRequiredArg($argName, $argDesc);

                // has it worked?
                $this->assertEquals($obj->name, $name);
                $this->assertEquals($obj->desc, $desc);
                $this->assertTrue($obj->testHasShortSwitch($shortSwitch));
                $this->assertTrue($obj->testHasRequiredArgument());
                $this->assertFalse($obj->testHasOptionalArgument());
        }

        public function testCanCreateArgWithDefaultValue()
        {
                $name = 'help';
                $desc = 'display this help message';
                $shortSwitch = 'h';

                $argName = '<command>';
                $argDesc = 'The <command> you want help with';

                $obj = new DefinedSwitch($name, $desc);
                $obj->setWithShortSwitch($shortSwitch)
                    ->setWithRequiredArg($argName, $argDesc)
                    ->setArgHasDefaultValueOf('trout');

                // has it worked?
                $this->assertEquals($obj->name, $name);
                $this->assertEquals($obj->desc, $desc);
                $this->assertEquals($obj->arg->defaultValue, 'trout');
                $this->assertTrue($obj->testHasShortSwitch($shortSwitch));
                $this->assertTrue($obj->testHasRequiredArgument());
                $this->assertFalse($obj->testHasOptionalArgument());
        }
}