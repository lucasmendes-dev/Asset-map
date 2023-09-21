<x-app-layout>
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="p-8 md:p-12 text-center">
                <svg class="w-16 h-16 text-green-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <h1 class="text-3xl md:text-4xl font-semibold text-gray-900 dark:text-gray-100 mt-4">Seja bem-vindo, {{ Auth::user()->name }}!</h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 mt-2">Asset Map Team</p>
                <p class="text-gray-600 dark:text-gray-400 mt-6">Clique no botão abaixo para cadastrar seu primeiro ativo!</p>
                <div class="flex items-center justify-center">
                    <button data-modal-target="form-modal" data-modal-toggle="form-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 mt-5" type="button">
                        Cadastre seu primeiro ativo!
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


@include('assets.form-modal')

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modalButton = document.querySelector('[data-modal-toggle="form-modal"]');
        const modal = document.getElementById('form-modal');

        modalButton.addEventListener("click", function () {
            modal.classList.toggle('hidden');
        });

        const closeButton = document.querySelector('[data-modal-hide="form-modal"]');
        closeButton.addEventListener("click", function () {
            modal.classList.toggle('hidden');
        });
    });
</script>
