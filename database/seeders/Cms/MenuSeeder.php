<?php

namespace Database\Seeders\Cms;

use App\Models\Menu\Menu;
use App\Models\Menu\ModuleFieldType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    private $menu = [
        ['name' => 'PERSONAL','url' => '','icon' => '', 'type' => 'separator','parent' => 0, 'hierarchy' => 1],
        ['name' => 'Dashboard', 'url' => 'index', 'icon' => 'dashboard', 'type' => 'controller','parent' => 0, 'hierarchy' => 2],
        ['name' => 'SETTINGS','url' => '','icon' => '', 'type' => 'separator','parent' => 0, 'hierarchy' => 3],
        ['name' => 'Administration','url' => '#','icon' => '', 'type' => 'custom','parent' => 0, 'hierarchy' => 4],
        ['name' => 'MASTER','url' => '','icon' => '', 'type' => 'separator','parent' => 0, 'hierarchy' => 5],
    ];

    private $field_type = [
        ['id' => 1, 'name' => 'Address'],
        ['id' => 2, 'name' => 'Checkbox'],
        ['id' => 3, 'name' => 'Date'],
        ['id' => 4, 'name' => 'Datetime'],
        ['id' => 5, 'name' => 'Decimal'],
        ['id' => 6, 'name' => 'Dropdown'],
        ['id' => 7, 'name' => 'Email'],
        ['id' => 8, 'name' => 'File'],
        ['id' => 9, 'name' => 'Image'],
        ['id' => 10, 'name' => 'Integer'],
        ['id' => 11, 'name' => 'Multiselect'],
        ['id' => 12, 'name' => 'Name'],
        ['id' => 13, 'name' => 'Radio'],
        ['id' => 14, 'name' => 'String'],
        ['id' => 15, 'name' => 'Taginput'],
        ['id' => 16, 'name' => 'Textarea'],
        ['id' => 17, 'name' => 'URL'],
        ['id' => 18, 'name' => 'Files'],
        ['id' => 19, 'name' => 'Checklist'],
        ['id' => 20, 'name' => 'List'],
        ['id' => 21, 'name' => 'Password']
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menu
        foreach($this->menu as $data) {
            Menu::create($data);
        }

        // Note: Do not edit below lines
        foreach($this->field_type as $data) {
            ModuleFieldType::updateOrCreate(['id' => $data['id']], $data);
        }
    }
}
