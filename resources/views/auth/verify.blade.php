<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Código - MyFinance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }
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
            <p class="mt-4 text-gray-400 text-lg">Segurança em primeiro lugar.</p>
        </div>
        <!-- Decorative Circle -->
        <div class="absolute w-96 h-96 bg-primary opacity-5 rounded-full blur-3xl -top-20 -left-20"></div>
        <div class="absolute w-64 h-64 bg-primary opacity-5 rounded-full blur-3xl bottom-10 right-10"></div>
    </div>

    <!-- Right Side (Form) -->
    <div class="w-full md:w-1/2 bg-white flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-900 mb-8 inline-flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Voltar
            </a>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Código de Verificação</h2>
                <p class="text-gray-500">Enviamos um código de 6 dígitos para <strong class="text-gray-900">{{ $email }}</strong>.</p>
            </div>

            <form action="{{ route('login.verify') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                
                <div>
                    <label for="code" class="block text-sm font-medium leading-6 text-gray-900">Código de acesso</label>
                    <div class="mt-2 relative">
                        <input id="code" name="code" type="text" inputmode="numeric" required autofocus
                            class="block w-full rounded-md border-0 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primaryDark sm:text-lg sm:leading-6 px-4 tracking-[0.5em] font-mono text-center"
                            placeholder="000000" maxlength="6">
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-md bg-dark px-3 py-3 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900 transition-all duration-200">
                        Acessar Sistema
                    </button>
                </div>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Não recebeu? <a href="{{ route('login') }}" class="font-semibold text-primaryDark hover:text-yellow-600">Tentar novamente</a>
            </p>
        </div>
    </div>
</body>
</html>