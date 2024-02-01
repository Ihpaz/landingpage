<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class DatatablesCollectionExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithCustomValueBinder
{
    public function __construct(Collection $data, array $header)
    {
        $this->data = $data;
        $this->header = $header;
    }

    public function headings(): array
    {
        return collect($this->header)->pluck('title')->toArray();
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $column_name = $this->header;
        return collect($this->data)->map(function ($row) use ($column_name) {
            $results = [];
            foreach ($column_name as $column) {
                $title = $this->decodeContent($column['title']);
                $data  = $this->decodeContent(collect($row)->get($column['name']));
                $results[$title] = $data;
            }
            return $results;
        });
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }

    /**
     * Decode content to a readable text value.
     *
     * @param string $data
     * @return string
     */
    protected function decodeContent($data)
    {
        try {
            $decoded = html_entity_decode(strip_tags($data), ENT_QUOTES, 'UTF-8');

            return str_replace("\xc2\xa0", ' ', $decoded);
        } catch (\Exception $e) {
            return $data;
        }
    }
}
