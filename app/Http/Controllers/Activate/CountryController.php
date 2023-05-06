<?php

namespace App\Http\Controllers\Activate;

use App\Http\Controllers\Controller;
use App\Models\Activate\SmsCountry;
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
        $countries = SmsCountry::paginate(10);

        return view('activate.country.index', compact(
            'countries',
        ));
    }

    /**
     * Обновление списка стран с сервиса
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $this->countryService->getApiCountries();

        return redirect()->route('activate.countries.index');
    }

    /**
     * Удаление всех стран
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete()
    {
        $countries = SmsCountry::all();

        foreach ($countries as $country) {
            $country->delete();
        }
        return redirect()->route('activate.countries.index');
    }
}
