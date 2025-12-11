<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyFinance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                    }
                }
            }
        }
    </script>
</head>
<body class="h-screen flex overflow-hidden">
    <!-- Left Side (Brand) -->
    <div class="hidden md:flex md:w-1/2 bg-dark items-center justify-center relative">
        <div class="z-10 text-center">
            <h1 class="text-5xl font-bold tracking-wider text-primary">MY<span class="text-white">FINANCE</span></h1>
            <p class="mt-4 text-gray-400 text-lg">Gerencie suas finanças com simplicidade.</p>
        </div>
        <!-- Decorative Circle -->
        <div class="absolute w-96 h-96 bg-primary opacity-5 rounded-full blur-3xl -top-20 -left-20"></div>
        <div class="absolute w-64 h-64 bg-primary opacity-5 rounded-full blur-3xl bottom-10 right-10"></div>
    </div>

    <!-- Right Side (Form) -->
    <div class="w-full md:w-1/2 bg-white flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            @if(session('info'))
                <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded shadow-sm text-sm">
                    {{ session('info') }}
                </div>
            @endif

            <div class="mb-10">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Bem-vindo(a)</h2>
                <p class="text-gray-500">Informe seu e-mail para acessar sua conta.</p>
            </div>

            <form action="{{ route('login.send') }}" method="POST" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Endereço de e-mail</label>
                    <div class="mt-2">
                        <input id="email" name="email" type="email" autocomplete="email" required 
                            value="{{ old('email') }}"
                            class="block w-full rounded-md border-0 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primaryDark sm:text-sm sm:leading-6 px-4"
                            :class="{ 'opacity-75 cursor-not-allowed bg-gray-50': loading }" :readonly="loading"
                            placeholder="seu@email.com">
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="flex w-full justify-center rounded-md bg-dark px-3 py-3 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900 transition-all duration-200"
                            :class="{ 'opacity-75 cursor-not-allowed pointer-events-none': loading }">
                        <span x-show="!loading">Enviar Código de Acesso</span>
                        <span x-show="loading" x-cloak class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Enviando...
                        </span>
                    </button>
                </div>
            </form>

            <p class="mt-10 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} MyFinance. Todos os direitos reservados.
            </p>
        </div>
    </div>
</body>
</html>