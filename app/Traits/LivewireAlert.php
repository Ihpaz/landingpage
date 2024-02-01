<?php

namespace App\Traits;

use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Support\MessageBag;

trait LivewireAlert
{
    protected $config;

    public function success($title, $text)
    {
        $this->alert($title, $text, 'success');
    }

    public function info($title, $text)
    {
        $this->alert($title, $text, 'info');
    }

    public function warning($title, $text)
    {
        $this->alert($title, $text, 'warning');
    }

    public function error($provider)
    {
        $value = $this->parseErrors($provider);
        session()->flash('errors', $value);
    }

    private function alert($title = '', $text = '', $icon = null)
    {
        $this->config['title'] = $title;
        $this->config['text'] = $text;
        if (!is_null($icon)) {
            $this->config['icon'] = $icon;
        }
        $this->dispatch();
        return $this;
    }

    /**
     * Parse the given errors into an appropriate value.
     *
     * @param  \Illuminate\Contracts\Support\MessageProvider|array|string  $provider
     * @return \Illuminate\Support\MessageBag
     */
    protected function parseErrors($provider)
    {
        if ($provider instanceof MessageProvider) {
            return $provider->getMessageBag();
        }

        return new MessageBag((array) $provider);
    }

    /**
     * Flash the config options for alert.
     *
     * @author Rashid Ali <realrashid05@gmail.com>
     */
    private function dispatch()
    {
        $this->dispatchBrowserEvent('swal', $this->config);
    }

    protected function setDefaultConfig()
    {
        $this->config = [
            'title' => '',
            'text' => '',
            'timer' => config('sweetalert.timer'),
            'width' => config('sweetalert.width'),
            'heightAuto' => config('sweetalert.height_auto'),
            'padding' => config('sweetalert.padding'),
            'showConfirmButton' => config('sweetalert.show_confirm_button'),
            'showCloseButton' => config('sweetalert.show_close_button'),
            'timerProgressBar' => config('sweetalert.timer_progress_bar'),
            'customClass' => [
                'container' => config('sweetalert.customClass.container'),
                'popup' => config('sweetalert.customClass.popup'),
                'header' => config('sweetalert.customClass.header'),
                'title' => config('sweetalert.customClass.title'),
                'closeButton' => config('sweetalert.customClass.closeButton'),
                'icon' => config('sweetalert.customClass.icon'),
                'image' => config('sweetalert.customClass.image'),
                'content' => config('sweetalert.customClass.content'),
                'input' => config('sweetalert.customClass.input'),
                'actions' => config('sweetalert.customClass.actions'),
                'confirmButton' => config('sweetalert.customClass.confirmButton'),
                'cancelButton' => config('sweetalert.customClass.cancelButton'),
                'footer' => config('sweetalert.customClass.footer')
            ]
        ];
    }
}
