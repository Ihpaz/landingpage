<?php

namespace App\Http\Controllers\Cms;

use App\Utils\Decomposer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SystemInformationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('role:superadmin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // MANDATORY PARAMETER
        $data['title'] = 'System Information';
        // END MANDATORY PARAMETER

        $data['server_env'] = Decomposer::getServerEnv();
        $data['server_extras'] = Decomposer::getServerExtras();
        $data['extra_stats'] = Decomposer::getExtraStats();

        $data['uptime'] = trim(shell_exec('uptime -p'));
        $data['disk_free'] = disk_free_space(app_path('/'));
        $data['disk_total'] = disk_total_space(app_path('/'));
        $data['disk_used'] = $data['disk_total'] - $data['disk_free'];
        $data['db_used'] = DB::select("SELECT pg_size_pretty(pg_database_size('" . config('database.connections')[config('database.default')]['database'] . "')) as total")[0]->total;

        // Read changelog files
        try {
            $data['file_changelog'] = File::get(base_path('CHANGELOG.md'));
        } catch (\Exception $e) {
            $data['file_changelog'] = 'Files not found in specific path';
        }

        // Maintener
        chdir(base_path());
        $data['git_version'] = trim(shell_exec('git describe --always --tags'));
        $data['git_last_update'] = trim(shell_exec('git log -1 --format=%cd --date=local'));
        $data['git_developer'] = preg_split('/[\r\n]+/', trim(shell_exec('git log --pretty="%an <a href="mailto:%ae"> %ae </a>" | sort | uniq')));

        //RAM usage
        $free = shell_exec('free');
        $free = (string) trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);
        $usedmem = $mem[2];
        $usedmemInGB = number_format($usedmem / 1048576, 2);
        $memory1 = $mem[2] / $mem[1] * 100;
        $memory = round($memory1) . '%';
        $fh = fopen('/proc/meminfo', 'r');
        $mem = 0;
        while ($line = fgets($fh)) {
            $pieces = array();
            if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
                $mem = $pieces[1];
                break;
            }
        }
        fclose($fh);
        $totalram = number_format($mem / 1048576, 2);

        //cpu usage
        $cpu_load = sys_getloadavg();
        $load = $cpu_load[0] . '% / 100%';
        $data['memory'] = $memory;
        $data['total_ram'] = $totalram;
        $data['memory_usage'] = $usedmemInGB;
        $data['cpu_load'] = $cpu_load[0];

        return view('cms.info.index', $data);
    }
}
