<?php

return [

    /**
     * The template the controller will use to render the report
     * This is used in WebController implementation of ControllerInterface@show method
     */
    "VIEW_TEMPLATE"=>env("VIEW_TEMPLATE","Zermelo::layouts.card"),

    /**
     * Middleware on the card web routes
     */
    'MIDDLEWARE' => env("MIDDLEWARE", [ "web" ]),

    /**
     * Path where the Report display.
     * This is used in implementations of ControllerInterface@show method
     * Note: the API routes are auto generated with this same URI path with the api-prefixed to the url
     * /Zermelo/(ReportName) (see config/zermelo.php for api prefix setting)
     */
    'URI_PREFIX'=>env("URI_PREFIX","ZermeloCard"),

];
