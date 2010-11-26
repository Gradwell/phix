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
 * @subpackage  PhinExtensions
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.phin-tool.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phin_Project\PhinExtensions;
use Phin_Project\Phin\Context;

class CommandBase implements CommandInterface
{
        public function getCommandName()
        {
                throw new \Exception(__METHOD__ . '() not implemented');
        }

        public function getCommandDesc()
        {
                throw new \Exception( __METHOD__ . '() not implemented');
        }

        public function getValidOptions()
        {
                throw new \Exception( __METHOD__ . '() not implemented');
        }

        /**
         * Check to make sure that the switches and args are valid
         * 
         * @param array $parsedSwitches
         * @param array $parsedArgs
         * @param DefinedOptions $validOptions
         */
        public function parseAndValidate($argv, $argsIndex, Context $context)
        {
                throw new \Exception(__METHOD__ . '() not implemented');
        }

        public function outputShortHelp(Context $context)
        {
                $so = $context->stdout;

                $so->outputLine($context->commandStyle, $this->getCommandName());
                $so->addIndent(4);
                $so->outputLine(null, $this->getCommandDesc());
                $this->outputImplementationDetails($context);
                $so->addIndent(-4);
        }

        public function outputImplementationDetails(Context $context)
        {
                $so = $context->stdout;

                $so->outputLine($context->commentStyle, '# implemented in: ' . get_class($this));
        }
}