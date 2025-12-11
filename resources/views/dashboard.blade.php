@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h2 class="text-3xl font-light text-gray-800">Visão Geral</h2>
        <p class="text-gray-500 text-sm">Acompanhe seus indicadores financeiros</p>
    </div>
    
    <!-- Filtro de Data -->
    <form method="GET" class="w-full md:w-auto bg-white p-3 rounded shadow-sm flex flex-col md:flex-row items-center gap-2">
        <div class="flex items-center w-full md:w-auto gap-2">
            <input type="date" name="start_date" value="{{ $startDate }}" class="flex-1 border-gray-300 border rounded px-2 py-2 text-sm focus:border-primary focus:ring-primary">
            <span class="text-gray-400 hidden md:inline">-</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="flex-1 border-gray-300 border rounded px-2 py-2 text-sm focus:border-primary focus:ring-primary">
        </div>
        <button type="submit" class="w-full md:w-auto bg-dark text-white px-6 py-2 rounded text-sm hover:bg-gray-800 transition font-bold">Filtrar</button>
    </form>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
        <div class="text-gray-400 text-xs uppercase tracking-wide font-bold mb-1">Receita Total</div>
        <div class="text-2xl font-bold text-gray-800">R$ {{ number_format($income, 2, ',', '.') }}</div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-red-500">
        <div class="text-gray-400 text-xs uppercase tracking-wide font-bold mb-1">Despesa Total</div>
        <div class="text-2xl font-bold text-gray-800">R$ {{ number_format($expense, 2, ',', '.') }}</div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 {{ $balance >= 0 ? 'border-blue-500' : 'border-orange-500' }}">
        <div class="text-gray-400 text-xs uppercase tracking-wide font-bold mb-1">Saldo Líquido</div>
        <div class="text-2xl font-bold {{ $balance >= 0 ? 'text-blue-600' : 'text-orange-600' }}">R$ {{ number_format($balance, 2, ',', '.') }}</div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-primary">
        <div class="text-gray-400 text-xs uppercase tracking-wide font-bold mb-1">Maior Gasto Único</div>
        <div class="text-xl font-bold text-gray-800 truncate" title="{{ $biggestExpense->description ?? '-' }}">
            {{ $biggestExpense ? 'R$ ' . number_format($biggestExpense->amount, 2, ',', '.') : '-' }}
        </div>
        <div class="text-xs text-gray-500 truncate">{{ $biggestExpense->description ?? 'Sem dados' }}</div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Evolution Chart (Wide) -->
    <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm">
        <h3 class="font-bold text-gray-700 mb-6">Fluxo de Caixa (Evolução)</h3>
        <div class="h-64">
            <canvas id="flowChart"></canvas>
        </div>
    </div>

    <!-- Category Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="font-bold text-gray-700 mb-6 text-center">Despesas por Categoria</h3>
        <div class="h-48">
            <canvas id="expenseChart"></canvas>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="font-bold text-gray-700 mb-4">Top 5 Maiores Despesas</h3>
        <ul class="divide-y divide-gray-100">
            @forelse($topExpenses as $t)
                <li class="py-3 flex justify-between items-center">
                    <div>
                        <div class="text-sm font-bold text-gray-700">{{ $t->description }}</div>
                        <div class="text-xs text-gray-400">{{ $t->date->format('d/m/Y') }}</div>
                    </div>
                    <span class="text-red-500 font-mono font-medium">R$ {{ number_format($t->amount, 2, ',', '.') }}</span>
                </li>
            @empty
                <li class="py-3 text-gray-400 text-sm">Sem dados para o período.</li>
            @endforelse
        </ul>
    </div>
    
    <div class="bg-dark text-white p-6 rounded-lg shadow-sm flex flex-col justify-center items-center text-center">
        <h3 class="text-primary font-bold text-xl mb-2">Dica Financeira</h3>
        <p class="text-gray-400 text-sm max-w-xs">
            @if($balance < 0)
                Seu saldo está negativo. Tente revisar suas categorias de maior gasto listadas ao lado.
            @else
                Parabéns pelo saldo positivo! Considere investir 20% do excedente.
            @endif
        </p>
    </div>
</div>

<script>
    // Configurações Globais Chart.js
    Chart.defaults.font.family = "'Roboto', sans-serif";
    Chart.defaults.color = '#6b7280';

    const expenseCtx = document.getElementById('expenseChart').getContext('2d');
    const flowCtx = document.getElementById('flowChart').getContext('2d');

    const expenseData = @json($expensesByCategory);
    const flowData = @json($dailyFlow);

    // Doughnut
    new Chart(expenseCtx, {
        type: 'doughnut',
        data: {
            labels: expenseData.map(d => d.category_label),
            datasets: [{
                data: expenseData.map(d => d.total),
                backgroundColor: ['#FACC15', '#18181b', '#9ca3af', '#fbbf24', '#52525b', '#e5e7eb'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } }
            }
        }
    });

    // Line / Bar Combo
    new Chart(flowCtx, {
        type: 'bar',
        data: {
            labels: flowData.map(d => new Date(d.date).toLocaleDateString('pt-BR')),
            datasets: [
                { 
                    label: 'Receita', 
                    data: flowData.map(d => d.income), 
                    backgroundColor: '#10B981',
                    borderRadius: 4
                },
                { 
                    label: 'Despesa', 
                    data: flowData.map(d => d.expense), 
                    backgroundColor: '#EF4444',
                    borderRadius: 4
                }
            ]
        },
        options: { 
            responsive: true,
            maintainAspectRatio: false,
            scales: { 
                y: { grid: { borderDash: [2, 4] }, beginAtZero: true },
                x: { grid: { display: false } }
            },
            plugins: { legend: { position: 'top', align: 'end' } }
        }
    });
</script>
@endsection