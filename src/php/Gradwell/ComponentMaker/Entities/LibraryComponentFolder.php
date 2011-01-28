<?php

namespace Gradwell\ComponentMaker\Entities;

class LibraryComponentFolder extends ComponentFolder
{
        const LATEST_VERSION = 1;
        const DATA_FOLDER = '@@DATA_DIR@@/phin/php-library';

        public function createComponent()
        {
                // step 1: create the folders required
                $this->createFolders();

                // step 2: create the build file
                $this->createBuildFile();

                // step 3: create the package.xml file
                $this->createPackageXmlFile();

                // step 4: add in the doc files
                $this->createDocFiles();

                // step 5: add in config files for popular source
                // control systems
                $this->createScmIgnoreFiles();

                // step 6: don't forget the bootstrap file for
                // the unit tests
                $this->createBootstrapFile();

                // if we get here, job done
        }

        protected function createFolders()
        {
                $foldersToMake = array
                (
                        'src',
                        'src/php',
                        'src/bin',
                        'src/data',
                        'src/web',
                        'src/tests',
                        'src/tests/unit-tests',
                        'src/tests/unit-tests/bin',
                        'src/tests/unit-tests/php',
                        'src/tests/unit-tests/web',
                        'src/tests/integration-tests',
                        'src/tests/functional-tests',
                );

                foreach ($foldersToMake as $folderToMake)
                {
                        $folder = $this->folder . '/' . $folderToMake;

                        // does the folder already exist?
                        if (is_dir($folder))
                        {
                                // yes it does ... nothing needed
                                continue;
                        }

                        // no it does not ... create it
                        if (!mkdir ($folder))
                        {
                                // it all went wrong
                                throw new \Exception('unable to create folder ' . $this->folder . '/' . $folderToMake);
                        }
                }
        }

        protected function createBuildFile()
        {
                $this->copyFilesFromDataFolder(array('build.xml', 'build.properties'));
        }

        protected function createPackageXmlFile()
        {
                $this->copyFilesFromDataFolder(array('package.xml'));
        }

        protected function createDocFiles()
        {
                $this->copyFilesFromDataFolder(array('README.md', 'LICENSE.txt'));
        }

        protected function createScmIgnoreFiles()
        {
                $this->copyFilesFromDataFolder(array('.gitignore', '.hgignore'));
        }

        protected function createBootstrapFile()
        {
                $this->copyFilesFromDataFolder(array('bootstrap.php'), '/src/tests/unit-tests/');
        }
}