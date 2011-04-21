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
 * @subpackage  PhixSwitches
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2011 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://gradwell.github.com
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\PhixSwitches;

use Phix_Project\Phix\Context;

use Gradwell\ConsoleDisplayLib\DevString;

class LongHelpSwitchTest extends \PHPUnit_Framework_TestCase
{
        public function testCanCreate()
        {
                $obj = new LongHelpSwitch();
                $this->assertTrue($obj instanceOf LongHelpSwitch);
        }

        public function testOutputsGeneralHelp()
        {
                // setup the test
                $context = new Context();
                $context->argvZero = 'phix';
                $context->phixDefinedSwitches = PhixSwitches::buildSwitches();
                $context->stdout = new DevString();
                $context->stderr = new DevString();
                $args = array ('phix', 'help');
                $argsIndex = 2;

                // perform the test
                $return = LongHelpSwitch::processBeforeCommandLoad($context, $args, $args, $argsIndex);

                // check the results
                $this->assertEquals(0, $return);

                $stdoutOutput = $context->stdout->_getOutput();
                $stderrOutput = $context->stderr->_getOutput();

                $this->assertNotEquals(0, strlen($stdoutOutput));
                $this->assertEquals(0, strlen($stderrOutput));

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

    See phix help <command> for detailed help on <command>

EOS;
                $this->assertEquals($expectedString, $stdoutOutput);
        }
}