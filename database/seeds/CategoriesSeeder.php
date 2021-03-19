<?php

use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
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
                'id' => '1',
                'title' => 'Новости',
                'alias' => 'news',
                'parent_id' => '0',
                'image' => 'NULL',
                'description' => 'Наши новости',
                'metakey' => '',
                'metadesc' => '',
                'published' => '1',
                'ordering' => 0,
                'created_at' => 'NULL',
                'updated_at' => 'NULL',
            ],
            [
                'id' => '2',
                'title' => 'Объявления',
                'alias' => 'adverts',
                'parent_id' => '0',
                'image' => 'NULL',
                'description' => 'Наши объявления',
                'metakey' => '',
                'metadesc' => '',
                'published' => '1',
                'ordering' => 0,
                'created_at' => 'NULL',
                'updated_at' => 'NULL',
            ],
            [
                'id' => '3',
                'title' => 'Спорт',
                'alias' => 'sport',
                'parent_id' => '1',
                'image' => 'NULL',
                'description' => '',
                'metakey' => 'NULL',
                'metadesc' => 'NULL',
                'published' => '1',
                'ordering' => 0,
                'created_at' => 'NULL',
                'updated_at' => 'NULL',
            ],
            [
                'id' => '4',
                'title' => 'Экономика',
                'alias' => 'economic',
                'parent_id' => '1',
                'image' => 'NULL',
                'description' => '',
                'metakey' => 'NULL',
                'metadesc' => 'NULL',
                'published' => '1',
                'ordering' => 1,
                'created_at' => 'NULL',
                'updated_at' => 'NULL',
            ],
            [
                'id' => '5',
                'title' => 'Политика',
                'alias' => 'politic',
                'parent_id' => '1',
                'image' => 'NULL',
                'description' => '',
                'metakey' => 'NULL',
                'metadesc' => 'NULL',
                'published' => '1',
                'ordering' => 1,
                'created_at' => 'NULL',
                'updated_at' => 'NULL',
            ],
            [
                'id' => '6',
                'title' => 'Полезная информация',
                'alias' => 'poleznaya-informatsiya-6',
                'parent_id' => '0',
                'image' => 'NULL',
                'description' => '',
                'metakey' => '',
                'metadesc' => '',
                'published' => '1',
                'ordering' => 0,
                'created_at' => '2020-09-07 10:34:21',
                'updated_at' => 'NULL',
            ],
        ];

        DB::table('categories')->insert($data);
    }
}
