<?php

namespace App\Imports;

use App\Models\User;
use Throwable;
use Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Validators\Failure;

class UserImport implements ToModel, ShouldQueue, WithChunkReading, WithBatchInserts,  WithHeadingRow, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    public function model(array $row)
    {
        User::withTrashed()->updateOrCreate([
            'email' => strtolower($row['email'])
        ], [
            'password' => bcrypt('P@ssw0rd'),
            'email' => strtolower($row['email']),
            'fullname' => strtoupper($row['fullname']),
            'nip' => $row['nip'],
            'pernr' => $row['pernr'],
            'position' => $row['position'],
            'company' => $row['company'],
            'department' => $row['department'],
            'company_code' => $row['company_code'],
            'business_area' => $row['business_area'],
            'personnel_area' => $row['personnel_area'],
            'personnel_sub_area' => $row['personnel_sub_area'],
            'organization_code' => $row['organization_code'],
            'position_code' => $row['position_code']
        ]);
    }

    public function rules(): array
    {
        return [
            '*.email' => Rule::unique('cms_users', 'email'),
        ];
    }

    public function chunkSize(): int
    {
        return 200;
    }

    public function batchSize(): int
    {
        return 200;
    }

    /**
     * @param Throwable $e
     */
    public function onError(Throwable $e)
    {
        // $this->errors[] = $e;
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        // Handle the failures how you'd like.
    }
}
