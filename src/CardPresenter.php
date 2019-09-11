<?php

namespace CareSet\ZermeloBladeCard;

use CareSet\Zermelo\Reports\Cards\CardPresenter as BasePresenter;

class CardPresenter extends BasePresenter
{
    public function bootstrapCssLocation()
    {
        return asset( config( 'zermelobladecard.BOOTSTRAP_CSS_LOCATION' ) );
    }
}
