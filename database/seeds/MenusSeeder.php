<?php

use Illuminate\Database\Seeder;

class MenusSeeder extends Seeder
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
                'id' => 1,
                'parent_id' => 0,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Панель управления',
                'link' => '/',
                'ordering' => 1,
                'icon' => 'fa fa-home',
                'published' => '1'
            ],
            [
                'id' => 2,
                'parent_id' => 0,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Содержимое',
                'link' => '#',
                'ordering' => 2,
                'icon' => 'fa fa-files-o',
                'published' => '1'
            ],
            [
                'id' => 3,
                'parent_id' => 0,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Справочник',
                'link' => '/registry',
                'ordering' => 8,
                'icon' => 'fa fa-book',
                'published' => '1'
            ],
            [
                'id' => 4,
                'parent_id' => 0,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Компоненты',
                'link' => '#',
                'ordering' => 3,
                'icon' => 'fa fa-puzzle-piece',
                'published' => '1'
            ],
            [
                'id' => 5,
                'parent_id' => 0,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Пользователи',
                'link' => '#',
                'ordering' => 6,
                'icon' => 'fa fa-user',
                'published' => '1'
            ],
            [
                'id' => 6,
                'parent_id' => 0,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Отчеты',
                'link' => '#',
                'ordering' => 7,
                'icon' => 'fa fa-bar-chart-o',
                'published' => '1'
            ],
            [
                'id' => 7,
                'parent_id' => 0,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Заявки',
                'link' => '#',
                'ordering' => 4,
                'icon' => 'fa fa-wrench',
                'published' => '1'
            ],
            [
                'id' => 10,
                'parent_id' => 4,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Категории',
                'link' => '/categories',
                'ordering' => 1,
                'icon' =>  '',
                'published' => '1'
            ],
            [
                'id' => 11,
                'parent_id' => 2,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Страницы',
                'link' => '/pages',
                'ordering' => 2,
                'icon' => '',
                'published' => '1'
            ],
            [
                'id' => 16,
                'parent_id' => 4,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Обратная связь',
                'link' => '/feedback',
                'ordering' => 4,
                'icon' => '',
                'published' => '0'
            ],
            [
                'id' => 18,
                'parent_id' => 5,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Группы',
                'link' => '/users/roles',
                'ordering' => 1,
                'icon' => '',
                'published' => '0'
            ],
            [
                'id' => 20,
                'parent_id' => 5,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Участки',
                'link' => '/areas',
                'ordering' => 4,
                'icon' => '',
                'published' => '1'
            ],
            [
                'id' => 21,
                'parent_id' => 6,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Статистика',
                'link' => '/statistic',
                'ordering' => 1,
                'icon' =>  '',
                'published' => '1'
            ],
            [
                'id' => 22,
                'parent_id' => 2,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Меню',
                'link' => '/menu',
                'ordering' => 1,
                'icon' => '',
                'published' => '1'
            ],
            [
                'id' => 24,
                'parent_id' => 5,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Пользователи',
                'link' => '/users',
                'ordering' => 2,
                'icon' => '',
                'published' => '1'
            ],
            [
                'id' => 26,
                'parent_id' => 4,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Комментарии',
                'link' => '/comments',
                'ordering' => 3,
                'icon' => '',
                'published' => '0'
            ],
            [
                'id' => 28,
                'parent_id' => 4,
                'group_id' => 1,
                'title' => 'Новости',
                'type' => 'internal_link',
                'page_id' => 0,
                'link' => '/articles',
                'ordering' => 2,
                'icon' => '',
                'published' => '1'
            ],
            [
                'id' => 38,
                'parent_id' => 4,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Faq',
                'link' => '/faq',
                'ordering' => 6,
                'icon' => '',
                'published' => '1'
            ],
            [
                'id' => 72,
                'parent_id' => 4,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Объявления',
                'link' => '/adverts',
                'icon' => '',
                'published' => '1',
                'ordering' => 3
            ],
            [
                'id' => 73,
                'parent_id' => 7,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Услуги',
                'link' => '/services',
                'icon' => '',
                'published' => '1',
                'ordering' => 1
            ],
            [
                'id' => 74,
                'parent_id' => 4,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Голосования',
                'link' => '/polls',
                'icon' => '',
                'published' => '1',
                'ordering' => 3
            ],
            [
                'id' => 75,
                'parent_id' => 4,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Полезная информация',
                'link' => '/handbook',
                'icon' => '',
                'published' => '1',
                'ordering' => 6
            ],
            [
                'id' => 76,
                'parent_id' => 4,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Предложения/Жалобы',
                'link' => '/abuses',
                'ordering' => 4,
                'icon' => '',
                'published' => '1'
            ],
            [
                'id' => 77,
                'parent_id' => 4,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Нормативные документы',
                'link' => '/normative-docs',
                'icon' => '',
                'published' => '1',
                'ordering' => 6
            ],
            [
                'id' => 78,
                'parent_id' => 7,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Заявки',
                'link' => '/service-request',
                'icon' => '',
                'published' => '1',
                'ordering' => 3
            ],
            [
                'id' => 80,
                'parent_id' => 7,
                'group_id' => 1,
                'type' => 'internal_link',
                'page_id' => 0,
                'title' => 'Исполнители',
                'link' => '/service-executors',
                'icon' => '',
                'published' => '1',
                'ordering' => 2
            ],

        ];

        DB::table('menus')->insert($data);
    }
}
