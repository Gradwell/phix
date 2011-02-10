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
 * @package     Phix_Project
 * @subpackage  CommandLineLib
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.Phix-tool.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\CommandLineLib;

use Phix_Project\ValidationLib\MustBeValidFile;
use Phix_Project\ValidationLib\MustBeWriteable;
use Phix_Project\ValidationLib\MustBeValidPath;

class CommandLineParserTest extends \PHPUnit_Framework_TestCase
{
        /**
         *
         * @return DefinedSwitches
         */
        protected function setupOptions()
        {
                $options = new DefinedSwitches();

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
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '-vh',
                        'help'
                );

                $parser = new CommandLineParser();
                list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($ParsedSwitches instanceof ParsedSwitches);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(2, $argsIndex);

                $this->assertTrue($ParsedSwitches->testHasSwitch('shortHelp'));
                $this->assertTrue($ParsedSwitches->testHasSwitch('version'));

                $switches = $ParsedSwitches->getSwitchesByOrder();
                $this->assertEquals(4, count($switches));
                $this->assertEquals('version', $switches[0]->name);
                $this->assertTrue($switches[0]->values[0]);
                $this->assertEquals('shortHelp', $switches[1]->name);
                $this->assertTrue($switches[1]->values[0]);
                $this->assertEquals('srcFolder', $switches[2]->name);
                $this->assertEquals('/usr/bin/php', $switches[2]->values[0]);
                $this->assertTrue($switches[3]->testIsDefaultValue());
                $this->assertEquals('warnings', $switches[3]->name);
                $this->assertEquals('all', $switches[3]->values[0]);
                $this->assertTrue($switches[3]->testIsDefaultValue());
        }

        public function testCanParseShortSwitchWithArg()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '-I',
                        '/tmp',
                        'help'
                );

                $parser = new CommandLineParser();
                list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($ParsedSwitches instanceof ParsedSwitches);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(3, $argsIndex);

                $this->assertTrue($ParsedSwitches->testHasSwitch('include'));
                $switches = $ParsedSwitches->getSwitches();
                $this->assertTrue(isset($switches['include']));
                $this->assertEquals('include', $switches['include']->name);
                $this->assertEquals('/tmp', $switches['include']->getFirstValue());
        }

        public function testCanParseShortSwitchWithEmbeddedArg()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '-I/tmp',
                        'help'
                );

                $parser = new CommandLineParser();
                list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($ParsedSwitches instanceof ParsedSwitches);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(2, $argsIndex);

                $this->assertTrue($ParsedSwitches->testHasSwitch('include'));
                $switches = $ParsedSwitches->getSwitches();
                $this->assertTrue(isset($switches['include']));
                $this->assertEquals('include', $switches['include']->name);
                $this->assertEquals('/tmp', $switches['include']->getFirstValue());
        }

        public function testCanParseLongSwitches()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '--version',
                        '--help',
                        'help'
                );

                $parser = new CommandLineParser();
                list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($ParsedSwitches instanceof ParsedSwitches);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(3, $argsIndex);

                $this->assertTrue($ParsedSwitches->testHasSwitch('longHelp'));
                $this->assertTrue($ParsedSwitches->testHasSwitch('version'));
        }

        public function testCanParseLongSwitchWithArgument()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '--include',
                        '/tmp',
                        'help'
                );

                $parser = new CommandLineParser();
                list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($ParsedSwitches instanceof ParsedSwitches);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(3, $argsIndex);

                $this->assertTrue($ParsedSwitches->testHasSwitch('include'));
                
                $switches = $ParsedSwitches->getSwitches();
                $this->assertEquals(3, count($switches));
                $this->assertEquals('include', $switches['include']->name);
                $this->assertEquals('/tmp', $switches['include']->values[0]);
        }

        public function testCanParseLongSwitchWithEmbeddedArgument()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '--include=/tmp',
                        'help'
                );

                $parser = new CommandLineParser();
                list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($ParsedSwitches instanceof ParsedSwitches);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(2, $argsIndex);

                $this->assertTrue($ParsedSwitches->testHasSwitch('include'));

                $switches = $ParsedSwitches->getSwitches();
                $this->assertEquals(3, count($switches));
                $this->assertEquals('include', $switches['include']->name);
                $this->assertEquals('/tmp', $switches['include']->values[0]);
        }

        public function testCanParseSwitchesThatRepeat()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '-lfred',
                        '--include',
                        '/tmp',
                        '--lib=harry',
                        'help'
                );

                $parser = new CommandLineParser();
                list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($ParsedSwitches instanceof ParsedSwitches);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(5, $argsIndex);

                $this->assertTrue($ParsedSwitches->testHasSwitch('include'));

                $switches = $ParsedSwitches->getSwitches();
                $this->assertEquals(4, count($switches));
                // did we get the include switch?
                $this->assertEquals('include', $switches['include']->name);
                $this->assertEquals('/tmp', $switches['include']->values[0]);
                // did we get both library switches?
                $retrievedArgs = $ParsedSwitches->getArgsForSwitch('library');
                $this->assertEquals(2, count($switches['library']->values));
                $this->assertEquals('fred', $switches['library']->values[0]);
                $this->assertEquals('harry', $switches['library']->values[1]);
        }

        public function testCanTellShortAndLongSwitchesApart()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '-?',
                        '--?',
                        'help'
                );

                $parser = new CommandLineParser();
                list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($ParsedSwitches instanceof ParsedSwitches);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(3, $argsIndex);

                $this->assertTrue($ParsedSwitches->testHasSwitch('shortHelp'));
                $this->assertTrue($ParsedSwitches->testHasSwitch('longHelp'));

                $switches = $ParsedSwitches->getSwitches();
                $this->assertEquals(4, count($switches));
                
                // did we get the include switch?
                $this->assertEquals('shortHelp', $switches['shortHelp']->name);
                $this->assertEquals('longHelp', $switches['longHelp']->name);
        }

        public function testParserStopsOnDoubleDash()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '-vh',
                        '--',
                        'help'
                );

                $parser = new CommandLineParser();
                list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($ParsedSwitches instanceof ParsedSwitches);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(3, $argsIndex);

                $this->assertTrue($ParsedSwitches->testHasSwitch('shortHelp'));
                $this->assertTrue($ParsedSwitches->testHasSwitch('version'));

                $switches = $ParsedSwitches->getSwitchesByOrder();
                $this->assertEquals(4, count($switches));
                $this->assertEquals('version', $switches[0]->name);
                $this->assertTrue($switches[0]->values[0]);
                $this->assertEquals('shortHelp', $switches[1]->name);
                $this->assertTrue($switches[1]->values[0]);
                $this->assertEquals('srcFolder', $switches[2]->name);
                $this->assertEquals('/usr/bin/php', $switches[2]->values[0]);
                $this->assertTrue($switches[3]->testIsDefaultValue());
                $this->assertEquals('warnings', $switches[3]->name);
                $this->assertEquals('all', $switches[3]->values[0]);
                $this->assertTrue($switches[3]->testIsDefaultValue());
        }

        public function testParserThrowsExceptionWhenUnexpectedShortSwitch()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '-vhx',
                        'help'
                );

                $caughtException = false;
                try
                {
                        $parser = new CommandLineParser();
                        list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);
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
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '--panic',
                        'help'
                );

                $caughtException = false;
                try
                {
                        $parser = new CommandLineParser();
                        list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);
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
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '-I'
                );

                $caughtException = false;
                try
                {
                        $parser = new CommandLineParser();
                        list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);
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
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '--include'
                );

                $caughtException = false;
                try
                {
                        $parser = new CommandLineParser();
                        list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);
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
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '--include='
                );

                $caughtException = false;
                try
                {
                        $parser = new CommandLineParser();
                        list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);
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
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '-vIh'
                );

                $caughtException = false;
                try
                {
                        $parser = new CommandLineParser();
                        list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);
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
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '-vhI',
                        '/fred'
                );

                $parser = new CommandLineParser();
                list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($ParsedSwitches instanceof ParsedSwitches);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(3, $argsIndex);

                $this->assertTrue($ParsedSwitches->testHasSwitch('shortHelp'));
                $this->assertTrue($ParsedSwitches->testHasSwitch('version'));
                $this->assertTrue($ParsedSwitches->testHasSwitch('include'));

                $switches = $ParsedSwitches->getSwitchesByOrder();
                $this->assertEquals(5, count($switches));
                $this->assertEquals('version', $switches[0]->name);
                $this->assertTrue($switches[0]->values[0]);
                $this->assertEquals('shortHelp', $switches[1]->name);
                $this->assertTrue($switches[1]->values[0]);
                $this->assertEquals('include', $switches[2]->name);
                $this->assertEquals('/fred', $switches[2]->values[0]);

                // don't forget the switch with the default value
                $this->assertEquals('srcFolder', $switches[3]->name);
                $this->assertEquals('/usr/bin/php', $switches[3]->values[0]);
                $this->assertTrue($switches[3]->testIsDefaultValue());
                $this->assertEquals('warnings', $switches[4]->name);
                $this->assertEquals('all', $switches[4]->values[0]);
                $this->assertTrue($switches[4]->testIsDefaultValue());
        }
        
        public function testCanLumpShortSwitchesTogetherWithLastOneHavingAOptionalArgument()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '-vhW',
                );

                $parser = new CommandLineParser();
                list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($ParsedSwitches instanceof ParsedSwitches);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(2, $argsIndex);
                
                $this->assertTrue($ParsedSwitches->testHasSwitch('shortHelp'));
                $this->assertTrue($ParsedSwitches->testHasSwitch('version'));
                $this->assertTrue($ParsedSwitches->testHasSwitch('warnings'));

                $switches = $ParsedSwitches->getSwitchesByOrder();
                $this->assertEquals(4, count($switches));
                $this->assertEquals('version', $switches[0]->name);
                $this->assertTrue($switches[0]->values[0]);
                $this->assertEquals('shortHelp', $switches[1]->name);
                $this->assertTrue($switches[1]->values[0]);
                $this->assertEquals('warnings', $switches[2]->name);
                $this->assertEquals('all', $switches[2]->values[0]);

                // don't forget the default values
                $this->assertEquals('srcFolder', $switches[3]->name);
                $this->assertEquals('/usr/bin/php', $switches[3]->values[0]);
                $this->assertTrue($switches[3]->testIsDefaultValue());
        }

        public function testSwitchesCanHaveOptionalArgs()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '--warnings',
                );

                $parser = new CommandLineParser();
                list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($ParsedSwitches instanceof ParsedSwitches);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(2, $argsIndex);

                $this->assertTrue($ParsedSwitches->testHasSwitch('warnings'));

                $switches = $ParsedSwitches->getSwitches();
                $this->assertEquals(2, count($switches));
                $this->assertEquals('warnings', $switches['warnings']->name);
                $this->assertEquals('all', $switches['warnings']->values[0]);
        }

        public function testOptionalArgsCanHaveValues()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest',
                        '--warnings=all',
                );

                $parser = new CommandLineParser();
                list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($ParsedSwitches instanceof ParsedSwitches);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(2, $argsIndex);

                $this->assertTrue($ParsedSwitches->testHasSwitch('warnings'));

                $switches = $ParsedSwitches->getSwitches();
                $this->assertEquals(2, count($switches));
                $this->assertEquals('warnings', $switches['warnings']->name);
                $this->assertEquals('all', $switches['warnings']->values[0]);
        }

        public function testDefaultValuesAreAddedIfSwitchNotSeen()
        {
                $options = $this->setupOptions();
                $this->assertTrue($options instanceof DefinedSwitches);

                $argv = array
                (
                        'PhixTest'
                );

                $parser = new CommandLineParser();
                list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 1, $options);

                // did it work?
                $this->assertTrue ($ParsedSwitches instanceof ParsedSwitches);

                // is the argsIndex pointing at the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals(1, $argsIndex);

                // what defaults have leeched through?
                // it should only be the required switches that have
                // default values

                $switches = $ParsedSwitches->getSwitches();
                $this->assertTrue(is_array($switches));
                $this->assertEquals(2, count($switches));
                $this->assertTrue(isset($switches['warnings']));
                $this->assertTrue(isset($switches['srcFolder']));
        }

        public function testDefaultValuesAreAddedIfNoSwitchesPresent()
        {
                // this is a bug I first discovered in Phix, and here
                // is the code necessary to reproduce the faults
                $options = new DefinedSwitches();

                $options->addSwitch('properties', 'specify the build.properties file to use')
                        ->setWithShortSwitch('b')
                        ->setWithLongSwitch('build.properties')
                        ->setWithRequiredArg('<build.properties>', 'the path to the build.properties file to use')
                        ->setArgHasDefaultValueOf('build.properties')
                        ->setArgValidator(new MustBeValidFile());

                $options->addSwitch('packageXml', 'specify the package.xml file to expand')
                        ->setWithShortSwitch('p')
                        ->setWithLongSwitch('packageXml')
                        ->setwithRequiredArg('<package.xml>', 'the path to the package.xml file to use')
                        ->setArgHasDefaultValueOf('.build/package.xml')
                        ->setArgValidator(new MustBeValidFile())
                        ->setArgValidator(new MustBeWriteable());

                $options->addSwitch('srcFolder', 'specify the src folder to feed into package.xml')
                        ->setWithShortSwitch('s')
                        ->setWithLongSwitch('src')
                        ->setWithRequiredArg('<folder>', 'the path to the folder where the package source files are')
                        ->setArgHasDefaultValueOf('src')
                        ->setArgValidator(new MustBeValidPath());

                $argv = array
                (
                        './Phix',
                        'pear:expand-package-xml'
                );

                $parser = new CommandLineParser();
                list($parsedSwitches, $argsIndex) = $parser->parseSwitches($argv, 2, $options);

                // did it work?
                $this->assertTrue ($parsedSwitches instanceof ParsedSwitches);

                // is the argsIndex pointing to the right place?
                $this->assertTrue (is_int($argsIndex));
                $this->assertEquals (2, $argsIndex);

                // are the defaults present?
                $switches = $parsedSwitches->getSwitches();
                $this->assertTrue(is_array($switches));
                $this->assertEquals(3, count($switches));
                $this->assertTrue(isset($switches['properties']));
                $this->assertTrue(isset($switches['packageXml']));
                $this->assertTrue(isset($switches['srcFolder']));
        }
}
