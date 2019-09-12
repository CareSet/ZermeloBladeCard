<?php

namespace CareSet\ZermeloBladeCard\Http\Controllers;

use CareSet\Zermelo\Http\Controllers\AbstractWebController;
use CareSet\Zermelo\Http\Requests\CardsReportRequest;
use CareSet\Zermelo\Models\Presenter;

class CardController extends AbstractWebController
{
    /**
     * @return \Illuminate\Config\Repository|mixed
     *
     * Get the view template
     */
    public  function getViewTemplate()
    {
        return config("zermelobladecard.VIEW_TEMPLATE");
    }

    /**
     * @return string
     *
     * Specify the path to this report's API
     * This report uses the tabular api prefix
     */
    public function getReportApiPrefix()
    {
        return tabular_api_prefix();
    }

    /**
     * @param $report
     *
     * Build presenter and push our required varialbes for this web view
     */
    public function buildPresenter($report)
    {
        $presenter = new Presenter($report);
        $bootstrap_css_location = asset(config('zermelobladecard.BOOTSTRAP_CSS_LOCATION'));
        $presenter->pushViewVariable('bootstrap_css_location', $bootstrap_css_location);
        $presenter->pushViewVariable('report_uri', $this->getReportUri($report));
        $presenter->pushViewVariable('summary_uri', $this->getSummaryUri($report));
        return $presenter;
    }

    /**
     * @param $report
     * @return string
     *
     * Protected method, specific to this controller, to build the report URI (though as of now it's the same as tabular)
     */
    protected function getReportUri($report)
    {
        $parameterString = implode("/", $report->getMergedParameters() );
        $report_api_uri = "/{$this->getApiPrefix()}/{$this->getReportApiPrefix()}/{$report->uriKey()}/{$parameterString}";
        return $report_api_uri;
    }

    protected function getSummaryUri($report)
    {
        $parameterString = implode("/", $report->getMergedParameters() );
        $summary_api_uri = "/{$this->getApiPrefix()}/{$this->getReportApiPrefix()}/{$report->uriKey()}/Summary/{$parameterString}";
        return $summary_api_uri;
    }
}
