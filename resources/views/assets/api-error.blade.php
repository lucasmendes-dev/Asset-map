<x-app-layout>
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="p-8 md:p-12 text-center">
                <svg class="w-16 h-16 text-red-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <h1 class="text-3xl md:text-4xl font-semibold text-gray-900 dark:text-gray-100 mt-4">Erro 504</h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 mt-2">Gateway Timeout</p>
                <p class="text-gray-600 dark:text-gray-400 mt-6">Desculpe-nos, mas a página que você está tentando acessar demorou muito para responder.</p>
                <p class="text-gray-600 dark:text-gray-400">Isso pode ocorrer devido a problemas temporários no servidor ou à sobrecarga.</p>
                <p class="text-gray-600 dark:text-gray-400 mt-6">Por favor, tente novamente mais tarde.</p>
                <div class="mt-8">
                    <a href="{{ url('/dashboard') }}" class="text-blue-500 hover:underline">Voltar para a página inicial</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
