@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <h2 class="text-2xl font-light text-gray-800">Lançamentos</h2>
    
    <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
        <a href="{{ route('transactions.export.xlsx') }}" class="flex items-center justify-center space-x-2 bg-green-600 border border-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>Excel</span>
        </a>
        <a href="{{ route('transactions.export.csv') }}" class="flex items-center justify-center space-x-2 bg-white border border-gray-300 text-gray-600 px-4 py-2 rounded hover:bg-gray-50 text-sm transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>CSV</span>
        </a>
        <a href="{{ route('transactions.create') }}" class="flex items-center justify-center space-x-2 bg-dark text-white px-4 py-2 rounded hover:bg-gray-800 text-sm font-bold shadow-md transition">
            <span>+ Novo Lançamento</span>
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white p-4 rounded-lg shadow-sm mb-6">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Início</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border rounded p-2 text-sm bg-gray-50 focus:ring-primary focus:border-primary">
        </div>
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Fim</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border rounded p-2 text-sm bg-gray-50 focus:ring-primary focus:border-primary">
        </div>
        <div>
            <label class="text-xs font-bold text-gray-500 uppercase">Categoria</label>
            <select name="category_id" class="w-full border rounded p-2 text-sm bg-gray-50 focus:ring-primary focus:border-primary">
                <option value="">Todas</option>
                <option value="others" {{ request('category_id') == 'others' ? 'selected' : '' }}>Outros (Temp)</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="w-full bg-primaryDark text-white font-bold py-2 rounded hover:bg-yellow-600 transition text-sm shadow-sm">Filtrar</button>
        </div>
    </form>
</div>

<!-- Tabela -->
<div class="bg-white shadow-lg rounded-lg border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]"> <!-- min-w ensure horizontal scroll on tiny screens -->
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
            <tr>
                <th class="p-4 font-medium">Data</th>
                <th class="p-4 font-medium">Descrição</th>
                <th class="p-4 font-medium">Categoria</th>
                <th class="p-4 font-medium text-right">Valor</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($transactions as $t)
                <tr class="hover:bg-yellow-50 transition duration-150">
                    <td class="p-4 text-gray-600 text-sm">{{ $t->date->format('d/m/Y') }}</td>
                    <td class="p-4 font-medium text-gray-800">{{ $t->description }}</td>
                    <td class="p-4">
                        <span class="px-2 py-1 text-xs font-bold rounded-full {{ $t->category_id ? 'bg-gray-200 text-gray-700' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $t->category_name }}
                        </span>
                    </td>
                    <td class="p-4 text-right font-mono font-medium {{ $t->type == 'income' ? 'text-green-600' : 'text-red-500' }}">
                        {{ $t->type == 'expense' ? '-' : '+' }} R$ {{ number_format($t->amount, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-gray-400 italic">Nenhum lançamento encontrado para este período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 bg-gray-50 border-t border-gray-100">
        {{ $transactions->links() }}
    </div>
</div>
@endsection