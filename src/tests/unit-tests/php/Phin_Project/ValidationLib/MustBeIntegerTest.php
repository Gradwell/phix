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
 * @subpackage  ValidationLib
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.phin-tool.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phin_Project\ValidationLib;

class MustBeIntegerTest extends ValidationLibTestBase
{
        /**
         *
         * @return MustBeInteger 
         */
        protected function setupObj()
        {
                // setup the test
                $obj = new MustBeInteger();
                $messages = $obj->getMessages();
                $this->assertTrue(is_array($messages));
                $this->assertEquals(0, count($messages));

                return $obj;
        }

        public function testCorrectlyDetectsStrings()
        {
                $obj = $this->setupObj();
                $this->doTestIsValid($obj, '0');
                $this->doTestIsNotValid($obj, 'fred', array("'fred' (of type string) is not a valid integer"));
        }

        public function testCorrectlyDetectsNulls()
        {
                $obj = $this->setupObj();
                $this->doTestIsNotValid($obj, null, array("'' (of type NULL) is not a valid integer"));
        }

        public function testCorrectlyDetectsIntegers()
        {
                $obj = $this->setupObj();
                $this->doTestIsValid($obj, 0);
                $this->doTestIsValid($obj, 1);
                $this->doTestIsValid($obj, -1);
        }

        public function testCorrectlyDetectsFloats()
        {
                $obj = $this->setupObj();
                $this->doTestIsNotValid($obj, 3.145297, array("'3.145297' (of type double) is not a valid integer"));
        }

        public function testCorrectlyDetectsObjects()
        {
                $obj = $this->setupObj();
                $this->doTestIsNotValid($obj, $obj, array("'Phin_Project\ValidationLib\MustBeInteger' (of type object) is not a valid integer"));
        }

        public function testCorrectlyDetectsResources()
        {
                $obj = $this->setupObj();
                $res = fopen('php://input', 'r');
                $this->doTestIsNotValid($obj, $res, array("'' (of type resource) is not a valid integer"));
        }

        public function testCorrectlyDetectsBooleans()
        {
                $obj = $this->setupObj();
                $this->doTestIsNotValid($obj, true, array("'TRUE' (of type boolean) is not a valid integer"));
                $this->doTestIsNotValid($obj, false, array("'FALSE' (of type boolean) is not a valid integer"));
        }

        public function testCorrectlyDetectsClosures()
        {
                $obj = $this->setupObj();
                $func = function() { return true; };

                $this->doTestIsNotValid($obj, $func, array("'Closure' (of type object) is not a valid integer"));
        }

        public function testCorrectlyDetectsArrays()
        {
                $obj = $this->setupObj();
                $arr = array (1,2,3,4,5,6,7,8,9,10);

                $this->doTestIsNotValid($obj, $arr, array ("'' (of type array) is not a valid integer"));
        }
}
