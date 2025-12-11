<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Filtros de Data
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $query = $user->transactions()
            ->whereBetween('date', [$startDate, $endDate]);

        // KPIs (Blocos)
        $income = (clone $query)->where('type', 'income')->sum('amount');
        $expense = (clone $query)->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;
        
        // Maior Despesa Única
        $biggestExpense = (clone $query)->where('type', 'expense')->orderBy('amount', 'desc')->first();

        // Gráfico 1: Categorias (Pizza)
        $expensesByCategory = (clone $query)
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.type', 'expense')
            ->select(
                DB::raw("COALESCE(categories.name, transactions.temp_category, 'Outros') as category_label"),
                DB::raw('sum(transactions.amount) as total')
            )
            ->groupBy('categories.name', 'transactions.temp_category')
            ->get();

        // Gráfico 2: Evolução Diária (Linha) - BI Style
        $dailyFlow = (clone $query)
            ->select('date', 
                DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income"),
                DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense")
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Gráfico 3: Top 5 Maiores Gastos (Barra Horizontal)
        $topExpenses = (clone $query)
            ->where('type', 'expense')
            ->orderBy('amount', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'income', 'expense', 'balance', 'biggestExpense',
            'expensesByCategory', 'dailyFlow', 'topExpenses',
            'startDate', 'endDate'
        ));
    }
}