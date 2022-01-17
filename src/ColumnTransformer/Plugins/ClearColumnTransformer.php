<?php

namespace machbarmacher\GdprDump\ColumnTransformer\Plugins;

use machbarmacher\GdprDump\ColumnTransformer\ColumnTransformer;

class ClearColumnTransformer extends ColumnTransformer
{
    protected function getSupportedFormatters(): array
    {
        return ['clear'];
    }

    public function getValue($expression): string
    {
        return '';
    }
}
