<?php

namespace CareSet\ZermeloBladeCard\Controllers;

use CareSet\Zermelo\Interfaces\ControllerInterface;
use CareSet\ZermeloBladeCard\Generators\ReportGenerator;
use CareSet\Zermelo\Models\DatabaseCache;
use CareSet\Zermelo\Models\ZermeloReport;
use CareSet\ZermeloBladeCard\Models\CardPresenter;
use DB;

class ApiController implements ControllerInterface
{
    public function show( ZermeloReport $report )
    {
        $presenter = new CardPresenter( $report );
	$api_prefix = trim( config("zermelo.URI_API_PREFIX"), "/ " );
        $presenter->setApiPrefix( $api_prefix );
        $presenter->setReportPath( config('zermelobladecard.TABULAR_URI_PREFIX') );
        $presenter->setSummaryPath( config('zermelobladecard.SUMMARY_URI_PREFIX') );
        $cache = new DatabaseCache( $report );
        $generator = new ReportGenerator( $cache );
        return $generator->toJson( $report );
    }

    public function prefix() : string
    {
	$api_prefix = trim( config("zermelo.URI_API_PREFIX"), "/ " );
        $prefix = $api_prefix."/".config('zermelobladecard.TABULAR_URI_PREFIX', "" );
        return $prefix;
    }
}
