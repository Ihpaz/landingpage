<?php

namespace Database\Seeders\Master;

use App\Models\Master\Location\Country;
use App\Models\Master\Location\Currency;
use App\Models\Master\Location\District;
use App\Models\Master\Location\Province;
use App\Models\Master\Location\Regency;
use App\Models\Master\Location\Village;
use GuzzleHttp\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->country();
        $this->province();
        $this->regency();
        $this->district();
        $this->village();
    }

    private function country()
    {
        $client = new Client();
        $endpoint = 'https://restcountries.com/v3.1/all';
        $response = $client->get($endpoint);

        $data = json_decode((string)$response->getBody(), true);

        if ($response->getStatusCode() == '200') {
            foreach ($data as $country) {
                if (array_key_exists('ccn3', $country)) {
                    Country::updateOrCreate([
                        'code' => $country['ccn3'],
                    ], [
                        'code' => $country['ccn3'],
                        'name' => $country['name']['common'],
                        'official' => $country['name']['official'],
                        'alpha_2' => $country['cca2'],
                        'alpha_3' => $country['cca3']
                    ]);

                    if (array_key_exists('currencies', $country)) {
                        $currencies = array_keys($country['currencies']);
                        foreach ($currencies as $currency) {
                            Currency::updateOrCreate([
                                'code' => $currency
                            ], [
                                'code' => $currency,
                                'symbol' => array_key_exists('symbol', $country['currencies'][$currency]) ? $country['currencies'][$currency]['symbol'] : $currency ,
                                'name' => ucwords($country['currencies'][$currency]['name'])
                            ]);
                        }

                        $negara = Country::where('code', $country['ccn3'])->first();
                        $mata_uang = Currency::whereIn('code', $currencies)->pluck('id');
                        $negara->currencies()->sync($mata_uang);;
                    }
                }
            }
        }
    }

    private function province()
    {
        $client = new Client();
        $endpoint = 'https://raw.githubusercontent.com/yusufsyaifudin/wilayah-indonesia/master/data/list_of_area/provinces.json';
        $response = $client->get($endpoint);

        $data = (array)json_decode((string)$response->getBody(), true);

        if ($response->getStatusCode() == '200') {
            $ind = Country::where('code', 360)->first();
            foreach ($data as $province) {
                Province::updateOrCreate([
                    'code' => $province['id'],
                ], [
                    'code' => $province['id'],
                    'country_id' => $ind->id,
                    'name' => ucwords(strtolower($province['name'])),
                    'latitude' => $province['latitude'],
                    'longitude' => $province['longitude']
                ]);
            }
        }
    }

    private function regency()
    {
        $client = new Client();
        $endpoint = 'https://raw.githubusercontent.com/yusufsyaifudin/wilayah-indonesia/master/data/list_of_area/regencies.json';
        $response = $client->get($endpoint);

        $data = (array)json_decode((string)$response->getBody(), true);

        if ($response->getStatusCode() == '200') {
            foreach ($data as $regency) {
                $province = Province::where('code', $regency['province_id'])->first();
                Regency::updateOrCreate([
                    'code' => $regency['id'],
                ], [
                    'code' => $regency['id'],
                    'province_id' => $province->id,
                    'name' => ucwords(strtolower($regency['name'])),
                    'latitude' => $regency['latitude'],
                    'longitude' => $regency['longitude']
                ]);
            }
        }
    }

    private function district()
    {
        $client = new Client();
        $endpoint = 'https://raw.githubusercontent.com/yusufsyaifudin/wilayah-indonesia/master/data/list_of_area/districts.json';
        $response = $client->get($endpoint);

        $data = (array)json_decode((string)$response->getBody(), true);

        if ($response->getStatusCode() == '200') {
            foreach ($data as $district) {
                $regency = Regency::where('code', $district['regency_id'])->first();
                District::updateOrCreate([
                    'code' => $district['id'],
                ], [
                    'code' => $district['id'],
                    'regency_id' => $regency->id,
                    'name' => ucwords(strtolower($district['name'])),
                    'latitude' => $district['latitude'],
                    'longitude' => $district['longitude']
                ]);
            }
        }
    }

    private function village()
    {
        $client = new Client();
        $endpoint = 'https://github.com/yusufsyaifudin/wilayah-indonesia/raw/master/data/list_of_area/villages.json';
        $response = $client->get($endpoint);

        $data = (array)json_decode((string)$response->getBody(), true);

        if ($response->getStatusCode() == '200') {
            foreach ($data as $village) {
                $district = District::where('code', $village['district_id'])->first();
                Village::updateOrCreate([
                    'code' => $village['id'],
                ], [
                    'code' => $village['id'],
                    'district_id' => $district->id,
                    'name' => ucwords(strtolower($village['name'])),
                    'latitude' => $village['latitude'],
                    'longitude' => $village['longitude']
                ]);
            }
        }
    }
}
