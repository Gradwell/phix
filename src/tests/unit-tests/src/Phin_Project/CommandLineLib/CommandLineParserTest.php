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

class CommandLineParserTest extends \PHPUnit_Framework_TestCase
{
        /**
         *
         * @return DefinedOptions
         */
        protected function setupOptions()
        {
                $options = new DefinedOptions();

                $options->addSwitch('shortHelp', 'display this help message')
                        ->setWithShortSwitch('h')
                        ->setWithShortSwitch('?');

                $options->addSwitch('longHelp', 'display full help message')
                        ->setWithLongSwitch('help')
                        ->setWithLongSwitch('?');

                $options->addSwitch('version', 'display app version number')
                        ->setWithShortSwitch('v')
                        ->setWithLongSwitch('version');

                $options->addSwitch('include', 'add a folder to search within')
                        ->setWithShortSwitch('I')
                        ->setWithLongSwitch('include')
                        ->setWithRequiredArg('<path>', 'path to the folder to search');

                $options->addSwitch('library', 'add a library to link against')
                        ->setWithShortSwitch('l')
                        ->setWithLongSwitch('lib')
                        ->setWithRequiredArg('<lib>', 'the name of a library to link against')
                        ->setSwitchIsRepeatable();
                
		$options->addSwitch('srcFolder', 'add a folder to load source code from')
			->setWithShortSwitch('s')
			->setWithLongSwitch('srcFolder')
			->setWithRequiredArg('<srcFolder>', 'path to the folder to load source code from')
			->setArgHasDefaultValueOf('/usr/bin/php');

                $options->addSwitch('warnings', 'enable warnings')
                        ->setWithShortSwitch('W')
                        ->setWithLongSwitch('warnings')
                        ->setWithOptionalArg('<warnings>', 'comma-separated list of warnings to enable')
                        ->setArgHasDefaultValueOf('all')
                        ->setSwitchIsRepeatable();

                return $options;
        }

        public function testCanCreate()
        {
                $obj = new CommandLineParser();
                $this->assertTrue(true);
        }

        public function testCanParseShortSwitches()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '-vh',
                        'help'
                );

                $parser = new CommandLineParser();
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(2, $argsIndex);

                $this->assertTrue($parsedOptions->testHasSwitch('shortHelp'));
                $this->assertTrue($parsedOptions->testHasSwitch('version'));

                $switches = $parsedOptions->getSwitchesByOrder();
                $this->assertEquals(2, count($switches));
                $this->assertEquals('version', $switches[0]->name);
                $this->assertTrue($switches[0]->values[0]);
                $this->assertEquals('shortHelp', $switches[1]->name);
                $this->assertTrue($switches[1]->values[0]);
        }

        public function testCanParseShortSwitchWithArg()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '-I',
                        '/tmp',
                        'help'
                );

                $parser = new CommandLineParser();
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(3, $argsIndex);

                $this->assertTrue($parsedOptions->testHasSwitch('include'));
                $switches = $parsedOptions->getSwitches();
                $this->assertTrue(isset($switches['include']));
                $this->assertEquals('include', $switches['include']->name);
                $this->assertEquals('/tmp', $switches['include']->getFirstValue());
        }

        public function testCanParseShortSwitchWithEmbeddedArg()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '-I/tmp',
                        'help'
                );

                $parser = new CommandLineParser();
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(2, $argsIndex);

                $this->assertTrue($parsedOptions->testHasSwitch('include'));
                $switches = $parsedOptions->getSwitches();
                $this->assertTrue(isset($switches['include']));
                $this->assertEquals('include', $switches['include']->name);
                $this->assertEquals('/tmp', $switches['include']->getFirstValue());
        }

        public function testCanParseLongSwitches()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '--version',
                        '--help',
                        'help'
                );

                $parser = new CommandLineParser();
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(3, $argsIndex);

                $this->assertTrue($parsedOptions->testHasSwitch('longHelp'));
                $this->assertTrue($parsedOptions->testHasSwitch('version'));
        }

        public function testCanParseLongSwitchWithArgument()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '--include',
                        '/tmp',
                        'help'
                );

                $parser = new CommandLineParser();
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(3, $argsIndex);

                $this->assertTrue($parsedOptions->testHasSwitch('include'));
                
                $switches = $parsedOptions->getSwitches();
                $this->assertEquals(2, count($switches));
                $this->assertEquals('include', $switches['include']->name);
                $retrievedArgs = $parsedOptions->getArgsForSwitch('include');
                $this->assertEquals('/tmp', $retrievedArgs[0]);
        }

        public function testCanParseLongSwitchWithEmbeddedArgument()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '--include=/tmp',
                        'help'
                );

                $parser = new CommandLineParser();
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(2, $argsIndex);

                $this->assertTrue($parsedOptions->testHasSwitch('include'));

                $switches = $parsedOptions->getSwitches();
                $this->assertEquals(2, count($switches));
                $this->assertEquals('include', $switches['include']->name);
                $retrievedArgs = $parsedOptions->getArgsForSwitch('include');
                $this->assertEquals('/tmp', $retrievedArgs[0]);
        }

        public function testCanParseSwitchesThatRepeat()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '-lfred',
                        '--include',
                        '/tmp',
                        '--lib=harry',
                        'help'
                );

                $parser = new CommandLineParser();
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(5, $argsIndex);

                $this->assertTrue($parsedOptions->testHasSwitch('include'));

                $switches = $parsedOptions->getSwitches();
                $this->assertEquals(3, count($switches));
                // did we get the include switch?
                $this->assertEquals('include', $switches['include']->name);
                $retrievedArgs = $parsedOptions->getArgsForSwitch('include');
                $this->assertEquals('/tmp', $retrievedArgs[0]);
                // did we get both library switches?
                $retrievedArgs = $parsedOptions->getArgsForSwitch('library');
                $this->assertEquals(2, count($retrievedArgs));
                $this->assertEquals('fred', $retrievedArgs[0]);
                $this->assertEquals('harry', $retrievedArgs[1]);
        }

        public function testCanTellShortAndLongSwitchesApart()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '-?',
                        '--?',
                        'help'
                );

                $parser = new CommandLineParser();
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(3, $argsIndex);

                $this->assertTrue($parsedOptions->testHasSwitch('shortHelp'));
                $this->assertTrue($parsedOptions->testHasSwitch('longHelp'));

                $switches = $parsedOptions->getSwitches();
                $this->assertEquals(3, count($switches));
                
                // did we get the include switch?
                $this->assertEquals('shortHelp', $switches['shortHelp']->name);
                $this->assertEquals('longHelp', $switches['longHelp']->name);
        }

        public function testParserStopsOnDoubleDash()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '-vh',
                        '--',
                        'help'
                );

                $parser = new CommandLineParser();
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(3, $argsIndex);

                $this->assertTrue($parsedOptions->testHasSwitch('shortHelp'));
                $this->assertTrue($parsedOptions->testHasSwitch('version'));

                $switches = $parsedOptions->getSwitchesByOrder();
                $this->assertEquals(2, count($switches));
                $this->assertEquals('version', $switches[0]->name);
                $this->assertTrue($switches[0]->values[0]);
                $this->assertEquals('shortHelp', $switches[1]->name);
                $this->assertTrue($switches[1]->values[0]);
        }

        public function testParserThrowsExceptionWhenUnexpectedShortSwitch()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '-vhx',
                        'help'
                );

                $caughtException = false;
                try
                {
                        $parser = new CommandLineParser();
                        list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                }

                // did it work?
                $this->assertTrue ($caughtException);
        }

        public function testParserThrowsExceptionWhenUnexpectedLongSwitch()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '--panic',
                        'help'
                );

                $caughtException = false;
                try
                {
                        $parser = new CommandLineParser();
                        list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                }

                // did it work?
                $this->assertTrue ($caughtException);
        }

        public function testParserThrowsExceptionWhenShortSwitchMissingArg()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '-I'
                );

                $caughtException = false;
                try
                {
                        $parser = new CommandLineParser();
                        list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                        $this->assertEquals("switch -I expected argument", $e->getMessage());
                }

                // did it work?
                $this->assertTrue ($caughtException);
        }

        public function testParserThrowsExceptionWhenLongSwitchMissingArg()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '--include'
                );

                $caughtException = false;
                try
                {
                        $parser = new CommandLineParser();
                        list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                        $this->assertEquals("switch --include expected argument", $e->getMessage());
                }

                // did it work?
                $this->assertTrue ($caughtException);

                // there is more than one way to leave out an argument
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '--include='
                );

                $caughtException = false;
                try
                {
                        $parser = new CommandLineParser();
                        list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                        $this->assertEquals("switch --include expected argument", $e->getMessage());
                }

                // did it work?
                $this->assertTrue ($caughtException);
        }

        public function testParserThrowsExceptionWhenSwitchInMiddleMissingArg()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '-vIh'
                );

                $caughtException = false;
                try
                {
                        $parser = new CommandLineParser();
                        list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);
                }
                catch (\Exception $e)
                {
                        $caughtException = true;
                        $this->assertEquals("switch -I expected argument", $e->getMessage());
                }

                // did it work?
                $this->assertTrue ($caughtException);
        }

        public function testCanLumpShortSwitchesTogetherWithLastOneRequiringAnArgument()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '-vhI',
                        '/fred'
                );

                $parser = new CommandLineParser();
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(3, $argsIndex);

                $this->assertTrue($parsedOptions->testHasSwitch('shortHelp'));
                $this->assertTrue($parsedOptions->testHasSwitch('version'));
                $this->assertTrue($parsedOptions->testHasSwitch('include'));

                $switches = $parsedOptions->getSwitchesByOrder();
                $this->assertEquals(3, count($switches));
                $this->assertEquals('version', $switches[0]->name);
                $this->assertTrue($switches[0]->values[0]);
                $this->assertEquals('shortHelp', $switches[1]->name);
                $this->assertTrue($switches[1]->values[0]);
                $this->assertEquals('include', $switches[2]->name);
                $this->assertEquals('/fred', $switches[2]->values[0]);
        }
        
        public function testCanLumpShortSwitchesTogetherWithLastOneHavingAOptionalArgument()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '-vhW',
                );

                $parser = new CommandLineParser();
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(2, $argsIndex);
                
                $this->assertTrue($parsedOptions->testHasSwitch('shortHelp'));
                $this->assertTrue($parsedOptions->testHasSwitch('version'));
                $this->assertTrue($parsedOptions->testHasSwitch('warnings'));

                $switches = $parsedOptions->getSwitchesByOrder();
                $this->assertEquals(3, count($switches));
                $this->assertEquals('version', $switches[0]->name);
                $this->assertTrue($switches[0]->values[0]);
                $this->assertEquals('shortHelp', $switches[1]->name);
                $this->assertTrue($switches[1]->values[0]);
                $this->assertEquals('warnings', $switches[2]->name);
                $this->assertEquals('all', $switches[2]->values[0]);
        }

        public function testSwitchesCanHaveOptionalArgs()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '--warnings',
                );

                $parser = new CommandLineParser();
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(2, $argsIndex);

                $this->assertTrue($parsedOptions->testHasSwitch('warnings'));

                $switches = $parsedOptions->getSwitches();
                $this->assertEquals(1, count($switches));
                $this->assertEquals('warnings', $switches['warnings']->name);
                $this->assertEquals('all', $switches['warnings']->values[0]);
        }

        public function testOptionalArgsCanHaveValues()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest',
                        '--warnings=all',
                );

                $parser = new CommandLineParser();
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(2, $argsIndex);

                $this->assertTrue($parsedOptions->testHasSwitch('warnings'));

                $switches = $parsedOptions->getSwitches();
                $this->assertEquals(1, count($switches));
                $this->assertEquals('warnings', $switches['warnings']->name);
                $this->assertEquals('all', $switches['warnings']->values[0]);
        }

        public function testDefaultValuesAreAddedIfSwitchNotSeen()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedOptions);

                $argv = array
                (
                        'phinTest'
                );

                $parser = new CommandLineParser();
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(1, $argsIndex);

                // what defaults have leeched through?
                // it should only be the required switches that have
                // default values

                $switches = $parsedOptions->getSwitches();
                $this->assertTrue(is_array($switches));
                $this->assertEquals(1, count($switches));
                $this->assertTrue(isset($switches['warnings']));
        }
}
