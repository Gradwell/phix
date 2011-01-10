<?php

namespace Gradwell\ComponentMaker\PhinCommands;

use Phin_Project\Phin\CommandsList;
use Phin_Project\Phin\Context;
use Phin_Project\PhinExtensions\CommandBase;
use Phin_Project\CommandLineLib\DefinedSwitches;
use Phin_Project\CommandLineLib\DefinedSwitch;
use Phin_Project\CommandLineLib\CommandLineParser;
use Phin_Project\ValidationLib\MustBeValidFile;
use Phin_Project\ValidationLib\MustBeValidPath;
use Phin_Project\ValidationLib\MustBeWriteable;

use Gradwell\ComponentMaker\Entities\LibraryComponentFolder;

class BuildPackageXml extends CommandBase
{
        public function getCommandName()
        {
                return 'package-xml:build';
        }

        public function getCommandDesc()
        {
                return 'expand the tokens and contents of the PEAR-compatible package.xml file';
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

                $options->addSwitch('srcFolder', 'specify the src folder to feed into package.xml')
                        ->setWithShortSwitch('s')
                        ->setWithLongSwitch('src')
                        ->setWithRequiredArg('<folder>', 'the path to the folder where the package source files are')
                        ->setArgHasDefaultValueOf('src')
                        ->setArgValidator(new MustBeValidPath());

                return $options;
        }

        public function validateAndExecute($args, $argsIndex, Context $context)
        {
                $so = $context->stdout;
                $se = $context->stderr;

                // step 1: parse the options
                $options  = $this->getCommandOptions();
                $parser   = new CommandLineParser();
                list($ParsedSwitches, $argsIndex) = $parser->parseSwitches($args, $argsIndex, $options);

                // step 2: verify the args
                $errors = $ParsedSwitches->validateSwitchValues();
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
                $buildPropertiesFile = $ParsedSwitches->getFirstArgForSwitch('properties');
                $packageXmlFile      = $ParsedSwitches->getFirstArgForSwitch('packageXml');
                $srcFolder           = $ParsedSwitches->getFirstArgForSwitch('srcFolder');
        }
}