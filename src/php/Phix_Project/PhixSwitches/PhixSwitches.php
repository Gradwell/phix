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
 * @subpackage  PhixSwitches
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://gradwell.github.com
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\PhixSwitches;
use Gradwell\CommandLineLib\DefinedSwitches;
use Gradwell\ValidationLib\MustBeValidPath;

class PhixSwitches
{
        static public function buildSwitches(DefinedSwitches $switches)
        {
                // Phix -h || Phix -?
                $switches->addSwitch('shortHelp', 'display a summary of the command-line structure')
                         ->setWithShortSwitch('h')
                         ->setWithShortSwitch('?');

                // Phix --help || Phix --?
                $switches->addSwitch('longHelp', 'display a full list of supported commands')
                         ->setWithLongSwitch('help')
                         ->setWithLongSwitch('?');
                
                // Phix -v || Phix --version
                $switches->addSwitch('version', 'display Phix version number')
                         ->setWithShortSwitch('v')
                         ->setWithLongSwitch('version');

                // Phix -d || Phix --debug
                $switches->addSwitch('debug', 'enable debugging output')
                         ->setWithShortSwitch('d')
                         ->setWithLongSwitch('debug');

                // Phix -I<path> || Phix --include=<path>
                $switches->addSwitch('include', 'add a folder to load commands from')
                         ->setLongDesc("Phix finds all of its commands by searching PHP's include_path for PHP files in "
                                        . "folders called 'PhixCommands'. If you want to Phix to look in other folders "
                                        . "without having to add them to PHP's include_path, use --include to tell Phix "
                                        . "to look in these folders."
                                        . \PHP_EOL . \PHP_EOL
                                        . "Phix expects '<path>' to point to a folder that conforms to the PSR0 standard "
                                        . "for autoloaders."
                                        . \PHP_EOL . \PHP_EOL
                                        . "For example, if your command is the class '\Me\Tools\PhixCommands\ScheduledTask', Phix would "
                                        . "expect to autoload this class from the 'Me/Tools/PhixCommands/ScheduledTask.php' file."
                                        . \PHP_EOL . \PHP_EOL
                                        . "If your class lives in the './myApp/lib/Me/Tools/PhixCommands' folder, you would call Phix "
                                        . "with 'Phix --include=./myApp/lib'")
                         ->setWithShortSwitch('I')
                         ->setWithLongSwitch('include')
                         ->setWithRequiredArg('<path>', 'The path to the folder to include')
                         ->setArgValidator(new MustBeValidPath())
                         ->setSwitchIsRepeatable();
        }
}