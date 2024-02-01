<?php

namespace App\Http\Livewire\Personal;

use App\Helpers\ActivityLog;
use App\Models\User;
use App\Traits\LivewireAlert;
use Livewire\Component;

class Configuration extends Component
{
    use LivewireAlert;

    public $user_id;
    public $locale;
    public $timezone;

    public function mount(User $user)
    {
        if ($user) {
            $this->user_id = $user->id;
            $this->locale = $user->locale;
            $this->timezone = $user->timezone;
        }
    }

    public function save()
    {
        try {
            $user = User::findOrFail($this->user_id);
            $user->locale = $this->locale;
            $user->timezone = $this->timezone;
            $user->save();

            return $this->success(trans('common.success'), 'Konfigurasi berhasil disimpan');
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return $this->error($e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.personal.configuration');
    }
}
