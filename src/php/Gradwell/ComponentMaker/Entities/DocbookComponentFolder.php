<?php

namespace Gradwell\ComponentMaker\Entities;

class DocbookComponentFolder extends ComponentFolder
{
        const LATEST_VERSION = 1;
        const DATA_FOLDER = '@@DATA_DIR@@/phix/php-docbook';

        public function createComponent()
        {
                // step 1: create the folders required
                $this->createFolders();

                // step 2: create the build file
                $this->createBuildFile();
                $this->createBuildProperties();

                // step 3: add in the doc files
                $this->createDocFiles();

                // step 4: add in config files for popular source
                // control systems
                $this->createScmIgnoreFiles();

                // step 5: copy in the tools folder
                $this->createTools();

                // if we get here, job done
        }

        public function upgradeComponent()
        {
                // just make sure we're not being asked to do something
                // that is impossible
                if ($this->componentVersion >= self::LATEST_VERSION)
                {
                        throw new \Exception('Folder ' . $this->folder . ' is on version ' . $this->componentVersion . ' which is newer than known latest version ' . self::LATEST_VERSION);
                }

                // ok, let's do the upgrades
                $thisVersion = $this->componentVersion;
                while ($thisVersion < self::LATEST_VERSION)
                {
                        $method = 'upgradeFrom' . $thisVersion . 'To' . ($thisVersion + 1);
                        \call_user_method($method, $this);
                        $thisVersion++;
                        $this->editBuildPropertiesVersionNumber($thisVersion);
                }

                // all done
        }

        protected function createFolders()
        {
                $foldersToMake = array
                (
                        'src',
                        'src/figures',
                        'tools',
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
                $this->copyFilesFromDataFolder(array('build.xml'));
        }

        protected function createBuildProperties()
        {
                $this->copyFilesFromDataFolder(array('build.properties'));
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

        protected function createTools()
        {
                $this->replaceFolderContentsFromDataFolder('tools', 'tools');
        }
}