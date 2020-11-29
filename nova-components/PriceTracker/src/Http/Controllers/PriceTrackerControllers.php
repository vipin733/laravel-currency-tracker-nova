<?php

namespace Acme\PriceTracker\Http\Controllers;

use App\Enums\Currency;
use App\Enums\DateFilter;
use App\Helpers\DataFilter;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class PriceTrackerControllers extends Controller
{

    /**
     * get latest rupee trakcer
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function index(DataFilter $filter, NovaRequest $request)
    {
        $currencyData = $filter->getCurrencyData($request);
        $lables = $currencyData->lables;
        $data = $currencyData->data;

        return response()->json([
            'datasets'   => $data,
            'lables'     => $lables
        ], 200);
    }

    public function filters()
    {
        $filters = DateFilter::asSelectArray();
        $currencies = Currency::getInstances();

        return response()->json([
            'filters'   => $filters,
            'currencies'     => $currencies
        ], 200);

    }

}
