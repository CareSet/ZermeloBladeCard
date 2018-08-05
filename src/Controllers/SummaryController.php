<?php

namespace CareSet\ZermeloBladeCard\Controllers;

use CareSet\Zermelo\Interfaces\ControllerInterface;
use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Models\ZermeloReport;
use CareSet\ZermeloBladeCard\Generators\ReportSummaryGenerator;

class SummaryController implements ControllerInterface
{
    public function show( ZermeloReport $report )
    {
        $cache = new DatabaseCache();
        $generator = new ReportSummaryGenerator( $cache );
        return $generator->toJson( $report );
    }

    public function prefix() : string
    {
        $prefix = api_prefix()."/".config('zermelobladecard.SUMMARY_URI_PREFIX', "" );
        return $prefix;
    }
}
