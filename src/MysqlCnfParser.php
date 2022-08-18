<?php

namespace druidfi\GdprDump;

use Matomo\Ini\IniReader;
use Symfony\Component\Filesystem\Path;

class MysqlCnfParser
{
    protected array $extensions = ['cnf', 'ini'];
    protected array $processedFiles = [];

    public static function parse($filename): array
    {
        return (new self())->parseIniFile($filename);
    }

    public function parseIniFile($filename): array
    {
        if (is_file($filename) && is_readable($filename)) {
            $this->markFileProcessed($filename);
            $contents = file_get_contents($filename);
            $contentArray = explode("\n", $contents);

            // Go through the file and pop any "!include/!includedir" directives
            $reader = new IniReader();
            $toParse = [];
            $includes = [];

            foreach ($contentArray as $line) {
                if (strpos(trim($line), "!include") === 0) {
                    $includes[] = $line;
                } elseif (strlen($line) > 0 && !in_array($line[0], ["!", "#"])) {
                    // Ignore comments
                    $toParse[] = $line;
                }
            }

            return array_merge_recursive(
                $reader->readString(implode("\n", $toParse)),
                $this->processIncludes($includes, Path::getDirectory($filename))
            );
        } else {
            return [];
        }
    }

    protected function processIncludes(Array $includes, $includePath): array
    {
        $return = [];

        foreach ($includes as $include) {
            // Strip any !include(s).
            $names = explode(" ", $include);
            $fileName = $names[1];
            $includeType = $names[0];
            $fileName = Path::makeAbsolute($fileName, $includePath);

            if (!$this->hasFileBeenProcessed($fileName)) {
                $this->markFileProcessed($fileName);
                $return = array_merge_recursive($return,
                    $includeType == "!includedir" ? $this->parseDirectory($fileName) : $this->parseIniFile($fileName)
                );
            }
        }

        return $return;
    }

    protected function parseDirectory($directoryName): array
    {
        $return = [];
        $files = [];

        foreach ($this->extensions as $extension) {
            $glob = sprintf('%s/*.%s', $directoryName, $extension);
            $files = array_merge($files, glob($glob));
        }

        foreach ($files as $file) {
            $return = array_merge_recursive($return, $this->parseIniFile($file));
        }

        return $return;
    }

    protected function hasFileBeenProcessed($fileName): bool
    {
        return in_array(Path::canonicalize($fileName), $this->processedFiles);
    }

    protected function markFileProcessed($fileName)
    {
        $canonicalFileName = Path::canonicalize($fileName);
        $this->processedFiles[$canonicalFileName] = $canonicalFileName;
    }
}
