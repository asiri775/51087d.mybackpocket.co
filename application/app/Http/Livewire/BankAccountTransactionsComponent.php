<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Helpers\Helper;
use App\Constants\StatementConstants;
use App\Models\BankAccountStatement;
use App\Models\Transaction;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\View\View;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

/**
 * @property BankAccountStatement|null $bankStatement
 */
class BankAccountTransactionsComponent extends DataTableComponent
{

    public $bankStatement;
    public $ids;

    public array $perPageAccepted = [25, 50, 100, 250];
    public bool $perPageAll = true;

    public bool $singleColumnSorting = true;

    public string $defaultSortColumn = 'id';
    public string $defaultSortDirection = 'desc';

    public array $bulkActions = [
        'update' => 'Update',
    ];

    public function renderTableFooterCustomView($view)
    {
        /**
         * @var View $view
         */
        $stats = [
            'this_page' => [
                'debits' => 0,
                'debits_total' => 0,
                'credits' => 0,
                'credits_total' => 0,
            ],
            'total' => [
                'debits' => 0,
                'debits_total' => 0,
                'credits' => 0,
                'credits_total' => 0,
            ]
        ];

        $allTransactionsQuery = Transaction::query();
        if ($this->bankStatement != null) {
            $allTransactionsQuery->where('bank_account_statement_id', $this->bankStatement->id);
        }
        if ($this->ids != null) {
            $allTransactionsQuery->whereIn('bank_account_statement_id' , explode(',', $this->ids));
        }

        $allTransactions = $allTransactionsQuery->get();
        if ($allTransactions != null) {
            foreach ($allTransactions as $transaction) {
                if ($transaction->is_debit == StatementConstants::DEBIT) {
                    $stats['total']['debits'] = $stats['total']['debits'] + 1;
                    $stats['total']['debits_total'] = $stats['total']['debits_total'] + $transaction->total;
                } else {
                    $stats['total']['credits'] = $stats['total']['credits'] + 1;
                    $stats['total']['credits_total'] = $stats['total']['credits_total'] + $transaction->total;
                }
            }
        }

        $pageTransactions = $view->getData()['rows'];
        $pageTransactions = json_encode($pageTransactions);
        $pageTransactions = json_decode($pageTransactions, true);
        $pageTransactions = $pageTransactions['data'];
        if (is_array($pageTransactions) && count($pageTransactions) > 0) {
            foreach ($pageTransactions as $transaction) {
                if ($transaction['is_debit'] == StatementConstants::DEBIT) {
                    $stats['this_page']['debits'] = $stats['this_page']['debits'] + 1;
                    $stats['this_page']['debits_total'] = $stats['this_page']['debits_total'] + $transaction['total'];
                } else {
                    $stats['this_page']['credits'] = $stats['this_page']['credits'] + 1;
                    $stats['this_page']['credits_total'] = $stats['this_page']['credits_total'] + $transaction['total'];
                }
            }
        }

        return view('bank-statements.totals-custom-view', compact('stats'));
    }

    public function renderTableHeaderCustomView($view)
    {
        /**
         * @var View $view
         */
        $selectionKeys = $this->selectedKeys();
        return view('bank-statements.table-top-custom-view', compact('selectionKeys'));
    }

    public function renderToView()
    {
        /**
         * @var View $view
         */
        $view = parent::renderToView();
        $view->with('footerCustomView', $this->renderTableFooterCustomView($view));
        $view->with('headerCustomView', $this->renderTableHeaderCustomView($view));
        /*dd($this->selectedKeys());
        dd($view);*/
        return $view;
    }

    public function render()
    {
        return parent::render();
    }

    public $columnSearch = [
        'particular' => null,
        'narration' => null,
        'status' => null,
        'category_id' => null,
        'vendor_id' => null,
        'is_debit' => null,
    ];

    public function columns(): array
    {
        return [
            Column::make('Transaction Date', 'transaction_date')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return Helper::displayDate($value);
                }),
            Column::make('Posting Date', 'posting_date')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return Helper::displayDate($value);
                }),
            Column::make('particular')
                ->sortable()
                ->searchable(),
            Column::make('Narration', 'order_no')
                ->sortable()
                ->searchable(),
            Column::make('Status', 'status')
                ->sortable()
                ->searchable()
                ->asHtml()
                ->format(function ($value) {
                    $html = '';
                    switch ($value) {
                        case StatementConstants::TRANSACTION_PENDING:
                            $html = '<span class="text-primary">Pending</span>';
                            break;
                        case StatementConstants::TRANSACTION_CONFIRMED:
                            $html = '<span class="text-success">Confirmed</span>';
                            break;
                    }
                    return $html;
                }),
            Column::make('Category', 'category_id')
                ->sortable()
                ->searchable()
                ->format(function ($value, $column, $row) {
                    if ($row->category == null) {
                        return 'Uncategorized';
                    }
                    return $row->category->name;
                }),
            Column::make('vendor', 'vendor_id')
                ->sortable()
                ->searchable()
                ->format(function ($value, $column, $row) {
                    if ($row->vendor == null) {
                        return '-';
                    }
                    return $row->vendor->name;
                }),
            Column::make('Type', 'is_debit')
                ->sortable()
                ->searchable()
                ->asHtml()
                ->format(function ($value) {
                    $html = '';
                    switch ($value) {
                        case StatementConstants::DEBIT:
                            $html = '<span class="badge badge-warning">Debit</span>';
                            break;
                        case StatementConstants::CREDIT:
                            $html = '<span class="badge badge-success">Credit</span>';
                            break;
                    }
                    return $html;
                }),
            Column::make('Amount', 'total')
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return Helper::printAmount($value);
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
        $query = Transaction::query();

        if ($this->bankStatement != null) {
            $query->where('bank_account_statement_id', $this->bankStatement->id);
        }

        if ($this->ids != null) {
            $query->whereIn('bank_account_statement_id' , explode(',', $this->ids));
        }


        $query->when($this->columnSearch['particular'] ?? null, fn ($query, $particular) => $query->where('particular', 'like', '%' . $particular . '%'));
        $query->when($this->columnSearch['narration'] ?? null, fn ($query, $narration) => $query->where('narration', 'like', '%' . $narration . '%'));

        $query->when($this->columnSearch['status'] ?? null, fn ($query, $status) => $query->where('status', '=', $status));
        $query->when($this->columnSearch['category_id'] ?? null, fn ($query, $category_id) => $query->where('category_id', '=', $category_id));
        $query->when($this->columnSearch['vendor_id'] ?? null, fn ($query, $vendor_id) => $query->where('vendor_id', '=', $vendor_id));

        $query->when($this->columnSearch['is_debit'] ?? null, function ($query, $is_debit) {
            if ($is_debit == 'debit') {
                $query = $query->where('is_debit', StatementConstants::DEBIT);
            } else {
                $query = $query->where('is_debit', StatementConstants::CREDIT);
            }
            return $query;
        });

        return $query;
    }


    public function update()
    {

    }


}
