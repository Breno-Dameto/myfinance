<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->transactions()->with('category')->latest('date');

        // Filtros
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } else {
            // Padrão: Mês atual se não filtrar
            // $query->whereMonth('date', Carbon::now()->month); 
            // Comentado para mostrar tudo por padrão ou usar logica de frontend
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            if ($request->category_id == 'others') {
                $query->whereNull('category_id');
            } else {
                $query->where('category_id', $request->category_id);
            }
        }

        $transactions = $query->paginate(15)->withQueryString();
        $categories = Auth::user()->categories; // Para o filtro
        
        return view('transactions.index', compact('transactions', 'categories'));
    }

    public function create()
    {
        $categories = Auth::user()->categories;
        return view('transactions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category_select' => 'required', // ID ou 'new_custom' ou 'temp_other'
            'new_category_name' => 'nullable|required_if:category_select,new_custom|string|max:50',
            'temp_category_name' => 'nullable|required_if:category_select,temp_other|string|max:50',
            'type' => 'required|in:income,expense'
        ]);

        $transactionData = [
            'description' => $data['description'],
            'amount' => $data['amount'],
            'date' => $data['date'],
            'type' => $data['type'],
            'user_id' => Auth::id()
        ];

        if ($data['category_select'] === 'new_custom') {
            // Criar nova categoria permanente
            $cat = Auth::user()->categories()->create([
                'name' => $data['new_category_name'],
                'type' => $data['type']
            ]);
            $transactionData['category_id'] = $cat->id;
        } 
        elseif ($data['category_select'] === 'temp_other') {
            // Categoria temporária (string na transação)
            $transactionData['category_id'] = null;
            $transactionData['temp_category'] = $data['temp_category_name'];
        } 
        else {
            // Categoria existente
            $transactionData['category_id'] = $data['category_select'];
        }

        Transaction::create($transactionData);

        return redirect()->route('transactions.index')->with('success', 'Lançamento salvo com sucesso!');
    }

    public function exportCsv(Request $request)
    {
        // Replicar lógica de filtro se necessário, ou exportar tudo
        $transactions = Auth::user()->transactions()->latest('date')->get();
        
        $fileName = 'transacoes_'.date('Ymd').'.csv';
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Expires" => "0"
        ];

        $callback = function() use($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Data', 'Descrição', 'Categoria', 'Tipo', 'Valor']);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->id, 
                    $t->date->format('Y-m-d'), 
                    $t->description, 
                    $t->category_name, // Usa o acessor
                    $t->type, 
                    $t->amount
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportXlsx()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\TransactionsExport, 'transacoes_'.date('Ymd').'.xlsx');
    }
}