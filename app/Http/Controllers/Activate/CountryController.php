<?php

namespace App\Http\Controllers\Activate;

use App\Http\Controllers\Controller;
use App\Models\Country\Country;
use App\Services\Activate\CountryService;

class CountryController extends Controller
{
    /**
     * @var CountryService
     */
    private CountryService $countryService;

    public function __construct()
    {
        $this->countryService = new CountryService();
    }

    /**
     * Получение списка стран
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $countries = Country::paginate(10);

        return view('activate.country.index', compact(
            'countries',
        ));
    }
}
