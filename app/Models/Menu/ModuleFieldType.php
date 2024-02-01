<?php

namespace App\Models\Menu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleFieldType extends Model
{
    use HasFactory;

    protected $table = 'cms_module_field_types';

    public static function getFTypes()
    {
        $fields = self::orderBy('name', 'asc')->get();
        $fields2 = [];
        foreach ($fields as $field) {
            $fields2[$field['name']] = $field['id'];
        }

        return $fields2;
    }

    public static function getFTypes2()
    {
        $fields = self::orderBy('name', 'asc')->get();
        $fields2 = [];
        foreach ($fields as $field) {
            $fields2[$field['id']] = $field['name'];
        }

        return $fields2;
    }
}
