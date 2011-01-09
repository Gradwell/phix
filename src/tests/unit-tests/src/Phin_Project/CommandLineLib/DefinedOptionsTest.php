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

class DefinedOptionsTest extends \PHPUnit_Framework_TestCase
{
        public function testCanCreateOptions()
        {
                $obj = new DefinedOptions();
                $this->assertTrue(true);
        }

        public function testCanCreateOptionsWithOneSwitch()
        {
                $switchName = 'help';
                $switchDesc = 'Display this help message';

                $obj = new DefinedOptions();
                $origSwitch = $obj->addSwitch($switchName, $switchDesc);
                $origSwitch->setWithShortSwitch('h');

                // did it work?
                $this->assertTrue($obj->testHasSwitchByName($switchName));
        }

        public function testCanRetrieveSwitchByName()
        {
                $switchName = 'help';
                $switchDesc = 'Display this help message';

                $obj = new DefinedOptions();
                $origSwitch = $obj->addSwitch($switchName, $switchDesc);
                $origSwitch->setWithShortSwitch('h');

                // did it work?
                $this->assertTrue($obj->testHasSwitchByName($switchName));
                $retrievedSwitch = $obj->getSwitchByName($switchName);
                $this->assertSame($origSwitch, $retrievedSwitch);

                // what happens if we look for a switch that does
                // not exist?
                $notASwitchName = 'version';
                $this->assertFalse($obj->testHasSwitchByName($notASwitchName));
                $caughtException = false;
                try
                {
                        $retrievedSwitch = $obj->getSwitchByName($notASwitchName);
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);
        }

        public function testCanRetrieveSwitchByShortSwitch()
        {
                $switchName = 'help';
                $switchDesc = 'Display this help message';

                $obj = new DefinedOptions();
                $origSwitch = $obj->addSwitch($switchName, $switchDesc);
                $origSwitch->setWithShortSwitch('h')
                           ->setWithShortSwitch('?');

                // did it work?
                $this->assertTrue($obj->testHasSwitchByName($switchName));
                $retrievedSwitch1 = $obj->getShortSwitch('h');
                $this->assertSame($origSwitch, $retrievedSwitch1);
                $retrievedSwitch2 = $obj->getShortSwitch('?');
                $this->assertSame($origSwitch, $retrievedSwitch2);
                $this->assertSame($retrievedSwitch1, $retrievedSwitch2);

                // what happens if we try to retrieve a short switch
                // that does not exist?
                $notASwitchName = 'version';
                $this->assertFalse($obj->testHasSwitchByName($notASwitchName));
                $caughtException = false;
                try
                {
                        $retrievedSwitch = $obj->getShortSwitch('v');
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);
        }

        public function testCanRetrieveSwitchByLongSwitch()
        {
                $switchName = 'help';
                $switchDesc = 'Display this help message';

                $obj = new DefinedOptions();
                $origSwitch = $obj->addSwitch($switchName, $switchDesc);
                $origSwitch->setWithLongSwitch('help')
                           ->setWithLongSwitch('?');

                // did it work?
                $this->assertTrue($obj->testHasSwitchByName($switchName));
                $retrievedSwitch1 = $obj->getLongSwitch('help');
                $this->assertSame($origSwitch, $retrievedSwitch1);
                $retrievedSwitch2 = $obj->getLongSwitch('?');
                $this->assertSame($origSwitch, $retrievedSwitch2);
                $this->assertSame($retrievedSwitch1, $retrievedSwitch2);

                // what happens if we try to retrieve a long switch
                // that does not exist?
                $notASwitchName = 'version';
                $this->assertFalse($obj->testHasSwitchByName($notASwitchName));
                $caughtException = false;
                try
                {
                        $retrievedSwitch = $obj->getLongSwitch($notASwitchName);
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                }
                $this->assertTrue($caughtException);
        }

        public function testCanRetrieveBothShortAndLongSwitches()
        {
                $switchName = 'help';
                $switchDesc = 'Display this help message';

                $obj = new DefinedOptions();
                $origSwitch = $obj->addSwitch($switchName, $switchDesc);
                $origSwitch->setWithShortSwitch('h')
                           ->setWithShortSwitch('?')
                           ->setWithLongSwitch('help')
                           ->setWithLongSwitch('?');

                // did it work?
                $this->assertTrue($obj->testHasSwitchByName($switchName));
                $retrievedSwitch1 = $obj->getShortSwitch('h');
                $this->assertSame($origSwitch, $retrievedSwitch1);
                $retrievedSwitch2 = $obj->getShortSwitch('?');
                $this->assertSame($origSwitch, $retrievedSwitch2);
                $this->assertSame($retrievedSwitch1, $retrievedSwitch2);
                $retrievedSwitch3 = $obj->getLongSwitch('help');
                $this->assertSame($origSwitch, $retrievedSwitch1);
                $retrievedSwitch4 = $obj->getLongSwitch('?');
                $this->assertSame($origSwitch, $retrievedSwitch2);
                $this->assertSame($retrievedSwitch1, $retrievedSwitch2);
                $this->assertSame($retrievedSwitch1, $retrievedSwitch3);
                $this->assertSame($retrievedSwitch1, $retrievedSwitch4);
        }

        public function testCanRetrieveArrayOfAllSwitches()
        {
                $switch1Name = 'help';
                $switch1Desc = 'Display this help message';

                $obj = new DefinedOptions();
                $switch1 = $obj->addSwitch($switch1Name, $switch1Desc);
                $switch1->setWithShortSwitch('h')
                        ->setWithShortSwitch('?')
                        ->setWithLongSwitch('help')
                        ->setWithLongSwitch('?');

                $switch2Name = 'version';
                $switch2Desc = 'Display the version number of this app';

                $switch2 = $obj->addSwitch($switch2Name, $switch2Desc);
                $switch2->setWithShortSwitch('v')
                        ->setWithShortSwitch('?')
                        ->setWithLongSwitch('?')
                        ->setWithLongSwitch('version');

                // did it work?
                $switches = $obj->getSwitches();
                $this->assertTrue(is_array($switches));
                $this->assertEquals(2, count($switches));

                $this->assertTrue(isset($switches[$switch1Name]));
                $this->assertSame($switch1, $switches[$switch1Name]);

                $this->assertTrue(isset($switches[$switch2Name]));
                $this->assertSame($switch2, $switches[$switch2Name]);
        }
}