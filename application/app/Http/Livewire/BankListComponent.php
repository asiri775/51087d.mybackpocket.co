<?php

namespace App\Http\Livewire;

use App\Helpers\Helper;
use App\Models\Bank;
use Illuminate\Database\Eloquent\Builder;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;


class BankListComponent extends DataTableComponent
{

    public array $perPageAccepted = [25, 50, 100, 250];
    public bool $perPageAll = true;

    public bool $singleColumnSorting = true;

    public string $defaultSortColumn = 'id';
    public string $defaultSortDirection = 'desc';

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Code', 'code')
                ->sortable()
                ->searchable(),
            Column::make('Swift Code', 'swift_code')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return Helper::IfNullSlash($value);
                }),
            Column::make('Created On', 'created_at')
                ->sortable()
                ->format(function ($value) {
                    return Helper::displayDateTime($value);
                }),
            Column::make('Updated On', 'updated_at')
                ->sortable()
                ->format(function ($value) {
                    return Helper::displayDateTime($value);
                }),
            Column::make('Actions', 'id')
                ->asHtml()
                ->format(function ($value) {
                    return Helper::getActionButtons([
                        'edit' => ['url' => 'javascript:;', 'class' => 'editRecord', 'dataAttributes' => [
                            'href' => route('banks.edit', $value)
                        ]],
                        'delete' => ['url' => route('banks.destroy', $value), 'is_form' => true],
                    ]);
                }),
        ];
    }

    public function filters(): array
    {
        return [

        ];
    }

    public function query(): Builder
    {
        return Bank::query();
    }


}


