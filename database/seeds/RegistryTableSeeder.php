<?php

use Illuminate\Database\Seeder;

class RegistryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'key' => 'total_area',
                'title' => 'Общая площадь ДНП',
                'value' => 11000,
                'group' => ''
            ],
            [
                'key' => 'meter_reading_period_start',
                'title' => 'Период сдачи показаний, начало',
                'value' => 5,
                'group' => ''
            ],
            [
                'key' => 'meter_reading_period_end',
                'title' => 'Период сдачи показаний, конец',
                'value' => 15,
                'group' => ''
            ],
            [
                'key' => '2020_05_30_195031_tariff',
                'title' => '',
                'value' => '{"water":"33.140","electricity":"4.120","electricity_night":"2.75", "end_day":"2020-06-30"}',
                'group' => 'tariff_data'
            ],
            [
                'key' => '2020_06_30_195031_tariff',
                'title' => '',
                'value' => '{"water":"33.150","electricity":"4.220","electricity_night":"2.8", "end_day":"2020-07-20"}',
                'group' => 'tariff_data'
            ],
            [
                'key' => '2020_07_08_210730_tariff',
                'title' => '',
                'value' => '{"water":"33.03","electricity":"4.200","electricity_night":"2.66","end_day":"2020-08-08"}',
                'group' => 'tariff_data'
            ],
            [
                'key' => 'date_receipt_calculation',
                'title' => 'Дата расчета квитанций',
                'value' => 20,
                'group' => ''
            ]
        ];
        DB::table('registry')->insert($data);
    }
}
