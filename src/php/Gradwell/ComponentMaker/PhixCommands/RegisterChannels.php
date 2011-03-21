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
 * @package     Gradwell
 * @subpackage  ComponentMaker
 * @author      Stuart Herbert <stuart.herbert@gradwell.com>
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://gradwell.github.com
 * @version     @@PACKAGE_VERSION@@
 */

namespace Gradwell\ComponentMaker\PhixCommands;

use Phix_Project\Phix\CommandsList;
use Phix_Project\Phix\Context;
use Phix_Project\PhixExtensions\CommandBase;
use Phix_Project\PhixExtensions\CommandInterface;
use Gradwell\CommandLineLib\DefinedSwitches;
use Gradwell\CommandLineLib\DefinedSwitch;
use Gradwell\CommandLineLib\CommandLineParser;
use Gradwell\ValidationLib\MustBeValidFile;
use Gradwell\ValidationLib\MustBeValidPath;
use Gradwell\ValidationLib\MustBeWriteable;

use Gradwell\ComponentMaker\Entities\LibraryComponentFolder;

if (!class_exists('Gradwell\ComponentMaker\PhixCommands\RegisterChannels'))
{
class RegisterChannels extends CommandBase implements CommandInterface
{
        public function getCommandName()
        {
                return 'pear:register-channels';
        }

        public function getCommandDesc()
        {
                return 'register the channels for the dependencies listed in the PEAR-compatible package.xml file';
        }

        public function getCommandOptions()
        {
                $options = new DefinedSwitches();

                $options->addSwitch('properties', 'specify the build.properties file to use')
                        ->setWithShortSwitch('b')
                        ->setWithLongSwitch('build.properties')
                        ->setWithRequiredArg('<build.properties>', 'the path to the build.properties file to use')
                        ->setArgHasDefaultValueOf('build.properties')
                        ->setArgValidator(new MustBeValidFile());

                $options->addSwitch('packageXml', 'specify the package.xml file to expand')
                        ->setWithShortSwitch('p')
                        ->setWithLongSwitch('packageXml')
                        ->setwithRequiredArg('<package.xml>', 'the path to the package.xml file to use')
                        ->setArgHasDefaultValueOf('.build/package.xml')
                        ->setArgValidator(new MustBeValidFile())
                        ->setArgValidator(new MustBeWriteable());

                $options->addSwitch('pearConfig', 'specify the PEAR config to register the channels with')
                        ->setWithShortSwitch('P')
                        ->setWithLongSwitch('pear-config')
                        ->setWithRequiredArg('<pear-config>', 'the PEAR config file to use')
                        ->setArgHasDefaultValueOf('.tmp/pear-config')
                        ->setArgValidator(new MustBeValidFile());

                return $options;
        }

        public function validateAndExecute($args, $argsIndex, Context $context)
        {
                $so = $context->stdout;
                $se = $context->stderr;

                // step 1: parse the options
                $options  = $this->getCommandOptions();
                $parser   = new CommandLineParser();
                list($parsedSwitches, $argsIndex) = $parser->parseSwitches($args, $argsIndex, $options);

                // step 2: verify the args
                $errors = $parsedSwitches->validateSwitchValues();
                if (count($errors) > 0)
                {
                        // validation failed
                        foreach ($errors as $errorMsg)
                        {
                                $se->output($context->errorStyle, $context->errorPrefix);
                                $se->outputLine(null, $errorMsg);
                        }

                        // return the error code to the caller
                        return 1;
                }

                // step 3: extract the values we need to carry on
                // var_dump($parsedSwitches);

                $buildPropertiesFile = $parsedSwitches->getFirstArgForSwitch('properties');
                $packageXmlFile      = $parsedSwitches->getFirstArgForSwitch('packageXml');
                $pearConfig          = $parsedSwitches->getFirstArgForSwitch('pearConfig');

                // step 4: let's get it on
                return $this->populatePackageXmlFile($context, $buildPropertiesFile, $packageXmlFile, $pearConfig);
        }

        protected function populatePackageXmlFile(Context $context, $buildPropertiesFile, $packageXmlFile, $pearConfig)
        {
                // load the files we are going to manipulate
                $rawBuildProperties = $this->loadBuildProperties($context, $buildPropertiesFile);
                $rawXml = $this->loadPackageXmlFile($context, $packageXmlFile);

                $channels = $this->extractChannels($context, $rawXml);
                $this->doRegisterChannels($context, $pearConfig, $channels);
                
                // all done
                return 0;
        }

        protected function loadBuildProperties(Context $context, $buildPropertiesFile)
        {
                // @TODO: error handling
                return parse_ini_file($buildPropertiesFile);
        }

        protected function loadPackageXmlFile(Context $context, $packageXmlFile)
        {
                // @TODO: error handling
                return file_get_contents($packageXmlFile);
        }

        protected function extractChannels(Context $context, $rawXml)
        {
                $channels = array();

                $xml = simplexml_load_string($rawXml);

                foreach ($xml->dependencies->required->package as $package)
                {
                        // skip over any non-pear packages
                        if (!isset($package->channel))
                        {
                                continue;
                        }

                        $channel = (string) $package->channel;

                        if (!isset($channels[$channel]))
                        {
                                $rawChannelXml = file_get_contents('http://' . $channel . '/channel.xml');
                                $channelXml = simplexml_load_string($rawChannelXml);
                                $channels[$channel] = (string) $channelXml->suggestedalias;
                        }
                }

                return $channels;
        }

        protected function doRegisterChannels(Context $context, $pearConfig, $channels)
        {
                $so = $context->stdout;
                $se = $context->stderr;

                foreach ($channels as $channel => $alias)
                {
                        $cmd = "pear -c " . $pearConfig . " channel-discover " . $channel;
                        $so->outputLine(null, "Registering PEAR channel " . $channel);
                        system($cmd);
                }
        }
}
}