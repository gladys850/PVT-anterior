<?php

use Illuminate\Database\Seeder;
use App\City;

class CityGeolocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities = City::get();
        foreach ($cities as $city) {
            switch ($city->name) {
                case 'BENI':
                    $city->latitude = -14.834909060282543;
                    $city->longitude = -64.90420103073122;
                    break;
                case 'CHUQUISACA':
                    $city->latitude = -19.04790026387467;
                    $city->longitude = -65.25959372520448;
                    break;
                case 'COCHABAMBA':
                    $city->latitude = -17.393741314813106;
                    $city->longitude = -66.15700721740724;
                    break;
                case 'LA PAZ':
                    $city->latitude = -16.495323179001716;
                    $city->longitude = -68.1342512369156;
                    break;
                case 'ORURO':
                    $city->latitude = -17.96955349604632;
                    $city->longitude = -67.11474359035493;
                    break;
                case 'PANDO':
                    $city->latitude = -11.018289250407365;
                    $city->longitude = -68.75365376472475;
                    break;
                case 'POTOSI':
                    $city->latitude = -19.589262281538055;
                    $city->longitude = -65.75350105762483;
                    break;
                case 'SANTA CRUZ':
                    $city->latitude = -17.784324090235096;
                    $city->longitude = -63.18201363086701;
                    break;
                case 'TARIJA':
                    $city->latitude = -21.533913878007194;
                    $city->longitude = -64.73429381847383;
                    break;
                default:
                    continue 2;
            }
            $city->save();
        }
    }
}
