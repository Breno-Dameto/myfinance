@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-light text-gray-800">Novo Lançamento</h2>
        <a href="{{ route('transactions.index') }}" class="text-sm text-gray-500 hover:text-dark">Voltar</a>
    </div>

    <div class="bg-white p-8 rounded-lg shadow-sm border-t-4 border-primary pb-20 md:pb-8">
        <form action="{{ route('transactions.store') }}" method="POST" x-data="{ 
            categoryMode: 'existing', 
            txnType: 'expense'
        }" class="relative">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Descrição -->
                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700">Descrição</label>
                    <input type="text" name="description" required class="w-full bg-gray-50 border border-gray-200 rounded p-3 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition" placeholder="Ex: Mercado, Salário...">
                </div>
                
                <!-- Valor -->
                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700">Valor (R$)</label>
                    <input type="number" step="0.01" name="amount" required class="w-full bg-gray-50 border border-gray-200 rounded p-3 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition" placeholder="0.00">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Tipo -->
                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700">Tipo</label>
                    <select name="type" x-model="txnType" class="w-full bg-gray-50 border border-gray-200 rounded p-3 focus:outline-none focus:border-primary transition">
                        <option value="expense">Despesa</option>
                        <option value="income">Receita</option>
                    </select>
                </div>

                <!-- Data -->
                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700">Data</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full bg-gray-50 border border-gray-200 rounded p-3 focus:outline-none focus:border-primary transition">
                </div>

                <!-- Lógica de Categoria Complexa -->
                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700">Categoria</label>
                    <select name="category_select" x-model="categoryMode" class="w-full bg-gray-50 border border-gray-200 rounded p-3 focus:outline-none focus:border-primary transition mb-2">
                        <optgroup label="Minhas Categorias">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Opções">
                            <option value="new_custom">+ Nova Categoria (Criar)</option>
                            <option value="temp_other">Outros (Digitar Temporário)</option>
                        </optgroup>
                    </select>

                    <!-- Input Condicional: Nova Categoria -->
                    <div x-show="categoryMode == 'new_custom'" class="mt-2">
                        <input type="text" name="new_category_name" class="w-full text-sm border-b-2 border-primary bg-yellow-50 p-2 focus:outline-none" placeholder="Nome da Nova Categoria...">
                    </div>

                    <!-- Input Condicional: Temporário -->
                    <div x-show="categoryMode == 'temp_other'" class="mt-2">
                        <input type="text" name="temp_category_name" class="w-full text-sm border-b-2 border-gray-400 bg-gray-50 p-2 focus:outline-none" placeholder="Digite a etiqueta...">
                    </div>
                </div>
            </div>

            <!-- Botão de Salvar Fixo para Mobile -->
            <div class="md:relative fixed bottom-0 left-0 right-0 p-4 bg-white border-t md:border-t-0 shadow-lg md:shadow-none z-10">
                <button type="submit" class="w-full bg-dark text-white font-bold py-3 rounded hover:bg-gray-800 transition shadow-lg">
                    Salvar Lançamento
                </button>
            </div>
        </form>
    </div>
</div>
@endsection