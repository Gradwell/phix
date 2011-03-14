phix
====

phix is a general-purpose, easily-extensible command-line tool for PHP. It has the advantage of being framework-agnostic, allowing you to create your own command-line tools today that will not break when you upgrade or switch frameworks.

System-Wide Installation
------------------------

phix should be installed using the [PEAR Installer](http://pear.php.net). This installer is the community's de-facto standard for distributing PHP components.

    sudo pear channel-discover pear.gradwell.com
    sudo pear install --alldeps Gradwell/phix

After installation, you will find phix inside your local PEAR repository, which on Linux systems is normally /usr/share/php.

Documentation
-------------

phix is self-documenting; use 'phix help' to get started.

Development Environment
-----------------------

If you want to patch or enhance this component, you will need to create a suitable development environment.  All components created by phix

    #phpunit
    sudo pear channel-discover pear.phpunit.de
    sudo pear channel-discover components.ez.no
    sudo pear channel-discover pear.symfony-project.com
    sudo pear install --alldeps phpunit/PHPUnit

    # phing
    sudo pear channel-discover pear.phing.info
    sudo pear install --alldeps phing/phing

    # pdepend
    sudo pear channel-discover pear.pdepend.org
    sudo pear install --alldeps pdepend/PHP_Depend-beta

    # phpdoc
    sudo pear install --alldeps pear/PhpDocumentor

    # phpmd
    sudo apt-get install php5-imagick
    sudo pear channel-discover pear.phpmd.org
    sudo pear install --alldeps phpmd/PHP_PMD-alpha

    # phpcpd
    sudo pear install --alldeps phpunit/phpcpd

    # phpcs
    sudo pear install --alldeps pear/PHP_CodeSniffer-beta

    # phpcb
    sudo pear install --alldeps phpunit/PHP_CodeBrowser

    # phix
    sudo pear channel-discover pear.gradwell.com
    sudo pear install --alldeps Gradwell/phix

You can then clone the git repository:

    # phix
    git clone git://github.com/Gradwell/phix.git
