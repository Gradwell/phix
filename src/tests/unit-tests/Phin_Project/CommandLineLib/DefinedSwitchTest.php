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

use Phin_Project\ValidationLib\MustBeWriteable;
use Phin_Project\ValidationLib\MustBeValidPath;

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

        public function testCanTestForShortSwitch()
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

        public function testTestReturnsFalseIfShortSwitchNotDefined()
        {
                $name = 'help';
                $desc = 'display this help message';
                $shortSwitch = 'h';

                $obj = new DefinedSwitch($name, $desc);
                $obj->setWithShortSwitch($shortSwitch);

                // has it worked?
                $this->assertEquals($obj->name, $name);
                $this->assertEquals($obj->desc, $desc);
                $this->assertFalse($obj->testHasShortSwitch('v'));
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

        public function testCanTestForLongSwitch()
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

        public function testReturnsFalseIfLongSwitchNotDefined()
        {
                $name = 'help';
                $desc = 'display this help message';
                $longSwitch = 'help';

                $obj = new DefinedSwitch($name, $desc);
                $obj->setWithLongSwitch($longSwitch);

                // has it worked?
                $this->assertEquals($obj->name, $name);
                $this->assertEquals($obj->desc, $desc);
                $this->assertFalse($obj->testHasLongSwitch('version'));
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
                $obj->setWithShortSwitch($shortSwitch);

                // has it worked?
                $this->assertEquals($obj->name, $name);
                $this->assertEquals($obj->desc, $desc);
                $this->assertTrue($obj->testHasShortSwitch($shortSwitch));
                $this->assertFalse($obj->testIsRepeatable());

                $obj->setSwitchIsRepeatable();
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

        public function testCanSetArgValidator()
        {
                $name = 'help';
                $desc = 'display this help message';
                $shortSwitch = 'h';

                $argName = '<command>';
                $argDesc = 'The <command> you want help with';

                $obj = new DefinedSwitch($name, $desc);
                $obj->setWithShortSwitch($shortSwitch)
                    ->setWithRequiredArg($argName, $argDesc)
                    ->setArgHasDefaultValueOf('trout')
                    ->setArgValidator(new MustBeWriteable());

                // did it work?
                $this->assertTrue($obj->arg->testMustValidateWith('Phin_Project\ValidationLib\MustBeWriteable'));
        }

        public function testCanTestForOptionalArguments()
        {
                $name = 'help';
                $desc = 'display this help message';
                $shortSwitch = 'h';

                $obj = new DefinedSwitch($name, $desc);
                $obj->setWithShortSwitch($shortSwitch);

                $this->assertEquals($obj->name, $name);
                $this->assertEquals($obj->desc, $desc);
                $this->assertTrue($obj->testHasShortSwitch($shortSwitch));

                // if we try and test for an arg when none is defined,
                // there should be no error
                $this->assertFalse($obj->testHasArgument());
                $this->assertFalse($obj->testHasOptionalArgument());
                $this->assertFalse($obj->testHasRequiredArgument());
        }

        public function testCannotSetOptionsOnArgUntilItIsDefined()
        {
                $name = 'help';
                $desc = 'display this help message';
                $shortSwitch = 'h';

                $obj = new DefinedSwitch($name, $desc);
                $obj->setWithShortSwitch($shortSwitch);

                $caughtException = false;
                try
                {
                        $obj->setArgHasDefaultValueOf(0);
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);

                $caughtException = false;
                try
                {
                        $obj->setArgValidator(new MustBeValidPath());
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);
        }

        public function testCanAutoGenerateAHumanReadableDesc()
        {
                $obj = new DefinedSwitch('include', 'add a folder to load commands from');
                $obj->setLongDesc("phin finds all of its commands by searching PHP's include_path for files with "
                                  . "the file extension '.phin.php'. If you want to phin to look in other folders "
                                  . "without having to add them to PHP's include_path, use --include to tell phin "
                                  . "to look in these folders."
                                  . \PHP_EOL . \PHP_EOL
                                  . "phin expects '<path>' to point to a folder that conforms to the PSR0 standard "
                                  . "for autoloaders."
                                  . \PHP_EOL . \PHP_EOL
                                  . "For example, if your command is the class '\Me\Tools\ScheduledTask', phin would "
                                  . "expect to autoload this class from the 'Me/Tools/ScheduledTask.phin.php' file."
                                  . \PHP_EOL . \PHP_EOL
                                  . "If your class lives in the './myApp/lib/Me/Tools' folder, you would call phin "
                                  . "with 'phin --include=./myApp/lib'")
                    ->setWithShortSwitch('I')
                    ->setWithLongSwitch('include')
                    ->setWithRequiredArg('<path>', 'The path to the folder to include')
                    ->setArgValidator(new MustBeValidPath())
                    ->setSwitchIsRepeatable();

                $desc = $obj->getHumanReadableSwitchList();

                $expectedArray = array
                (
                        "-I" => "-I",
                        "--include" => "--include"
                );

                $this->assertEquals($expectedArray, $desc);
        }
}