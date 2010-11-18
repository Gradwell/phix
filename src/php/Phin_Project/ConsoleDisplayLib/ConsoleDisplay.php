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
 * @subpackage  ConsoleDisplayLib
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.phin-tool.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phin_Project\ConsoleDisplayLib;

class ConsoleDisplay
{
        public $fgGray = 30;
        public $fgBlack = 30;
        public $fgRed = 31;
        public $fgGreen = 32;
        public $fgYellow = 33;
        public $fgBlue = 34;
        public $fgMagenta = 35;
        public $fgCyan = 36;
        public $fgWhite = 37;

        public $bgGray = 40;
        public $bgBlack = 40;
        public $bgRed = 41;
        public $bgGreen = 42;
        public $bgYellow = 43;
        public $bgBlue = 44;
        public $bgMagenta = 45;
        public $bgCyan = 46;
        public $bgWhite = 47;

        public $default = 0;
        public $bold = 1;
        public $faint = 2;
        public $italic = 3;
        public $underlined = 4;
        public $doubleUnderlined = 21;
        public $normal = 22;

        /**
         * Basic escape sequence string. Use sprintf() to insert escape codes.
         *
         * @var string
         */
        private $escapeSequence = "\033[%sm";

        public function style($codes)
        {
                if (is_array($codes))
                {
                        return sprintf($this->escapeSequence, implode(';', $codes));
                }
                else
                {
                        return sprintf($this->escapeSequence, $codes);
                }
        }

        public function resetStyle()
        {
                return sprintf($this->escapeSequence, $this->default);
        }
        
        protected function outputToTarget($target, $colors, $string)
        {
                $fp = fopen($target, 'w+');
                if (\posix_isatty($fp))
                {
                        fwrite($fp, $colors);
                }
                fwrite($fp, $string);
                if (posix_isatty($fp))
                {
                        fwrite($fp, $this->resetStyle());
                }
                fclose($fp);
        }

        protected function outputLineToTarget($target, $colors, $string)
        {
                $fp = fopen($target, 'w+');
                if (\posix_isatty($fp))
                {
                        fwrite($fp, $colors);
                }
                fwrite($fp, $string);
                if (posix_isatty($fp))
                {
                        fwrite($fp, $this->resetStyle());
                }
                fwrite($fp, PHP_EOL);
                fclose($fp);
        }
}