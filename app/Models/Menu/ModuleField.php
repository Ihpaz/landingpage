<?php

namespace App\Models\Menu;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;

class ModuleField extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'cms_module_fields';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['colname', 'label', 'module_id', 'field_type_id', 'unique', 'default', 'minlength', 'maxlength', 'required', 'popup_vals', 'sort', 'listing_col', 'comment']);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $description = [
            'created' => 'Buat data Module Field dengan nama :subject.colname',
            'updated' => 'Ubah data Module Field dengan nama :subject.colname',
            'deleted' => 'Hapus data Module Field dengan nama :subject.colname',
        ];
        return $description[$eventName];
    }

    public function module()
    {
        return $this->belongsTo(Modules::class, 'module_id', 'id');
    }

    public function field()
    {
        return $this->belongsTo(ModuleFieldType::class, 'field_type_id', 'id');
    }

    public function html($value, $readOnly = false)
    {
        // Address
        if (in_array($this->field_type_id, [1, 16])) {
            return '
                <textarea class="form-control"
                    name="' . $this->colname . '" rows="3"
                    style="max-height:unset"
                    ' . ($this->required ? "required" : "") . '
                    ' . ($readOnly ? 'readonly' : '') . '
                    placeholder="' . $this->comment . '">' . (old($this->colname) ?? $value) . '</textarea>
            ';
        }
        // Text Input
        if (in_array($this->field_type_id, [7, 12, 14, 17, 21])) {
            return '
                <input class="form-control" 
                    type="' . ($this->field_type_id == 7 ? "email" : ($this->field_type_id == 21 ? "password" : "text")) . '" 
                    name="' . $this->colname . '" 
                    value="' . (old($this->colname) ?? $value) . '" 
                    min="' . $this->minlength . '"
                    max="' . $this->maxlength . '"
                    ' . ($this->required ? "required" : "") . '
                    placeholder="' . $this->comment . '" 
                    ' . ($readOnly ? 'readonly' : '') . '>
            ';
        }
        // Integer
        if (in_array($this->field_type_id, [5, 10])) {
            return '
                <input class="form-control" 
                    type="number" 
                    name="' . $this->colname . '" 
                    value="' . (old($this->colname) ?? $value) . '" 
                    min="' . $this->minlength . '"
                    max="' . $this->maxlength . '"
                    ' . ($this->required ? "required" : "") . '
                    placeholder="' . $this->comment . '"
                    ' . ($readOnly ? 'readonly' : '') . '>
            ';
        }
        // Checkbox
        if ($this->field_type_id == 2) {
            return '
            <div class="input-group">
                <div class="i-checks">
                    <input id="i-'.$this->colname.'" type="checkbox" name="' . $this->colname . '"' . ($this->required ? "required" : "") . ' ' . ($value ? "checked" : "") . ' ' . ($readOnly ? "disabled" : "") . '>
                    <label style="padding-left: 10px;" for="i-' . $this->colname . '">' . $this->comment . '</label>
                </div>
            </div>';
        }
        // Date
        if ($this->field_type_id == 3) {
            return '
                <div class="input-group">
                    <input class="form-control datepicker"
                        id="i-'.$this->colname.'" 
                        type="text" 
                        name="' . $this->colname . '"
                        value="' . (old($this->colname) ?? $value) . '" 
                        placeholder="' . $this->comment . '" 
                        ' . ($readOnly ? 'readonly' : '') . '>
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="icon-calender"></i></span>
                    </div>
                </div>
            ';
        }
        // Dropdown
        if ($this->field_type_id == 6) {
            $popup_vals_str = $this->popup_vals;
            $options = [];
            if (is_string($popup_vals_str) && str_starts_with($popup_vals_str, '@')) {
                // Get Module / Table Name
                $table_name = str_ireplace('@', '', $popup_vals_str);

                $datatable = DB::table($table_name)->orderBy('name')->get();
                foreach ($datatable as $data) {
                    array_push($options, '<option value="' . $data->id . '" ' . ($value == $data->id ? 'selected' : '') . '>' . $data->name . '</option>');
                }
            } else {
                $datalist = json_decode($popup_vals_str, true);
                if (is_array($datalist)) {
                    foreach ($datalist as $data) {
                        array_push($options, '<option value="' . $data . '"' . ($value == $data->id ? 'selected' : '') . '>' . $data . '</option>');
                    }
                }
            }
            return '
                    <div class="input-group">
                        <select name="' . $this->colname . '" class="select2 form-control d-none" ' . ($this->required ? "required" : "") . ' ' . ($readOnly ? 'disabled' : '') . '>
                            <option></option>
                            ' . implode('', $options) . '
                        </select>
                    </div>
                ';
        }
        // Checklist
        if ($this->field_type_id == 19) {
            $popup_vals_str = $this->popup_vals;
            $options = [];

            if (is_string($popup_vals_str) && str_starts_with($popup_vals_str, '@')) {
                // Get Module / Table Name
                $table_name = str_ireplace('@', '', $popup_vals_str);

                $datatable = DB::table($table_name)->orderBy('name')->get();
                foreach ($datatable as $data) {
                    array_push($options, '
                        <div class="col-lg-12 pl-0 pb-2" style="display:inline-block">
                            <div class="input-group">
                                <div class="i-checks">
                                    <input type="checkbox" name="' . $this->colname . '[]" value="' . $data->id . '"' . ' ' . ($value == $data->id ? 'checked' : '') . ' ' . ($readOnly ? 'readonly' : '') . '>
                                    <label for="' . $this->colname . '">' . $data->name . '</label>
                                </div>
                            </div>
                        </div>
                    ');
                }
            } else {
                $datalist = json_decode($popup_vals_str, true);
                if (is_array($datalist)) {
                    foreach ($datalist as $data) {
                        array_push($options, '
                            <div class="col-lg-12 pl-0 pb-2" style="display:inline-block">
                                <div class="input-group">
                                    <div class="i-checks">
                                        <input type="checkbox" name="' . $this->colname . '[]" value="' . $data . '"' . ($value == $data->id ? 'checked' : '') . ($readOnly ? 'readonly' : '') . '>
                                        <label for="' . $this->colname . '">' . $data . '</label>
                                    </div>
                                </div>
                            </div>
                        ');
                    }
                }
            }

            return '
                    <ul class="mb-0 icheck-list" style="list-style: none;padding-inline-start:0px;float:unset">
                    <li>' . implode('', $options) . '</li>
                    </ul>
                ';
        }
        // Radio
        if ($this->field_type_id == 13) {
            $popup_vals_str = $this->popup_vals;
            $options = [];
            if (is_string($popup_vals_str) && str_starts_with($popup_vals_str, '@')) {
                // Get Module / Table Name
                $table_name = str_ireplace('@', '', $popup_vals_str);

                $datatable = DB::table($table_name)->orderBy('name')->get();
                foreach ($datatable as $data) {
                    array_push($options, '
                        <div class="col-lg-12 pl-0 pb-2" style="display:inline-block">
                            <div class="input-group">
                                <div class="i-checks">
                                    <input type="radio" name="' . $this->colname . '" value="' . $data->id . '"' . ($readOnly ? 'readonly' : '') . '>
                                    <label for="' . $this->colname . '">' . $data->name . '</label>
                                </div>
                            </div>
                        </div>
                    ');
                }
            } else {
                $datalist = json_decode($popup_vals_str, true);
                if (is_array($datalist)) {
                    foreach ($datalist as $data) {
                        array_push($options, '
                            <div class="col-lg-12 pl-0 pb-2" style="display:inline-block">
                                <div class="input-group">
                                    <div class="i-checks">
                                        <input type="radio" name="' . $this->colname . '" value="' . $data . '"' . ($readOnly ? 'readonly' : '') . '>
                                        <label for="' . $this->colname . '">' . $data . '</label>
                                    </div>
                                </div>
                            </div>
                        ');
                    }
                }
            }
            return '
                <ul class="mb-0 icheck-list" style="column-count: 2;column-gap: 0;list-style: none;padding-inline-start:0px;">
                <li>' . implode('', $options) . '</li>
                </ul>
            ';
        }
    }

    /**
     * Get Field Value when its associated with another Module / Table via "@"
     * e.g. "@employees".
     *
     * @param $field Module Field Object
     * @param $value_id This is a ID for which we wanted the Value from another table
     * @return mixed Returns Value found in table or Value id itself
     */
    public static function getFieldValue($field, $value_id)
    {
        $external_table_name = substr($field->popup_vals, 1);
        if (Schema::hasTable($external_table_name)) {
            $external_value = DB::table($external_table_name)->where('id', $value_id)->get();
            if (isset($external_value[0])) {
                $external_module = Modules::where('table', $external_table_name)->first();
                if (isset($external_module->view_col)) {
                    $external_value_viewcol_name = $external_module->view_col;

                    return $external_value[0]->$external_value_viewcol_name;
                } else {
                    if (isset($external_value[0]->{'name'})) {
                        return $external_value[0]->name;
                    } elseif (isset($external_value[0]->{'title'})) {
                        return $external_value[0]->title;
                    }
                }
            } else {
                return $value_id;
            }
        } else {
            return $value_id;
        }
    }
}
