<?php

namespace App\Helpers;

use App\Enums\Currency;
use App\Enums\DateFilter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class DataFilter 
{
    private $currencyData = [
        [
            'label'       => 'USD',
            'borderColor' => "#8e5ea2",
            'fill'        =>  false,
            'data'        => []
        ],
        [
            'label'       => 'GBP',
            'fill'        =>  false,
            'borderColor' => "#3cba9f",
            'data'        => []
        ],
        [
            'label'       => 'EUR',
            'fill'        =>  false,
            'borderColor' => "#c45850",
            'data'        => []
        ]
    ];

    //getCurrencyData function for get currency data
    public function getCurrencyData($request)
    {
        $lables = [];
        $data = [];

        try {
            $url = $this->filterUrl($request);
            $filterData = $this->getFilterData($request);
            $response = Http::get($url);
            if ($response->ok()) {
                $rates  = json_decode($response->body());
                $getData = $this->formatData($rates, $filterData);
                $lables  = $getData->lables;
                $data  = $getData->data;
            }

        } catch (\Exception $e) {
            report($e);
        }

        $currencyData = new \stdClass;
        $currencyData->lables = $lables;
        $currencyData->data = $data;

        return $currencyData;

    }

    // formatData function for filter currency data and dates
    public  function formatData($rates, $filter)
    {
        $lables = [];
        $datas = [];

        $currencyData = $this->currencyData;
        $currencies = $filter->currencies;

        foreach ($rates->rates as $key => $obj) {
            $lables[] = $key;// Carbon::parse($key)->format('d M, y');
            foreach ($obj as $ckey => $value) {
               $datas [] = [
                   'currency' => $ckey ,
                   'value'    => $value
               ];
            }
        }

        $filterCurrencyData = [];

        foreach($currencyData as $cData){
            $currency = $cData['label'];
            if (in_array($currency, $currencies)) {
                $filterData = collect($datas)->where('currency', $currency)->pluck('value');
                $cData['data'] =  $filterData;
                $filterCurrencyData[] = $cData;
            }
        }

        $formatedData  = new \stdClass;
        $formatedData->data = $filterCurrencyData;
        $formatedData->lables = $lables;
        return $formatedData;
    }


    // filterUrl function for getting url
    public function filterUrl($request)
    {
        $filters = $this->getFilterData($request);
        $start_at = $filters->start_at;
        $end_at = $filters->end_at;
        $symbols = $filters->symbols;

        $baseUrl = config('nova.currency_tracker_url');
        $url = "$baseUrl?start_at=$start_at&end_at=$end_at&base=INR&symbols=$symbols";

        return $url;
    }

    // getFilterData function for getting filter keys
    public function getFilterData($request)
    {
        $filter = $request->filter;

        $vsCurrency = $request->currency;
        $currencyEnums = Currency::getKeys();
        $symbols = join(',',$currencyEnums);
        if ($vsCurrency) {
            $symbols = Currency::getKey($vsCurrency);
        }

        $start = now()->subWeek()->format('Y-m-d');
        $today = now()->format('Y-m-d');

        if ($filter) {
            if( $filter == DateFilter::CurrentWeek){
                $start = now()->startOfWeek()->format('Y-m-d');
            }
    
            if( $filter == DateFilter::CurrentMonth){
                $start = now()->startOfMonth()->format('Y-m-d');
            }
    
            if( $filter == DateFilter::LastThreeMonth){
                $start = now()->subMonths(3)->format('Y-m-d');
            }
    
            if( $filter == DateFilter::LastSixMonth){
                $start = now()->subMonths(6)->format('Y-m-d');
            }
        }

        $currencies = explode(',', $symbols);
        $dates = new \stdClass;
        $dates->start_at = $start;
        $dates->end_at = $today;
        $dates->symbols = $symbols;
        $dates->currencies = $currencies;

        return $dates;
    }


}