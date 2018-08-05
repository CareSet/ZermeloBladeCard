<?php

namespace CareSet\ZermeloBladeCard;

use CareSet\Zermelo\Models\AbstractZermeloProvider;
use CareSet\ZermeloBladeCard\Console\ZermeloBladeCardInstallCommand;
use CareSet\ZermeloBladeCard\Controllers\ApiController;
use CareSet\ZermeloBladeCard\Controllers\SummaryController;
use CareSet\ZermeloBladeCard\Controllers\WebController;


Class ServiceProvider extends AbstractZermeloProvider
{

    protected $controllers = [
        ApiController::class,
        SummaryController::class,
        WebController::class
    ];

    public function boot(\Illuminate\Routing\Router $router)
	{

        /*
         * Register our zermelo view make command which:
         *  - Copies views
         *  - Exports configuration
         *  - Exports Assets
         */
        $this->commands([
            ZermeloBladeCardInstallCommand::class
        ]);

        /*
         * Merge with main config so parameters are accessable.
         * Try to load config from the app's config directory first,
         * then load from the package.
         */
        if ( file_exists( config_path( 'zermelobladecard.php' ) ) ) {

            $this->mergeConfigFrom(
                config_path( 'zermelobladecard.php' ), 'zermelobladecard'
            );
        } else {
            $this->mergeConfigFrom(
                __DIR__.'/../config/zermelobladecard.php', 'zermelobladecard'
            );
        }

        $this->loadViewsFrom( resource_path( 'views/zermelo' ), 'Zermelo');
	}
}
