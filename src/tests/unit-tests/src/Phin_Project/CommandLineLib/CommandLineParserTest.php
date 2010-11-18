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
        public function testCanCreate()
        {
                $obj = new CommandLineParser();
                $this->assertTrue(true);
        }

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
                
                return $options;
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
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);
                $this->assertTrue (is_int($argsIndex));

                $this->assertEquals(2, $argsIndex);
                $this->assertTrue($parsedOptions->testHasSwitch('shortHelp'));
                $this->assertTrue($parsedOptions->testHasSwitch('version'));
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
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);
                $this->assertTrue (is_int($argsIndex));

                $this->assertEquals(3, $argsIndex);
                $this->assertTrue($parsedOptions->testHasSwitch('include'));
                $switches = $parsedOptions->getSwitches();
                $this->assertTrue(isset($switches['include']));
                $this->assertEquals('include', $switches['include']->name);
                $retrievedArgs = $parsedOptions->getArgsForSwitch('include');
                $this->assertEquals('/tmp', $retrievedArgs[0]);
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
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);
                $this->assertTrue (is_int($argsIndex));

                $this->assertEquals(2, $argsIndex);
                $this->assertTrue($parsedOptions->testHasSwitch('include'));
                $switches = $parsedOptions->getSwitches();
                $this->assertTrue(isset($switches['include']));
                $this->assertEquals('include', $switches['include']->name);
                $retrievedArgs = $parsedOptions->getArgsForSwitch('include');
                $this->assertEquals('/tmp', $retrievedArgs[0]);
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
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);
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
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);
                $this->assertTrue (is_int($argsIndex));

                $this->assertEquals(3, $argsIndex);
                $this->assertTrue($parsedOptions->testHasSwitch('include'));
                
                $switches = $parsedOptions->getSwitches();
                $this->assertEquals(1, count($switches));
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
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);
                $this->assertTrue (is_int($argsIndex));

                $this->assertEquals(2, $argsIndex);
                $this->assertTrue($parsedOptions->testHasSwitch('include'));

                $switches = $parsedOptions->getSwitches();
                $this->assertEquals(1, count($switches));
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
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);
                $this->assertTrue (is_int($argsIndex));

                $this->assertEquals(5, $argsIndex);
                $this->assertTrue($parsedOptions->testHasSwitch('include'));

                $switches = $parsedOptions->getSwitches();
                $this->assertEquals(2, count($switches));
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
                list($parsedOptions, $argsIndex) = $parser->parseSwitches($argv, $options);

                // did it work?
                $this->assertTrue ($parsedOptions instanceof ParsedOptions);
                $this->assertTrue (is_int($argsIndex));

                $this->assertEquals(3, $argsIndex);
                $this->assertTrue($parsedOptions->testHasSwitch('shortHelp'));
                $this->assertTrue($parsedOptions->testHasSwitch('longHelp'));

                $switches = $parsedOptions->getSwitches();
                $this->assertEquals(2, count($switches));
                // did we get the include switch?
                $this->assertEquals('shortHelp', $switches['shortHelp']->name);
                $this->assertEquals('longHelp', $switches['longHelp']->name);
        }
}