<?php

/**
 * Copyright (c) 2011 Gradwell dot com Ltd.
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
 * @package     Phix_Project
 * @subpackage  PhixExtensions
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2011 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://gradwell.github.com
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\PhixExtensions;

// use Phix_Project\Phix\CommandsList;
use Phix_Project\Phix\Context;
use Gradwell\CommandLineLib\DefinedSwitches;
use Gradwell\CommandLineLib\DefinedSwitch;
// use Gradwell\CommandLineLib\CommandLineParser;
use Gradwell\ConsoleDisplayLib\DevString;


class DummyCommand extends CommandBase
{
        
}

class DummyCommandWithSwitches extends CommandBase
{
        public function getCommandName()
        {
                return 'DummyCommand:withSwitches';
        }

        public function getCommandDesc()
        {
                return 'A dummy command used to prove that CommandBase does the right thing with its switches';
        }
        
        public function getCommandOptions()
        {
                $options = new DefinedSwitches();

                $options->addSwitch('version', 'show the version number')
                        ->setWithShortSwitch('v')
                        ->setWithLongSwitch('version');

                $options->addSwitch('properties', 'specify the build.properties file to use')
                        ->setWithShortSwitch('b')
                        ->setWithLongSwitch('build.properties')
                        ->setWithRequiredArg('<build.properties>', 'the path to the build.properties file to use')
                        ->setArgHasDefaultValueOf('build.properties');

                $options->addSwitch('packageXml', 'specify the package.xml file to expand')
                        ->setWithShortSwitch('p')
                        ->setWithLongSwitch('packageXml')
                        ->setwithRequiredArg('<package.xml>', 'the path to the package.xml file to use')
                        ->setArgHasDefaultValueOf('.build/package.xml');

                $options->addSwitch('srcFolder', 'specify the src folder to feed into package.xml')
                        ->setWithShortSwitch('s')
                        ->setWithLongSwitch('src')
                        ->setWithRequiredArg('<folder>', 'the path to the folder where the package source files are')
                        ->setArgHasDefaultValueOf('src');

                $options->addSwitch('help', 'displays a summary of how to use this command')
                        ->setWithShortSwitch('h')
                        ->setWithShortSwitch('?')
                        ->setWithLongSwitch('help');

                return $options;
        }

        public function _calculateSwitchDisplayOrder()
        {
                $definedSwitches = $this->getCommandOptions();
                return $this->calculateSwitchDisplayOrder($definedSwitches);
        }
        
        public function _showName(Context $context)
        {
                return $this->showName($context);
        }
}

class CommandBaseTest extends \PHPUnit_Framework_TestCase
{
        public function testCanExtend()
        {
                $obj = new DummyCommand();
                $this->assertTrue($obj instanceof CommandBase);
        }

        public function testThrowsExceptionsWhenNotProperlyExtended()
        {
                $obj = new DummyCommand();

                $caughtAssert = false;
                try
                {
                        $obj->getCommandName();
                }
                catch (\Exception $e)
                {
                        $caughtAssert = true;
                }
                $this->assertTrue($caughtAssert);

                $caughtAssert = false;
                try
                {
                        $obj->getCommandDesc();
                }
                catch (\Exception $e)
                {
                        $caughtAssert = true;
                }
                $this->assertTrue($caughtAssert);

                $caughtAssert = false;
                try
                {
                        $obj->getValidOptions();
                }
                catch (\Exception $e)
                {
                        $caughtAssert = true;
                }
                $this->assertTrue($caughtAssert);

                $context      = new Context();
                $argv         = array();
                $argsIndex    = 0;
                $caughtAssert = false;
                try
                {
                        $obj->validateAndExecute($argv, $argsIndex, $context);
                }
                catch (\Exception $e)
                {
                        $caughtAssert = true;
                }
                $this->assertTrue($caughtAssert);
        }

        public function testDefaultCommandOptionsIsNull()
        {
                // setup
                $obj = new DummyCommand();

                // do the test
                $options = $obj->getCommandOptions();

                // test the results
                $this->assertNull($options);
        }

        public function testDefaultCommandArgsIsEmptyList()
        {
                // setup
                $obj = new DummyCommand();

                // do the test
                $definedArgs = $obj->getCommandArgs();

                // test the results
                $this->assertTrue(is_array($definedArgs));
                $this->assertEquals(0, count($definedArgs));
        }

        public function testOutputsHelpFromSwitches()
        {
                // setup
                $obj = new DummyCommandWithSwitches();
                $context = new Context();
                $context->argvZero = 'phix';
                $context->stdout = new DevString();
                $context->stderr = new DevString();

                // do the test
                $obj->outputHelp($context);

                // test the results
                $stdOutOutput = $context->stdout->_getOutput();
                $stdErrOutput = $context->stderr->_getOutput();

                $this->assertEquals(0, strlen($stdErrOutput));
                $this->assertNotEquals(0, strlen($stdOutOutput));

                // var_dump($stdOutOutput);
                
                // that just tells us we have some sort of output in
                // the expected places
                //
                // do we have the _right_ output?
                $expectedString = <<<EOS
NAME
    phix DummyCommand:withSwitches - A dummy command used to prove that
    CommandBase does the right thing with its switches

SYNOPSIS
    phix DummyCommand:withSwitches [ ? -h -v ] [ help --version ] [ -b
    <build.properties> ] [ -p <package.xml> ] [ -s <folder> ] [
    --build.properties=<build.properties> ] [ --packageXml=<package.xml> ] [
    --src=<folder> ]

OPTIONS
    -? | -h | --help
        displays a summary of how to use this command

    -b <build.properties> | --build.properties=<build.properties>
        specify the build.properties file to use

        The default value for <build.properties> is: build.properties

    -p <package.xml> | --packageXml=<package.xml>
        specify the package.xml file to expand

        The default value for <package.xml> is: .build/package.xml

    -s <folder> | --src=<folder>
        specify the src folder to feed into package.xml

        The default value for <folder> is: src

    -v | --version
        show the version number

IMPLEMENTATION
    This command is implemented in the PHP class:

    * Phix_Project\PhixExtensions\DummyCommandWithSwitches

    which is defined in the file:

    * /home/stuarth/Devel/GWC/phix/src/tests/unit-tests/php/Phix_Project
      /PhixExtensions/CommandBaseTest.php

EOS;
                
                $this->assertEquals($expectedString, $stdOutOutput);
        }

        public function testCalculateCorrectOrderToDisplayShortSwitches()
        {
                // setup
                $obj = new DummyCommandWithSwitches();

                // do the test
                $switches = $obj->_calculateSwitchDisplayOrder();

                // do we have the expected structure back?
                $this->assertTrue(isset($switches['shortSwitchesWithArgs']));
                $this->assertTrue(isset($switches['shortSwitchesWithoutArgs']));

                // has it worked?
                $expectedOrder = array('b', 'p', 's');
                $actualOrder   = array_keys($switches['shortSwitchesWithArgs']);
                $this->assertEquals($expectedOrder, $actualOrder);

                $expectedOrder = array('?', 'h', 'v');
                $actualOrder   = array_keys($switches['shortSwitchesWithoutArgs']);
                $this->assertEquals($expectedOrder, $actualOrder);
        }

        public function testCalculateCorrectOrderToDisplayLongSwitches()
        {
                // setup
                $obj = new DummyCommandWithSwitches();

                // do the test
                $switches = $obj->_calculateSwitchDisplayOrder();

                // do we have the expected structure back?
                $this->assertTrue(isset($switches['longSwitchesWithArgs']));
                $this->assertTrue(isset($switches['longSwitchesWithoutArgs']));

                // has it worked?
                $expectedOrder = array('build.properties', 'packageXml', 'src');
                $actualOrder   = array_keys($switches['longSwitchesWithArgs']);
                $this->assertEquals($expectedOrder, $actualOrder);

                $expectedOrder = array('help', 'version');
                $actualOrder   = array_keys($switches['longSwitchesWithoutArgs']);
                $this->assertEquals($expectedOrder, $actualOrder);
        }

        public function testCalculateCorrectOrderToDisplayAllSwitches()
        {
                // setup
                $obj = new DummyCommandWithSwitches();

                // do the test
                $switches = $obj->_calculateSwitchDisplayOrder();

                // do we have the expected structure back?
                $this->assertTrue(isset($switches['allSwitches']));

                // has it worked?
                $expectedOrder = array('-?', '-b', '-h', '-p', '-s', '-v', '--build.properties', '--help', '--packageXml', '--src', '--version');
                $actualOrder   = array_keys($switches['allSwitches']);
                $this->assertEquals($expectedOrder, $actualOrder);
        }
}