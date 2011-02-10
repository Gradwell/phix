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
 * @subpackage  ConsoleDisplayLib
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.Phix-tool.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\ConsoleDisplayLib;

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

        public $target = null;
        
        /**
         * Basic escape sequence string. Use sprintf() to insert escape codes.
         *
         * @var string
         */
        private $escapeSequence = "\033[%sm";

        protected $wrapAt = 78;
        protected $indent = 0;

        // state to track the current line
        protected $currentLineLength = 0;

        public function style($codes)
        {
                if (\is_array($codes))
                {
                        return \sprintf($this->escapeSequence, \implode(';', $codes));
                }
                else
                {
                        return \sprintf($this->escapeSequence, $codes);
                }
        }

        public function resetStyle()
        {
                return \sprintf($this->escapeSequence, $this->default);
        }

        public function output($colors, $string)
        {
                $this->outputToTarget($this->target, $colors, $string);
        }

        public function outputLine($colors, $string)
        {
                $this->outputLineToTarget($this->target, $colors, $string);
        }

        public function outputBlankLine()
        {
                $this->outputBlankLineToTarget($this->target);
        }

        public function setIndent($indent)
        {
                $this->indent = $indent;
        }

        public function addIndent($indent)
        {
                $this->indent += $indent;
        }

        public function getIndent()
        {
                return $this->indent;
        }

        public function getWrapAt()
        {
                return $this->wrapAt;
        }

        public function setWrapAt($wrapAt)
        {
                $this->wrapAt = $wrapAt;
        }
        
        /**
         * Returns TRUE if $fp is a file handle that writes to a
         * real terminal.
         *
         * Returns FALSE is $fp is a file handle that writes to a normal
         * file, or a pipe (for example, the output of this program is
         * being piped into 'less').
         *
         * This is a separate method to assist with the testability of
         * this class.
         * 
         * @param resource $fp
         * @return boolean
         */
        public function isPosixTty($fp)
        {
                return \posix_isatty($fp);
        }

        protected function outputToTarget($target, $colors, $string)
        {
                $fp = \fopen($target, 'a+');
                if ($this->isPosixTty($fp))
                {
                        \fwrite($fp, $colors);
                }
                $this->writeWrappedStrings($fp, $string);
                if ($this->isPosixTty($fp))
                {
                        \fwrite($fp, $this->resetStyle());
                }
                \fclose($fp);
        }

        protected function outputLineToTarget($target, $colors, $string)
        {
                $fp = \fopen($target, 'a+');
                if ($this->isPosixTty($fp))
                {
                        \fwrite($fp, $colors);
                }
                $this->writeWrappedStrings($fp, $string);
                if ($this->isPosixTty($fp))
                {
                        \fwrite($fp, $this->resetStyle());
                }
                \fwrite($fp, \PHP_EOL);
                $this->currentLineLength = 0;
                \fclose($fp);
        }

        protected function outputBlankLineToTarget($target)
        {
                $fp = \fopen($target, 'a+');
                $string = PHP_EOL;
                if ($this->currentLineLength !== 0)
                {
                        $string .= \PHP_EOL;
                        $this->currentLineLength = 0;
                }
                \fwrite($fp, $string);
                \fclose($fp);
        }

        protected function writeWrappedStrings($fp, $string)
        {
                $strings = \explode(PHP_EOL, $string);
                $append = false;

                foreach ($strings as $string)
                {
                        if ($append)
                        {
                                \fwrite($fp, \PHP_EOL);
                                $this->currentLineLength = 0;
                        }
                        $append = true;
                        if (\strlen($string) > 0)
                        {
                                $this->writeWrappedString($fp, $string);

                        }
                }
        }

        protected function writeWrappedString($fp, $string)
        {
                // what will we split the line on?
                $separators = array(' ' => true, '\\' => true, '/' => true);

                // which characters do we wish to skip when splitting
                // the line?
                $whitespace = array(' ' => true);

                while (\strlen($string) > 0)
                {
                        // step 1: are we at the beginning of the line?
                        $this->outputLineIndent($fp);

                        // step 2: do we need to split the line?
                        if (!$this->doesStringNeedWrapping($string))
                        {
                                // no; just output and go
                                \fwrite($fp, $string);
                                $this->currentLineLength += \strlen($string);
                                $string = '';
                        }
                        else
                        {
                                // if we get here, the string needs wrapping (if possible)
                                $rawWrapPoint = $this->wrapAt - $this->currentLineLength;
                                $wrapPoint = $rawWrapPoint;
                                while ($wrapPoint > 0 && !isset($separators[$string{$wrapPoint}]))
                                {
                                        $wrapPoint--;
                                }

                                if ($wrapPoint == 0)
                                {
                                        // we will have to wrap in the middle of this
                                        // silly length string
                                        if (\strlen($string) > $this->wrapAt)
                                        {
                                                $wrapPoint = $rawWrapPoint;
                                        }
                                }

                                if ($wrapPoint > 0)
                                {
                                        \fwrite($fp, \substr($string, 0, $wrapPoint) . \PHP_EOL);
                                        if (isset($whitespace[$string{$wrapPoint}]))
                                        {
                                                $wrapPoint++;
                                        }
                                        $string = \substr($string, $wrapPoint);
                                }
                                else
                                {
                                        \fwrite($fp, PHP_EOL);
                                }
                                $this->currentLineLength = 0;
                        }
                }
        }

        protected function outputLineIndent($fp)
        {
                if ($this->currentLineLength < $this->indent)
                {
                        $indent = $this->indent - $this->currentLineLength;
                        // we need to write out the indent
                        \fwrite($fp, \str_repeat(' ', $indent));
                        $this->currentLineLength += $indent;
                }
        }

        protected function doesStringNeedWrapping($string)
        {
                if ($this->currentLineLength + \strlen($string) <= $this->wrapAt)
                {
                        return false;
                }

                return true;
        }
}