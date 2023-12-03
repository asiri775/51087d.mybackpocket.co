<?php

namespace App\Http\Livewire;

use App\Helpers\Helper;
use App\Models\Bank;
use App\Models\BankAccount;
use Illuminate\Database\Eloquent\Builder;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;


class BankAccountListComponent extends DataTableComponent
{

    public array $perPageAccepted = [25, 50, 100, 250];
    public bool $perPageAll = true;

    public bool $singleColumnSorting = true;

    public string $defaultSortColumn = 'id';
    public string $defaultSortDirection = 'desc';

    public function columns(): array
    {
        return [
            Column::make('Bank', 'bank_id')
                ->searchable()
                ->format(function ($value, $column, $row) {
                    return $row->bank->name;
                }),
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Alias', 'alias')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return Helper::IfNullSlash($value);
                }),
            Column::make('Account No.', 'account_number')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return Helper::IfNullSlash($value);
                }),
            Column::make('Transit No.', 'transit_number')
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
                            'href' => route('bank-accounts.edit', $value)
                        ]],
                        'delete' => ['url' => route('bank-accounts.destroy', $value), 'is_form' => true],
                    ]);
                }),
        ];
    }

    public function filters(): array
    {
        $banks = [];
        $banks[''] = 'All';
        $bankList = Bank::all();
        if ($bankList != null) {
            foreach ($bankList as $bank) {
                $banks[$bank->id] = $bank->name;
            }
        }
        return [
            'bank_id' => Filter::make('Bank')
                ->select($banks),
        ];
    }

    public function query(): Builder
    {
        return BankAccount::query()
            ->when($this->getFilter('bank_id'), fn ($query, $bank_id) => $query->where('bank_id', $bank_id));
    }


}


