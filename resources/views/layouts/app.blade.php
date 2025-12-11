<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFinance BI</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Google Fonts: Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Roboto', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FACC15', // Yellow 400
                        primaryDark: '#EAB308', // Yellow 500
                        dark: '#18181b', // Zinc 900
                        darker: '#09090b', // Zinc 950
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 text-gray-800 flex flex-col md:flex-row h-screen">

    @auth
    <!-- Mobile Sidebar Backdrop & Menu -->
    <div x-data="{ open: false }" class="md:hidden w-full flex-shrink-0">
        <!-- Mobile Header (Updated with Menu Button) -->
        <header class="bg-dark text-white p-4 flex justify-between items-center z-30 relative shadow-md w-full">
            <div class="flex items-center">
                <button @click="open = !open" class="text-white focus:outline-none mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <span class="font-bold text-primary text-xl tracking-wider">MY<span class="text-white">FINANCE</span></span>
            </div>
            <!-- Profile/Logout Mini -->
            <form action="{{ route('logout') }}" method="POST" class="flex items-center">
                @csrf
                <button class="text-gray-400 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg></button>
            </form>
        </header>

        <!-- Slide-over Menu -->
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="-translate-x-full opacity-0"
             class="fixed inset-0 z-40 bg-dark/95 backdrop-blur-sm pt-20 px-6"
             style="display: none;"> <!-- Setup for x-show -->
            
            <nav class="space-y-6">
                <a href="{{ route('dashboard') }}" class="block text-xl font-medium text-white hover:text-primary border-b border-gray-800 pb-2">Dashboard BI</a>
                <a href="{{ route('transactions.index') }}" class="block text-xl font-medium text-white hover:text-primary border-b border-gray-800 pb-2">Lançamentos</a>
                <a href="{{ route('transactions.create') }}" class="block text-xl font-medium text-white hover:text-primary border-b border-gray-800 pb-2">Novo Lançamento</a>
                <button @click="open = false" class="mt-8 w-full py-3 bg-gray-800 text-gray-400 rounded-lg">Fechar Menu</button>
            </nav>
        </div>
    </div>

    <!-- Desktop Sidebar -->
    <aside class="hidden md:flex w-64 bg-dark text-white flex-col shadow-2xl z-20 h-screen fixed top-0 left-0 overflow-y-auto">
        <div class="h-16 flex items-center justify-center border-b border-gray-800 shrink-0">
            <h1 class="text-2xl font-bold tracking-wider text-primary">MY<span class="text-white">FINANCE</span></h1>
        </div>
        
        <nav class="flex-1 py-6 space-y-2 px-4">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded hover:bg-gray-800 transition {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-primary border-r-4 border-primary' : 'text-gray-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="font-medium">Dashboard BI</span>
            </a>
            
            <a href="{{ route('transactions.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded hover:bg-gray-800 transition {{ request()->routeIs('transactions.index') ? 'bg-gray-800 text-primary border-r-4 border-primary' : 'text-gray-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span class="font-medium">Lançamentos</span>
            </a>

            <a href="{{ route('transactions.create') }}" class="flex items-center space-x-3 px-4 py-3 rounded hover:bg-gray-800 transition {{ request()->routeIs('transactions.create') ? 'bg-gray-800 text-primary border-r-4 border-primary' : 'text-gray-400' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span class="font-medium">Novo Lançamento</span>
            </a>
        </nav>

        <div class="p-4 border-t border-gray-800">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="flex items-center space-x-3 text-gray-400 hover:text-white w-full transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Sair</span>
                </button>
            </form>
        </div>
    </aside>
    @endauth

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col md:ml-64 transition-all duration-300">
        
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4 md:p-8">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex justify-between items-center">
                    <div>{{ session('success') }}</div>
                    <button @click="show = false" class="text-green-700 font-bold">&times;</button>
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>

</body>
</html>