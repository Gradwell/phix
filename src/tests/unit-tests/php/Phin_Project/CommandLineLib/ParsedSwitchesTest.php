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

class ParsedSwitchesTest extends \PHPUnit_Framework_TestCase
{
        public function testCanAddSwitch()
        {
                // define the options we are expecting
                $expectedOptions = new DefinedSwitches();

                // create the switch to add
                $switchName = 'fred';
                $switchDesc = 'trout';
                $expectedOptions->addSwitch($switchName, $switchDesc);

                // create the ParsedSwitches object
                $ParsedSwitches = new ParsedSwitches();
                $ParsedSwitches->addSwitch($expectedOptions, $switchName);

                // did it work?
                $this->assertTrue($ParsedSwitches->testHasSwitch($switchName));

                // and what happens if we try to add a switch
                // that has not been defined?
                $caughtException = false;
                try
                {
                        $ParsedSwitches->addSwitch($expectedOptions, 'harry');
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);
        }

        public function testCanCheckForSwitch()
        {
                // define the options we are expecting
                $expectedOptions = new DefinedSwitches();

                // create the switch to add
                $switchName = 'fred';
                $switchDesc = 'trout';
                $expectedOptions->addSwitch($switchName, $switchDesc);

                // create the ParsedSwitches object
                $ParsedSwitches = new ParsedSwitches($expectedOptions);
                $ParsedSwitches->addSwitch($expectedOptions, $switchName);

                // did it work?
                $this->assertTrue($ParsedSwitches->testHasSwitch($switchName));

                // if the switch is not there?
                $this->assertFalse($ParsedSwitches->testHasSwitch('harry'));
        }

        public function testCanGetSwitchByName()
        {
                // define the options we are expecting
                $expectedOptions = new DefinedSwitches();

                // create the switches to add
                $switchName1 = 'fred';
                $switchDesc1 = 'trout';
                $expectedOptions->addSwitch($switchName1, $switchDesc1);
                $switch1 = $expectedOptions->getSwitchByName($switchName1);

                $switchName2 = 'harry';
                $switchDesc2 = 'salmon';
                $expectedOptions->addSwitch($switchName2, $switchDesc2);
                $switch2 = $expectedOptions->getSwitchByName($switchName2);

                // create the ParsedSwitches object
                $ParsedSwitches = new ParsedSwitches($expectedOptions);
                $ParsedSwitches->addSwitch($expectedOptions, $switchName1);
                $ParsedSwitches->addSwitch($expectedOptions, $switchName2);

                // did it work?
                $retrievedSwitch1 = $ParsedSwitches->getSwitch($switchName1);
                $retrievedSwitch2 = $ParsedSwitches->getSwitch($switchName2);

                $this->assertTrue($retrievedSwitch1 instanceof ParsedSwitch);
                $this->assertEquals($switch1->name, $retrievedSwitch1->name);
                $this->assertSame($switch1, $retrievedSwitch1->definition);

                $this->assertTrue($retrievedSwitch2 instanceof ParsedSwitch);
                $this->assertEquals($switch2->name, $retrievedSwitch2->name);
                $this->assertSame($switch2, $retrievedSwitch2->definition);

                // and if the switch is not there?
                $caughtException = false;
                try
                {
                        $ParsedSwitches->getSwitch('notdefined');
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);
        }

        public function testCanGetSwitchArgValues()
        {
                // define the options we are expecting
                $expectedOptions = new DefinedSwitches();

                $expectedOptions->addSwitch('fred', 'trout')
                                ->setWithOptionalArg('<fish>', 'which kind of fish you like');

                $ParsedSwitches = new ParsedSwitches();
                $ParsedSwitches->addSwitch($expectedOptions, 'fred', 'salmon');

                // can we do this?
                $retrievedArgs = $ParsedSwitches->getArgsForSwitch('fred');

                // did it work?
                $this->assertTrue(is_array($retrievedArgs));
                $this->assertEquals(1, count($retrievedArgs));
                $this->assertEquals('salmon', $retrievedArgs[0]);
        }

        public function testCanGetSwitchArgFirstValue()
        {
                // define the options we are expecting
                $expectedOptions = new DefinedSwitches();

                $expectedOptions->addSwitch('fred', 'trout')
                                ->setWithOptionalArg('<fish>', 'which kind of fish you like');

                $ParsedSwitches = new ParsedSwitches();
                $ParsedSwitches->addSwitch($expectedOptions, 'fred', 'salmon');

                // can we do this?
                $retrievedArg = $ParsedSwitches->getFirstArgForSwitch('fred');

                // did it work?
                $this->assertEquals('salmon', $retrievedArg);
        }

        public function testReturnsTrueIfSwitchArgFirstValueMissing()
        {
                // define the options we are expecting
                $expectedOptions = new DefinedSwitches();

                $expectedOptions->addSwitch('fred', 'trout')
                                ->setWithOptionalArg('<fish>', 'which kind of fish you like');

                $ParsedSwitches = new ParsedSwitches();
                $ParsedSwitches->addSwitch($expectedOptions, 'fred');

                // can we do this?
                $retrievedArg = $ParsedSwitches->getFirstArgForSwitch('fred');

                // did it work?
                $this->assertTrue($retrievedArg);
        }

        public function testCanGetAllSwitches()
        {
                // define the options we are expecting
                $expectedOptions = new DefinedSwitches();

                // create the switches to add
                $switchName1 = 'fred';
                $switchDesc1 = 'trout';
                $expectedOptions->addSwitch($switchName1, $switchDesc1);
                $switch1 = $expectedOptions->getSwitchByName($switchName1);

                $switchName2 = 'harry';
                $switchDesc2 = 'salmon';
                $expectedOptions->addSwitch($switchName2, $switchDesc2);
                $switch2 = $expectedOptions->getSwitchByName($switchName2);

                // create the ParsedSwitches object
                $ParsedSwitches = new ParsedSwitches($expectedOptions);
                $ParsedSwitches->addSwitch($expectedOptions, $switchName1);
                $ParsedSwitches->addSwitch($expectedOptions, $switchName2);

                // did it work?
                $switches = $ParsedSwitches->getSwitches();
                $this->assertEquals(2, count($switches));
                $this->assertSame($switch1, $switches[$switchName1]->definition);
                $this->assertSame($switch2, $switches[$switchName2]->definition);
        }

        public function testCanAddRepeatedSwitches()
        {
                // define the options we are expecting
                $expectedOptions = new DefinedSwitches();

                // create the switch to add
                $switchName = 'fred';
                $switchDesc = 'trout';
                $expectedOptions->addSwitch($switchName, $switchDesc);

                // create the ParsedSwitches object
                $ParsedSwitches = new ParsedSwitches($expectedOptions);

                // repeat the switch
                $ParsedSwitches->addSwitch($expectedOptions, $switchName);
                $ParsedSwitches->addSwitch($expectedOptions, $switchName);

                // did it work?
                $switches = $ParsedSwitches->getSwitches();
                $retrievedArgs = $ParsedSwitches->getArgsForSwitch($switchName);
                $this->assertEquals(1, count($switches));
                $this->assertEquals(2, count($retrievedArgs));
                $this->assertEquals(2, $ParsedSwitches->getInvokeCountForSwitch($switchName));
        }

        public function testCanAddRepeatedSwitchesWithArguments()
        {
                // define the options we are expecting
                $expectedOptions = new DefinedSwitches();

                // create the switch to add
                $switchName = 'fred';
                $switchDesc = 'trout';
                $expectedOptions->addSwitch($switchName, $switchDesc);

                $args = array
                (
                        'hickory',
                        'dickory',
                        'dock',
                        'the',
                        'mouse',
                        'ran',
                        'up',
                        'the',
                        'clock'
                );

                // add the switch
                $ParsedSwitches = new ParsedSwitches();
                foreach ($args as $arg)
                {
                        $ParsedSwitches->addSwitch($expectedOptions, $switchName, $arg);
                }

                // did it work?
                $switches = $ParsedSwitches->getSwitches();
                $this->assertEquals(1, count($switches));
                $this->assertEquals($switchName, $switches[$switchName]->name);
                $this->assertEquals(count($args), count($ParsedSwitches->getArgsForSwitch($switchName)));
                $this->assertEquals(count($args), $ParsedSwitches->getInvokeCountForSwitch($switchName));
                $this->assertEquals($args, $ParsedSwitches->getArgsForSwitch($switchName));
        }

        public function testCanValidateAllSwitchValuesInOneGo()
        {
                // define the options we are expecting
                $expectedOptions = new DefinedSwitches();
                $switch1 = $expectedOptions->addSwitch('fred', 'desc 1')
                         ->setWithOptionalArg("<fish>", 'the fish that fred likes most')
                         ->setArgValidator(new MustBeString());

                $switch2 = $expectedOptions->addSwitch('harry', 'desc 2')
                         ->setWithOptionalArg('<sauce>', 'the sauce that harry likes most')
                         ->setArgValidator(new MustBeInteger());

                // add the parsed results
                $ParsedSwitches = new ParsedSwitches();
                $ParsedSwitches->addSwitch($expectedOptions, 'fred', 'trout');
                $ParsedSwitches->addSwitch($expectedOptions, 'harry', 'salmon');

                // now, can we validate or not?
                $results = $ParsedSwitches->validateSwitchValues();

                // what happened?
                $this->assertTrue(is_array($results));
                $this->assertEquals(1, count($results));
        }
}
