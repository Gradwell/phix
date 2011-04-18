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

class ShortHelpSwitchTest extends \PHPUnit_Framework_TestCase
{
        public function testCanCreate()
        {
                $obj = new ShortHelpSwitch();
                $this->assertTrue($obj instanceof ShortHelpSwitch);
        }

        public function testOutputsHelpMessage()
        {
                // setup the test
                $context = new Context();
                $context->stdout = new DevString();
                $context->stderr = new DevString();
                $args = array ('phix', 'h');
                $argsIndex = 2;

                // pre-conditions?
                $this->assertEquals(0, strlen($context->stdout->_getOutput()));

                // do the test
                $returnCode    = ShortHelpSwitch::processBeforeExtensionLoad($context, $args, $args, $argsIndex);
                $writtenString = $context->stdout->_getOutput();

                // did it work?
                $this->assertTrue(strlen($writtenString) > 0);
        }
}