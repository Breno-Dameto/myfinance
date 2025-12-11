<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Auth::user()->transactions()->latest('date')->with('category')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Data',
            'Descrição',
            'Categoria',
            'Tipo',
            'Valor (R$)',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->date->format('d/m/Y'),
            $transaction->description,
            $transaction->category_name, // Accessor
            $transaction->type == 'income' ? 'Receita' : 'Despesa',
            $transaction->amount,
        ];
    }
}
