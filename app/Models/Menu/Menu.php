<?php

namespace App\Models\Menu;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Spatie\Activitylog\LogOptions;

class Menu extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'cms_menus';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'type', 'url', 'icon', 'parent', 'hierarchy']);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $description = [
            'created' => 'Buat data Menu dengan nama :subject.name',
            'updated' => 'Ubah data Menu dengan nama :subject.name',
            'deleted' => 'Hapus data Menu dengan nama :subject.name',
        ];
        return $description[$eventName];
    }

    public function children()
    {
        return $this->hasMany($this, 'parent', 'id');
    }

    public function getHtmlAttribute()
    {
        if ($this->type == 'separator') {
            return '<li class="nav-small-cap">' . trans($this->name) . '</li>';
        } else {
            $url = $this->url;
            if ($this->type == 'route') {
                $url = route($this->url);
            }
            if ($this->type == 'module') {
                $url = route('module.index', $this->url);
            }
            $list = $this->parseChild($this->children);
            $has_child = count($this->children) ? true : false;
            if(auth()->user()->can($this->permission)) {
                return '
                    <li>
                        <a class="' . ($has_child ? 'has-arrow' : null) . ' waves-effect waves-dark" href="' . $url . '" aria-expanded="false">
                            <i class="mdi ' . $this->icon . '"></i><span class="hide-menu">' . trans($this->name) . '</span>
                        </a>' . implode('', $list) . '
                    </li>
                ';
            }            
        }
    }

    public function getChildHtmlAttribute()
    {
        $list = $this->parseChild($this->children);
        $has_child = count($this->children) ? true : false;
        $url = $this->type == 'route' ? (Route::has($this->url) ? route($this->url) : $this->url) : $this->url;

        return '<li><a class="' . ($has_child ? 'has-arrow' : null) . '" href="' . $url . '" >' . trans($this->name) . '</a>' . implode('', $list) . '</li>';
    }

    private function parseChild($children)
    {
        $list = [];
        array_push($list, '<ul aria-expanded="false" class="collapse">');
        foreach ($children as $data) {
            if(auth()->user()->can($data->permission)) {
                array_push($list, $data->child_html);
            }
        }
        array_push($list, '</ul>');
        return count($children) ? $list : [];
    }
}
