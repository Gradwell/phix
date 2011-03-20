#!/usr/bin/env php
<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'HTMLFilterIterator.php';

function webifyDirectory($properties, $directory, $edition)
{
    $toc = getSubstring(
      file_get_contents($directory . DIRECTORY_SEPARATOR . 'index.html'),
      '<dl>',
      '</dl>',
      TRUE,
      TRUE,
      TRUE
    );

    $_editions = '';
    $editions  = $properties['editions'];

    foreach ($editions as $language => $versions) {
        foreach ($versions as $version) {
            if ($language . '-' . $version == $edition) {
                $active = ' class="active"';
            } else {
                $active = '';
            }

            switch ($language) {
                case 'en': {
                    $_language = 'English';
                }
                break;

                case 'ja': {
                    $_language = 'Japanese';
                }
                break;

                case 'de': {
                    $_language = 'German';
                }
                break;
            }

            $_editions .= sprintf(
              '<li><a href="../%s-%s/index.html"%s>%s %s <span><small>%s</small></span></a></li>',
              $version,
	      $language,
              $active,
	      $properties['longName'],
              $version,
              $_language
            );
        }
    }

    foreach (new HTMLFilterIterator(new DirectoryIterator($directory)) as $file) {
        webifyFile($file->getPathName(), $toc, $_editions);
    }
}

function webifyFile($file, $toc, $editions)
{
    $filename = basename($file);

    if (strpos('-book-', $filename)) {
        return;
    }

    $toc = str_replace(
      '<a href="' . $filename . '">',
      '<a href="' . $filename . '" class="active">',
      $toc
    );

    $template = file_get_contents(
      dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'page.html'
    );

    $title   = '';
    $content = '';
    $prev    = '';
    $next    = '';

    if ($filename !== 'index.html') {
        if (strpos($filename, 'appendixes') === 0) {
            $type = 'appendix';
        }

        else if (strpos($filename, 'preface') === 0) {
            $type = 'preface';
	}

	else if (strpos($filename, 'pt') === 0) {
	    $type = 'part';
	}

        else {
            $type = 'chapter';
        }

        $buffer  = file_get_contents($file);
        $title   = getSubstring($buffer, '<title>', '</title>', FALSE, FALSE);
        $content = getSubstring($buffer, '<div class="' . $type . '"', '<div class="navfooter">', TRUE, FALSE);
        $prev    = getSubstring($buffer, '<link rel="prev" href="', '" title', FALSE, FALSE);
        $next    = getSubstring($buffer, '<link rel="next" href="', '" title', FALSE, FALSE);

        if (!empty($prev)) {
            $prev = '<a accesskey="p" href="' . $prev . '">Prev</a>';
        }

        if (!empty($next)) {
            $next = '<a accesskey="n" href="' . $next . '">Next</a>';
        }
    }
    else
    {
        $buffer  = file_get_contents($file);
        $title   = getSubstring($buffer, '<title>', '</title>', FALSE, FALSE);
        // $content = getSubstring($buffer, '<div class="titlepage"', '<div class="navfooter">', TRUE, FALSE);
        $content = getSubstring($buffer, '<div class="titlepage"', '<hr>', TRUE, FALSE) . "\n</div>\n";
        $prev    = '';
        $next    = getSubstring($buffer, '<link rel="next" href="', '" title', FALSE, FALSE);

        if (!empty($next)) {
            $next = '<a accesskey="n" href="' . $next . '">Next</a>';
        }
    }

    $buffer =  str_replace(
      array('{title}', '{content}', '{toc}', '{editions}', '{prev}', '{next}'),
      array($title, $content, $toc, $editions, $prev, $next),
      $template
    );

    if (function_exists('tidy_repair_string')) {
        $buffer = tidy_repair_string(
          $buffer,
          array(
            'indent'       => TRUE,
            'output-xhtml' => TRUE,
            'wrap'         => 0
          ),
          'utf8'
        );
    }

    file_put_contents($file, $buffer);
}

function getSubstring($buffer, $start, $end, $includeStart = TRUE, $includeEnd = TRUE, $strrpos = FALSE)
{
    if ($includeStart) {
        $prefix = 0;
    } else {
        $prefix = strlen($start);
    }

    if ($includeEnd) {
        $suffix = strlen($end);
    } else {
        $suffix = 0;
    }

    $start = strpos($buffer, $start);

    if ($strrpos) {
        $_end = strrpos($buffer, $end);
    } else {
        $_end = strpos($buffer, $end, $start);
    }

    if ($start !== FALSE) {
        return substr(
          $buffer,
          $start + $prefix,
          $_end - ($start + $prefix) + $suffix
        );
    } else {
        return '';
    }
}

function loadProperties($buildPropertiesFilename)
{
	$return = array();

	$buildProperties = parse_ini_file($buildPropertiesFilename);
	$return['longName'] = $buildProperties['docbook.longName'];

	$editions = explode(',', $buildProperties['docbook.editions']);
	foreach ($editions as $edition)
	{
		$parts = explode('-', $edition);
		$version = $parts[0];
		$lang    = $parts[1];

		$return['editions'][$lang][] = $version;
	}

	return $return;
}

$properties = loadProperties($argv[1]);
webifyDirectory($properties, $argv[2], $argv[3]);
