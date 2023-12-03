<?php

namespace App\Http\Livewire;

use App\Helpers\Helper;
use App\Constants\StatementConstants;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\BankAccountStatement;
use App\Models\BankAccountTransaction;
use Illuminate\Database\Eloquent\Builder;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;


class BankAccountStatementsComponent extends DataTableComponent
{

    public bool $singleColumnSorting = true;

    public string $defaultSortColumn = 'id';
    public string $defaultSortDirection = 'desc';

    public array $perPageAccepted = [25, 50, 100, 250];
    public bool $perPageAll = true;

    public array $bulkActions = [
        'view' => 'View',
        'pdf' => 'PDF',
        'xls' => 'XLS',
        'delete' => 'Delete',
    ];

    public function view()
    {
        $keys = $this->selectedKeys();
        if (is_array($keys) && count($keys) > 0) {
            $keys = implode(',', $keys);
            return redirect()->route('bankStatements.listTransactions', $keys);
        }
    }

    public function delete()
    {
        $keys = $this->selectedKeys();
        foreach ($keys as $id) {
            $statement = BankAccountStatement::where('id', $id)->first();
            if ($statement != null) {
                $statement->delete();
            }
        }
        $this->resetAll();
        return true;
    }

    public function pdf()
    {
        return true;
    }

    public function xls()
    {
        return true;
    }

    public function columns(): array
    {
        return [
            Column::make('Bank', 'bank_id')
                ->searchable()
                ->format(function ($value, $column, $row) {
                    return $row->bank->name;
                }),
            Column::make('Bank Account', 'bank_account_id')
                ->searchable()
                ->format(function ($value, $column, $row) {
                    return $row->bankAccount->name;
                }),
            Column::make('Name', 'name')
                ->sortable()
                ->searchable()
                ->asHtml()
                ->format(function ($value, $column, $row) {
                    return '<a href="'.route('bankStatements.showTransactions', $row->id).'">'.$value.'</a>';
                }),
            Column::make('status')
                ->sortable()
                ->searchable()
                ->asHtml()
                ->format(function ($value) {
                    $html = '';
                    switch ($value) {
                        case StatementConstants::PENDING:
                            $html = '<span class="badge badge-primary">Pending</span>';
                            break;
                        case StatementConstants::PROCESSING:
                            $html = '<span class="badge badge-warning">Processing</span>';
                            break;
                        case StatementConstants::FAILED:
                            $html = '<span class="badge badge-danger">Failed</span>';
                            break;
                        case StatementConstants::COMPLETED:
                            $html = '<span class="badge badge-success">Completed</span>';
                            break;
                    }
                    return $html;
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
                ->format(function ($value, $column, $row) {
                    return Helper::getActionButtons([
                        'edit' => ['url' => 'javascript:;', 'class' => 'editRecord', 'dataAttributes' => [
                            'href' => route('bankStatements.editStatement', ['id' => $value]),
                            'bank_account_id' => $row->bank_account_id,
                            'name' => $row->name,
                        ]],
                        'view' => ['url' => route('bankStatements.showTransactions', $value)],
                        'delete' => ['url' => route('bankStatements.deleteStatement', $value)],
                    ]);
                }),
        ];
    }

    public function filters(): array
    {
        $bankAccounts = [];
        $bankAccounts[''] = 'All';
        $bankAccountList = BankAccount::all();
        if ($bankAccountList != null) {
            foreach ($bankAccountList as $bankAccount) {
                $bankAccounts[$bankAccount->id] = $bankAccount->name;
            }
        }

        $banks = [];
        $banks[''] = 'All';
        $banksList = Bank::all();
        if ($banksList != null) {
            foreach ($banksList as $bank) {
                $banks[$bank->id] = $bank->name;
            }
        }

        return [
            'status' => Filter::make('Status')
                ->select([
                    '' => 'Any',
                    StatementConstants::PENDING => 'Pending',
                    StatementConstants::PROCESSING => 'Processing',
                    StatementConstants::FAILED => 'Failed',
                    StatementConstants::COMPLETED => 'Completed',
                ]),
            'bank_id' => Filter::make('Bank')
                ->select($banks),
            'bank_account_id' => Filter::make('Bank Account')
                ->select($bankAccounts),
        ];
    }

    public function query(): Builder
    {
        return BankAccountStatement::query()
            ->when($this->getFilter('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($this->getFilter('bank_id'), fn ($query, $bank_id) => $query->where('bank_id', $bank_id))
            ->when($this->getFilter('bank_account_id'), fn ($query, $bank_account_id) => $query->where('bank_account_id', $bank_account_id));
    }


}


