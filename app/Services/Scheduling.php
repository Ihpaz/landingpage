<?php

namespace App\Services;

use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Support\Str;

class Scheduling
{
    /**
     * Get all events in console kernel.
     *
     * @return array
     */
    protected function getKernelEvents()
    {
        app()->make('Illuminate\Contracts\Console\Kernel');
        return app()->make('Illuminate\Console\Scheduling\Schedule')->events();
    }

    /**
     * Get all formatted tasks.
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getTasks()
    {
        $tasks = [];

        foreach ($this->getKernelEvents() as $index => $event) {
            $tasks[] = [
                'id'            => $index + 1,
                'task'          => $this->formatTask($event),
                'expression'    => $event->expression,
                'nextRunDate'   => $event->nextRunDate()->format('Y-m-d H:i:s'),
                'description'   => $event->description ?? '-',
                'readable'      => CronSchedule::fromCronString($event->expression)->asNaturalLanguage(),
            ];
        }

        return $tasks;
    }

    /**
     * Format a giving task.
     *
     * @param $event
     *
     * @return array
     */
    protected function formatTask($event)
    {
        
        if ($event instanceof CallbackEvent) {
            return [
                'type' => 'closure',
                'name' => 'Closure',
            ];
        }

        if (Str::contains($event->command, '\'artisan\'')) {
            $exploded = explode(' ', $event->command);

            return [
                'type' => 'artisan',
                'name' => 'artisan '.implode(' ', array_slice($exploded, 2)),
            ];
        }

        return [
            'type' => 'Command',
            'name' => $event->command,
        ];
    }

    /**
     * Show specific task
     * 
     * @param int $id
     * 
     * @return array
     */
    public function showTask($id)
    {

        $event = $this->getKernelEvents()[$id - 1];
        $data['task']     = $this->formatTask($event)['name'];
        $data['expression'] = $event->expression;
        $data['nexRunDate'] = $event->nextRunDate()->format('Y-m-d H:i:s');
        $data['description'] = $event->description ?? '-';
        return $data;
    }

    /**
     * Run specific task.
     *
     * @param int $id
     *
     * @return string
     */
    public function runTask($id)
    {
        set_time_limit(0);

        /** @var \Illuminate\Console\Scheduling\Event $event */
        $event = $this->getKernelEvents()[$id - 1];
        $event->run(app());
    }
}