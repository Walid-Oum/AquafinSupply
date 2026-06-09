<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FloodRiskController extends Controller
{

    public function index(){
        $response = HTTP::get("Http::get('https://api.open-meteo.com/v1/forecast", [
    'latitude' => 51.2205,
    'longitude' => 4.4003,
    'daily' => 'precipitation_sum,precipitation_probability_max,rain_sum,wind_gusts_10m_max,weather_code',
    'timezone' => 'Europe/Brussels',
    'forecast_days' => 16,
            ]);

        $data = $response->json();

        $location = auth()->user()->location;
        return view('flood_risk', compact('data', 'location'));
    }

}
