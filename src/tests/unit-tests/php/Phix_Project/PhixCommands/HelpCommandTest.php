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
 * @subpackage  PhixCommands
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2011 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://gradwell.github.com
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\PhixCommands;

use Phix_Project\Phix\Context;
use Phix_Project\Phix\CommandsList;
use Phix_Project\PhixExtensions\CommandInterface;
use Phix_Project\PhixSwitches\PhixSwitches;

use Gradwell\CommandLineLib\DefinedSwitches;
use Gradwell\ConsoleDisplayLib\DevString;

class HelpCommandTest extends \PHPUnit_Framework_TestCase
{
        public function testCanCreate()
        {
                $obj = new HelpCommand();
                $this->assertTrue($obj instanceof HelpCommand);
        }

        public function testImplementsCommandInterface()
        {
                $obj = new HelpCommand();
                $this->assertTrue($obj instanceof CommandInterface);
        }

        public function testCommandIsCalledHelp()
        {
                $obj = new HelpCommand();
                $name = $obj->getCommandName();

                $this->assertTrue(is_string($name));
                $this->assertNotEquals(0, strlen($name));
        }

        public function testCommandAsDescription()
        {
                $obj = new HelpCommand();
                $desc = $obj->getCommandDesc();

                $this->assertTrue(is_string($desc));
                $this->assertNotEquals(0, strlen($desc));
        }

        public function testShowsGeneralHelpWhenNoArgs()
        {
                // setup
                $obj = new HelpCommand();
                $context = new Context();
                $context->argvZero = 'phix';
                $context->phixDefinedSwitches = PhixSwitches::buildSwitches();
                $context->commandsList->addClass('Phix_Project\PhixCommands\HelpCommand');
                $context->stdout = new DevString();
                $context->stderr = new DevString();

                $args = array ('phix', 'help');
                $argsIndex = 2;

                // do the test
                $retVal = $obj->validateAndExecute($args, $argsIndex, $context);

                // did the general help get shown?
                $stdoutOutput = $context->stdout->_getOutput();
                $stderrOutput = $context->stderr->_getOutput();

                $this->assertEquals(0, strlen($stderrOutput));
                $this->assertNotEquals(0, strlen($stdoutOutput));

                $expectedString = <<<EOS
phix @@PACKAGE_VERSION@@ http://gradwell.github.com
Copyright (c) 2010 Gradwell dot com Ltd. Released under the BSD license

SYNOPSIS
    phix [ -? -d -h -v ] [ --? --debug --help --version ] [ -I <path> ] [
    --include=<path> ] [ command ] [ command-options ]

OPTIONS
    Use the following switches in front of any <command> to have the following
    effects.

    -? | -h
        display a summary of the command-line structure

    -I<path> | --include=<path>
        add a folder to load commands from

        phix finds all of its commands by searching PHP's include_path for PHP
        files in folders called 'PhixCommands'. If you want to phix to look in
        other folders without having to add them to PHP's include_path, use
        --include to tell phix to look in these folders.

        phix expects '<path>' to point to a folder that conforms to the PSR0
        standard for autoloaders.

        For example, if your command is the class '\Me\Tools\PhixCommands
        \ScheduledTask', phix would expect to autoload this class from the 'Me
        /Tools/PhixCommands/ScheduledTask.php' file.

        If your class lives in the './myApp/lib/Me/Tools/PhixCommands' folder,
        you would call phix with 'phix --include=./myApp/lib'

    -d | --debug
        enable debugging output

    -v | --version
        display phix version number

    --? | --help
        display a full list of supported commands

COMMANDS
    help # get detailed help about a specific phix command

    See phix help <command> for detailed help on <command>

EOS;
                $this->assertEquals($expectedString, $stdoutOutput);

                // did the right return value get returned?
                $this->assertEquals(0, $retVal);
        }

        public function testDisplaysErrorWhenAskedForHelpAboutUnknownCommand()
        {
                // setup
                $obj = new HelpCommand();
                $context = new Context();
                $context->argvZero = 'phix';
                $context->stdout = new DevString();
                $context->stderr = new DevString();

                $args = array ('phix', 'help', 'fred');
                $argsIndex = 2;

                // do the test
                $retVal = $obj->validateAndExecute($args, $argsIndex, $context);

                // test the results
                $stdoutOutput = $context->stdout->_getOutput();
                $stderrOutput = $context->stderr->_getOutput();

                $this->assertNotEquals(0, strlen($stderrOutput));
                $this->assertEquals(0, strlen($stdoutOutput));

                $expectedString = <<<EOS
*** error:  unknown command fred
use phix --help for a list of all available commands

EOS;
                $this->assertEquals($expectedString, $stderrOutput);

                // did the right return value get returned?
                $this->assertEquals(1, $retVal);
        }

        public function testShowsHelpOnACommand()
        {
                // setup
                $obj = new HelpCommand();
                $context = new Context();
                $context->argvZero = 'phix';
                $context->phixDefinedSwitches = PhixSwitches::buildSwitches();
                $context->commandsList->addClass('Phix_Project\PhixCommands\HelpCommand');
                $context->stdout = new DevString();
                $context->stderr = new DevString();

                $args = array ('phix', 'help', 'help');
                $argsIndex = 2;

                // do the test
                $retVal = $obj->validateAndExecute($args, $argsIndex, $context);

                // did the general help get shown?
                $stdoutOutput = $context->stdout->_getOutput();
                $stderrOutput = $context->stderr->_getOutput();

                $this->assertEquals(0, strlen($stderrOutput));
                $this->assertNotEquals(0, strlen($stdoutOutput));

                $expectedString = <<<EOS
NAME
    phix help - get detailed help about a specific phix command

SYNOPSIS
    phix help

IMPLEMENTATION
    This command is implemented in the PHP class:

    * Phix_Project\PhixCommands\HelpCommand

    which is defined in the file:

    * /home/stuarth/Devel/GWC/phix/src/php/Phix_Project/PhixCommands
      /HelpCommand.php

EOS;
                $this->assertEquals($expectedString, $stdoutOutput);
        }
}