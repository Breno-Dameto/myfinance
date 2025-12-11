<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

$user = User::first();
Auth::login($user); 

$startDate = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
$endDate = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');

dump("Date Range: $startDate to $endDate");

// Mimic Controller Logic
$query = $user->transactions()
    ->whereBetween('date', [$startDate, $endDate]);

$expensesByCategory = (clone $query)
    ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
    ->where('transactions.type', 'expense')
    ->select(
        DB::raw("COALESCE(categories.name, transactions.temp_category, 'Outros') as category_name"),
        DB::raw('sum(transactions.amount) as total')
    )
    ->groupBy('categories.name', 'transactions.temp_category')
    ->get();

dump("--- Expenses By Category Result ---");
foreach($expensesByCategory as $row) {
    dump((array)$row); // Cast to array to see properties clearly
    // The result of get() is a Collection of stdClass or Models?
    // Since we used select() with DB::raw, it returns models but with dynamic attributes?
    // Or stdClass if we didn't start from a Model builder?
    // We started from $user->transactions() (Relation), so it returns Transaction models.
    dump("Attributes: ", $row->getAttributes());
}

dump("--- SQL Debug ---");
$sql = (clone $query)
    ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
    ->where('transactions.type', 'expense')
    ->select(
        DB::raw("COALESCE(categories.name, transactions.temp_category, 'Outros') as category_name"),
        DB::raw('sum(transactions.amount) as total')
    )
    ->groupBy('categories.name', 'transactions.temp_category')
    ->toSql();
dump($sql);
dump((clone $query)->getBindings());