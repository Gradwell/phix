<?php

namespace Gradwell\ComponentMaker\PhinCommands;

use Phin_Project\Phin\CommandsList;
use Phin_Project\Phin\Context;
use Phin_Project\PhinExtensions\CommandBase;
use Phin_Project\PhinExtensions\CommandInterface;
use Phin_Project\CommandLineLib\DefinedSwitches;
use Phin_Project\CommandLineLib\DefinedSwitch;
use Phin_Project\CommandLineLib\CommandLineParser;
use Phin_Project\ValidationLib\MustBeValidFile;
use Phin_Project\ValidationLib\MustBeValidPath;
use Phin_Project\ValidationLib\MustBeWriteable;

use Gradwell\ComponentMaker\Entities\LibraryComponentFolder;

if (!class_exists('Gradwell\ComponentMaker\PhinCommands\ExpandPackageXml'))
{
class ExpandPackageXml extends CommandBase implements CommandInterface
{
        public function getCommandName()
        {
                return 'pear:expand-package-xml';
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
                $srcFolder           = $parsedSwitches->getFirstArgForSwitch('srcFolder');

                // step 4: let's get it on
                return $this->populatePackageXmlFile($context, $buildPropertiesFile, $packageXmlFile, $srcFolder);
        }

        protected function populatePackageXmlFile(Context $context, $buildPropertiesFile, $packageXmlFile, $srcFolder)
        {
                // load the files we are going to manipulate
                $rawBuildProperties = $this->loadBuildProperties($context, $buildPropertiesFile);
                $rawXml = $this->loadPackageXmlFile($context, $packageXmlFile);

                // translate the raw properties into the tokens we support
                $buildProperties = array();
                foreach ($rawBuildProperties as $name => $value)
                {
                        $buildProperties['${' . $name . '}'] = $value;
                }
                $buildProperties['${build.date}'] = date('Y-m-d');
                $buildProperties['${build.time}'] = date('H:i:s');

                // generate a list of the files to add
                $buildProperties['${contents}']   = $this->calculateFilesList($context, $srcFolder);

                // do the replacement
                $newXml = str_replace(array_keys($buildProperties), $buildProperties, $rawXml);

                // write out the new file
                file_put_contents($packageXmlFile, $newXml);

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

        protected function calculateFilesList(Context $context, $srcFolder)
        {
                $return = '';

                $roles = array(
                        'bin'   => 'script',
                        'data'  => 'data',
                        'doc'   => 'doc',
                        'php'   => 'php',
                        'tests/unit-tests/php' => 'test',
                        'www'   => 'www'
                );

                foreach ($roles as $dir => $role)
                {
                        $searchFolder = $srcFolder . DIRECTORY_SEPARATOR . $dir;

                        // do we have the folder in this project?
                        if (!is_dir($searchFolder))
                        {
                                // no we do not - bail
                                continue;
                        }

                        // yes we do - what is inside?
                        $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($searchFolder));
                        foreach ($objects as $name => $direntry)
                        {
                                // skip all directories
                                if ($direntry->isDir())
                                {
                                        continue;
                                }

                                $filename = \str_replace($searchFolder, '', $direntry->getPathname());
                                $md5sum   = \md5(file_get_contents($direntry->getPathname()));
				$return .= '      <file baseinstalldir="/" md5sum="' . $md5sum . '" name="' . $filename . '" role="' . $role . '"';

				// do we need tasks for this file?
				switch($role)
				{
					case 'php':
                                        case 'test':
						// do something here
						$return .= ">\n"
							. '        <tasks:replace from="@@PACKAGE_VERSION@@" to="version" type="package-info" />' . "\n"
							. '        <tasks:replace from="@@PHP_DIR@@" to="php_dir" type="pear-config" />' . "\n"
							. "      </file>\n";
						break;

					case 'script':
						// do something here
						$return .= ">\n"
							. '        <tasks:replace from="@@PACKAGE_VERSION@@" to="version" type="package-info" />' . "\n"
							. '        <tasks:replace from="/usr/bin/env php" to="php_bin" type="pear-config" />' . "\n"
							. '        <tasks:replace from="@@PHP_BIN@@" to="php_bin" type="pear-config" />' . "\n"
							. '        <tasks:replace from="@@BIN_DIR@@" to="bin_dir" type="pear-config" />' . "\n"
							. '        <tasks:replace from="@@PHP_DIR@@" to="php_dir" type="pear-config" />' . "\n"
							. "      </file>\n";
						break;

					default:
						$return .= "/>\n";
				}
                        }
                }

                return $return;
        }
}
}