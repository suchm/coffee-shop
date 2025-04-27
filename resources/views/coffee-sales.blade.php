<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New ☕️ Sales') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-8">

                        <!-- Add data form -->
                        <form>
                            @csrf

                            <div class="flex justify-between">
                                <!-- Quantity and Unit Cost Fields -->
                                <div class="flex gap-4">
                                    <!-- Quantity Input -->
                                    <div class="w-40">
                                        <x-label for="quantity" value="Quantity" />
                                        <x-input type="text" id="quantity" class="w-full" name="quantity" placeholder="0" value="{{ old('quantity') }}" required />
                                    </div>

                                    <!-- Unit Cost Input -->
                                    <div class="w-40">
                                        <x-label for="unit_cost" value="Unit Cost (£)" />
                                        <x-input type="text" step="0.01" id="unit-cost" class="w-full" name="unit-cost" placeholder="00.00" value="{{ old('unit_cost') }}" required />
                                    </div>

                                    <!-- Selling Price -->
                                    <div class="w-40 flex flex-col justify-end">
                                        <div id="selling-price" class="flex items-center bg-gray-100 w-full p-6 rounded-md" style="max-height: 30px;">
                                            <div class="selling-price-spinner hidden">
                                                <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                                                </svg>
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <span class="selling-price-output font-semibold"></span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col justify-end">
                                        <!-- Submit Button -->
                                        <div class="">
                                            <x-button id="record-sale-btn">
                                                Record Sale
                                            </x-button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </form>

                        <!-- Error display -->
                        <div id="error-message" class="text-red-500 mt-2"></div>
                    </div>

                    <!-- Sales Table -->
                    <h2 class="font-semibold text-2xl mb-4">Previous Sales</h2>
                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100">
                                Quantity
                            </th>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100">
                                Unit Cost (£)
                            </th>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100">
                                Selling Price (£)
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($sales as $sale)
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">
                                    {{ $sale->quantity }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    £{{ number_format($sale->unit_cost, 2) }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    £{{ number_format($sale->selling_price, 2) }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
