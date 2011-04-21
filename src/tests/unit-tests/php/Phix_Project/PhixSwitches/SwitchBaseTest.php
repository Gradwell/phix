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

namespace Phix_Project\PhixSwitches;

use Phix_Project\Phix\Context;

class SwitchBaseTest extends \PHPUnit_Framework_TestCase
{
        public function testSupportsBeforeCommandLoad()
        {
                // setup
                $context = new Context();
                $args    = array ('phix', 'test', 'anotherSwitch');
                $argsIndex = 2;

                // do the test
                $cloneContext = clone $context;
                $cloneArgs    = $args;
                $return = SwitchBase::processBeforeCommandLoad($cloneContext, $cloneArgs);

                // make sure nothing happened
                $this->assertTrue(is_null($return));
                $this->assertEquals($context, $cloneContext);
                $this->assertEquals($args, $cloneArgs);
        }

        public function testSupportsAfterCommandLoad()
        {
                // setup
                $context = new Context();
                $args    = array ('phix', 'test', 'anotherSwitch');

                // do the test
                $cloneContext = clone $context;
                $cloneArgs    = $args;
                $return = SwitchBase::processAfterCommandLoad($cloneContext, $cloneArgs);

                // make sure nothing happened
                $this->assertTrue(is_null($return));
                $this->assertEquals($context, $cloneContext);
                $this->assertEquals($args, $cloneArgs);
        }
}