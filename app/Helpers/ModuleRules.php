<?php

namespace App\Helpers;

use App\Models\Menu\ModuleField;
use App\Models\Menu\ModuleFieldType;
use App\Models\Menu\Modules;

class ModuleRules 
{
    /**
     * Create Validations rules array for Laravel Validations using Module Field Context / Metadata
     * Used in LaraAdmin generated Controllers for store and update.
     * This generates array of validation rules for whole Module.
     *
     *
     * @param $module \App\Models\Menu\Modules Name
     * @param $request \Illuminate\Http\Request Object
     * @param bool $isEdit Is this a Update or Store Request
     * @return array Returns Array to validate given Request
     */
    public static function validate(Modules $module, $request, $isEdit = false)
    {
        $rules = [];
        if (isset($module)) {
            $ftypes = ModuleFieldType::getFTypes2();
            foreach ($module->fields as $field) {
                if (isset($request->{$field['colname']})) {
                    $col = '';
                    if ($field['required']) {
                        $col .= 'required|';
                    }
                    if (in_array($ftypes[$field['field_type_id']], ['Currency', 'Decimal'])) {
                        // No min + max length
                    } elseif ($ftypes[$field['field_type_id']] == 'List') {
                        if ($field['minlength'] != 0) {
                            $col .= 'mincount:'.$field['minlength'].'|';
                        }
                        if ($field['maxlength'] != 0) {
                            $col .= 'maxcount:'.$field['maxlength'].'|';
                        }
                    } elseif ($ftypes[$field['field_type_id']] == 'Taginput') {
                        if ($field['minlength'] != 0) {
                            $col .= 'mincount:'.$field['minlength'].'|';
                        }
                        if ($field['maxlength'] != 0) {
                            $col .= 'maxcount:'.$field['maxlength'].'|';
                        }
                    } elseif ($ftypes[$field['field_type_id']] == 'Checklist') {
                        if ($field['minlength'] != 0) {
                            $col .= 'mincount:'.$field['minlength'].'|';
                        }
                        if ($field['maxlength'] != 0) {
                            $col .= 'maxcount:'.$field['maxlength'].'|';
                        }
                    } else {
                        if ($ftypes[$field['field_type_id']] == 'Integer') {
                            $col .= 'numeric|'; // TODO check
                        }
                        if ($field['minlength'] != 0) {
                            $col .= 'min:'.$field['minlength'].'|';
                        }
                        if ($field['maxlength'] != 0) {
                            $col .= 'max:'.$field['maxlength'].'|';
                        }
                    }
                    if ($field['unique'] && ! $isEdit) {
                        $col .= 'unique:'.$module->table.',NULL';
                    }
                    // 'name' => 'required|unique|min:5|max:256',
                    // 'author' => 'required|max:50',
                    // 'price' => 'decimal',
                    // 'pages' => 'integer|max:5',
                    // 'genre' => 'max:500',
                    // 'description' => 'max:1000'
                    if ($col != '') {
                        $rules[$field['colname']] = trim($col, '|');
                    }
                }
            }

            return $rules;
        } else {
            return $rules;
        }
    }

    public static function get($slug)
    {
        $module = Modules::where('slug', $slug)
            ->first();
        if (isset($module)) {
            $moduleArr = $module->toArray();
            $fields = ModuleField::where('module', $moduleArr['id'])->orderBy('sort','asc')->get()
                ->toArray();
            $field2 = [];
            foreach ($fields as $field) {
                $field2[$field['colname']] = $field;
            }
            $module->fields = $field2;

            return $module;            
        } else {
            return null;
        }
    }
}