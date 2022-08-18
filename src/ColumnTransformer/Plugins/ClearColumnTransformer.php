<?php

namespace druidfi\GdprDump\ColumnTransformer\Plugins;

use druidfi\GdprDump\ColumnTransformer\ColumnTransformer;

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
