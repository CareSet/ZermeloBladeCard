<?php

namespace CareSet\ZermeloBladeCard\Controllers;

use CareSet\Zermelo\Interfaces\ControllerInterface;
use CareSet\Zermelo\Models\ZermeloReport;
use CareSet\ZermeloBladeCard\Models\CardPresenter;
use DB;
use Illuminate\Support\Facades\Auth;

class WebController implements ControllerInterface
{
    public function show( ZermeloReport $report )
    {
        $presenter = new CardPresenter( $report );

	$api_prefix = trim( config("zermelo.URI_API_PREFIX"), "/ " );
        $presenter->setApiPrefix( $api_prefix );
        $presenter->setReportPath( config('zermelobladecard.TABULAR_URI_PREFIX', '') );
        $presenter->setSummaryPath( config('zermelobladecard.SUMMARY_URI_PREFIX', '') );

        $user = Auth::guard()->user();
        if ( $user ) {
            $presenter->setToken( $user->last_token );
        }

        $view = $presenter->getReportView();
        if ( !$view ) {
            $view = config("zermelobladecard.TABULAR_VIEW_TEMPLATE");
        }

        return view( $view, [ 'presenter' => $presenter ] );
    }

    public function prefix() : string
    {
        $prefix = config('zermelobladecard.TABULAR_URI_PREFIX', "" );
        return $prefix;
    }
}
