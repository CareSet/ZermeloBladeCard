<?php

namespace CareSet\ZermeloBladeCard\Console;

use CareSet\Zermelo\Console\AbstractZermeloInstallCommand;

class ZermeloBladeCardInstallCommand extends AbstractZermeloInstallCommand
{
    protected $view_path = __DIR__.'/../../views';

    protected $asset_path = __DIR__.'/../../assets';

    protected $config_file = __DIR__.'/../../config/zermelobladecard.php';

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'zermelo/card.blade.php',
        'zermelo/layouts/card.blade.php',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zermelo:install_zermelobladecard
                    {--force : Overwrite existing views by default}';
}
