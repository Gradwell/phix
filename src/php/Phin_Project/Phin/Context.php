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
 * @subpackage  Phin
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.phin-tool.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phin_Project\Phin;

use Phin_Project\ConsoleDisplayLib\StdOut;
use Phin_Project\ConsoleDisplayLib\StdErr;

class Context
{
        /**
         *
         * @var StdOut
         */
        public $stdout = null;

        /**
         *
         * @var StdErr
         */
        public $stderr = null;

        public $phinDefinedOptions = null;
        public $searchPaths = array();
        public $commands = null;

        public $version = '@@PACKAGE_VERSION@@';

        public $highlightStyle = null;
        public $switchStyle = null;
        public $argStyle = null;
        public $errorStyle = null;
        public $exampleStyle = null;
        public $urlStyle = null;

        /**
         *
         * @var int
         */
        public $debugLevel = 0;

        public function __construct()
        {
                $this->stdout = new Stdout;
                $this->stderr = new Stderr;

                $this->setupStyles();
        }

        public function addSearchPath($path)
        {
                $this->searchPaths[] = $path;
        }

        protected function setupStyles()
        {
                $so = $this->stdout;
                $this->highlightStyle = $so->style(array($so->bold, $so->fgGreen));
                $this->commandStyle = $so->style(array($so->bold, $so->fgGreen));
                $this->switchStyle = $so->style(array($so->bold, $so->fgYellow));
                $this->argStyle = $so->style(array($so->bold, $so->fgBlue));
                $this->errorStyle = $so->style(array($so->bold, $so->fgRed));
                $this->exampleStyle = $so->style(array($so->bold, $so->fgYellow));
                $this->urlStyle = $so->style(array($so->fgBlue, $so->bold));
        }
}