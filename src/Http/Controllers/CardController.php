<?php

namespace CareSet\ZermeloBladeCard\Http\Controllers;

use CareSet\Zermelo\Http\Requests\CardsReportRequest;
use CareSet\ZermeloBladeCard\CardPresenter;
use Illuminate\Support\Facades\Auth;

class CardController
{
    public function show( CardsReportRequest $request )
    {
        $presenter = new CardPresenter( $request->buildReport() );

        $presenter->setApiPrefix( api_prefix() );
        $presenter->setReportPath( tabular_api_prefix() );

        $user = Auth::guard()->user();
        if ( $user ) {
            $presenter->setToken( $user->getRememberToken() );
        }

        $view = config("zermelobladecard.VIEW_TEMPLATE");

        return view( $view, [ 'presenter' => $presenter ] );
    }
}
