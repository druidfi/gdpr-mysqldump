<?php

namespace druidfi\GdprDump;

use Druidfi\Mysqldump\Mysqldump;
use druidfi\GdprDump\ColumnTransformer\ColumnTransformer;

class MysqldumpGdpr extends Mysqldump
{
    /** @var array [string][string]string */
    protected array $gdprExpressions = [];

    /** @var array [string][string]string */
    protected array $gdprReplacements = [];

    protected bool $debugSql = false;

    public function __construct($dsn = '', $user = '', $pass = '', array $dumpSettings = [], array $pdoSettings = [])
    {
        if (array_key_exists('gdpr-expressions', $dumpSettings)) {
            $this->gdprExpressions = $dumpSettings['gdpr-expressions'];
            unset($dumpSettings['gdpr-expressions']);
        }

        if (array_key_exists('gdpr-replacements', $dumpSettings)) {
            $this->gdprReplacements = $dumpSettings['gdpr-replacements'];
            unset($dumpSettings['gdpr-replacements']);
        }

        if (array_key_exists('debug-sql', $dumpSettings)) {
            $this->debugSql = $dumpSettings['debug-sql'];
            unset($dumpSettings['debug-sql']);
        }

        $this->setTransformTableRowHook([$this, 'hookTransformRow']);

        parent::__construct($dsn, $user, $pass, $dumpSettings, $pdoSettings);
    }

    public function getColumnStmt($tableName): array
    {
        $columnStmt = parent::getColumnStmt($tableName);

        if (!empty($this->gdprExpressions[$tableName])) {
            $columnTypes = $this->tableColumnTypes()[$tableName];

            foreach (array_keys($columnTypes) as $i => $columnName) {
                if (!empty($this->gdprExpressions[$tableName][$columnName])) {
                    $expression = $this->gdprExpressions[$tableName][$columnName];
                    $columnStmt[$i] = "$expression as $columnName";
                }
            }

            if ($this->debugSql) {
                print "/* SELECT " . implode(",", $columnStmt) . " FROM `$tableName` */\n\n";
            }
        }

        return $columnStmt;
    }

    protected function hookTransformRow($tableName, array $row): array
    {
        foreach ($row as $colName => &$colValue) {
            if (!empty($this->gdprReplacements[$tableName][$colName])) {
                $replacement = ColumnTransformer::replaceValue($tableName, $colName, $this->gdprReplacements[$tableName][$colName]);

                if ($replacement !== false) {
                    $colValue = $replacement;
                }
            }
        }

        return $row;
    }
}
