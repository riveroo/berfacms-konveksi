<x-filament-panels::layout>
    {{-- Searchable Select Assets (Tom Select) --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <div class="px-8 py-8 mx-auto w-full max-w-2xl min-w-0" x-data="cashBookForm()">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('cash-book.index') }}" class="p-2 text-gray-500 hover:text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-900 dark:border-gray-800 dark:hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">{{ __('finance.record_transaction') }}</h2>
        </div>

        @if($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-danger-50 border border-danger-200">
            <ul class="list-disc list-inside text-sm text-danger-600">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('cash-book.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-6 space-y-6">
                
                {{-- Type Selection & Description Row --}}
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('finance.transaction_type') }}</label>
                        <select name="type" x-model="type" required class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="money_in">{{ __('finance.money_in') }}</option>
                            <option value="money_out">{{ __('finance.money_out') }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('finance.description') }}</label>
                        <textarea name="description" required rows="2" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="{{ __('finance.transaction_description_placeholder') }}">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('finance.date') }}</label>
                        <input type="date" name="date" x-model="date" required class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('finance.amount_rp') }}</label>
                        <input type="number" step="0.01" name="amount" x-model="amount" required class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="{{ __('finance.amount_placeholder') }}">
                    </div>
                </div>

                {{-- Searchable Select Fields --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('finance.account_cash_bank') }}</label>
                        <select name="account_id" id="account_id" x-model="accountId" required class="searchable-select">
                            <option value="">{{ __('finance.select_account') }}</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" x-text="type === 'money_in' || type === 'in' ? '{{ __('finance.category_income_source') }}' : (type === 'transfer' ? '{{ __('finance.destination_account_cash_bank') }}' : '{{ __('finance.category_expense_source') }}')"></label>
                        <select name="counter_account_id" id="counter_account_id" x-model="counterId" required class="searchable-select">
                            <option value="">{{ __('finance.select_category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->type }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Client & Receive From --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('finance.client_label') }}</label>
                        <select name="client_id" id="client_id" class="searchable-select">
                            <option value="">{{ __('finance.client_optional') }}</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->client_name }} ({{ ucfirst($client->type) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('finance.receive_from_pay_to') }}</label>
                        <input type="text" name="receive_from" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="{{ __('finance.receive_from_pay_to_placeholder') }}">
                    </div>
                </div>
            </div>

            {{-- PREVIEW WIDGET --}}
            <div x-show="isReadyForPreview()" class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 p-6" style="display: none;">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider mb-4 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    {{ __('finance.transaction_preview') }}
                </h4>
                
                <p class="text-gray-600 dark:text-gray-300 mb-3" x-html="'{{ __('finance.preview_instruction', ['type' => 'TYPE_PLACEHOLDER', 'amount' => 'AMOUNT_PLACEHOLDER']) }}'.replace('TYPE_PLACEHOLDER', '<strong class=&quot;text-gray-900 dark:text-white&quot;>' + (type === 'money_in' || type === 'in' ? '{{ __('finance.money_in') }}' : (type === 'transfer' ? 'Transfer' : '{{ __('finance.money_out') }}')) + '</strong>').replace('AMOUNT_PLACEHOLDER', '<strong class=&quot;text-gray-900 dark:text-white&quot;>Rp ' + formatRupiah(amount) + '</strong>')"></p>

                <div class="bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-800 p-4 font-mono text-sm space-y-2">
                    <template x-if="type === 'money_in' || type === 'in'">
                        <div>
                            <div class="flex justify-between"><span><span x-text="accountName"></span> ({{ __('finance.account_cash_bank') }})</span> <span class="text-success-600">{{ __('finance.preview_money_in') }}  ← <span x-text="formatRupiah(amount)"></span></span></div>
                            <div class="flex justify-between"><span><span x-text="counterName"></span> ({{ __('finance.category') }})</span> <span class="text-gray-500">{{ __('finance.preview_money_out') }} → <span x-text="formatRupiah(amount)"></span></span></div>
                        </div>
                    </template>
                    <template x-if="type === 'transfer'">
                        <div>
                            <div class="flex justify-between"><span><span x-text="accountName"></span> ({{ __('finance.coa_to') }})</span> <span class="text-success-600">{{ __('finance.preview_transfer_debit') }}  ← <span x-text="formatRupiah(amount)"></span></span></div>
                            <div class="flex justify-between"><span><span x-text="counterName"></span> ({{ __('finance.coa_from') }})</span> <span class="text-danger-600">{{ __('finance.preview_transfer_credit') }} → <span x-text="formatRupiah(amount)"></span></span></div>
                        </div>
                    </template>
                    <template x-if="type === 'money_out' || type === 'out'">
                        <div>
                            <div class="flex justify-between"><span><span x-text="counterName"></span> ({{ __('finance.category') }})</span> <span class="text-gray-500">{{ __('finance.preview_money_in') }}  ← <span x-text="formatRupiah(amount)"></span></span></div>
                            <div class="flex justify-between"><span><span x-text="accountName"></span> ({{ __('finance.account_cash_bank') }})</span> <span class="text-danger-600">{{ __('finance.preview_money_out') }} → <span x-text="formatRupiah(amount)"></span></span></div>
                        </div>
                    </template>
                </div>
                <p class="text-xs text-gray-500 mt-4">{{ __('finance.preview_auto_journal_note') }}</p>
            </div>

            <div class="flex justify-end gap-3">
                <x-button href="{{ route('cash-book.index') }}" variant="outline">{{ __('finance.cancel') }}</x-button>
                <x-button type="submit" variant="primary">{{ __('finance.save_transaction') }}</x-button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const settings = {
                create: false,
                maxItems: 1,
                plugins: [],
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                onInitialize: function() {
                    this.wrapper.classList.add('w-full');
                    this.control.classList.add('!border-gray-300', 'dark:!border-gray-700', 'dark:!bg-gray-900', '!rounded-lg', '!shadow-sm');
                }
            };

            const accountSelect = new TomSelect('#account_id', settings);
            const counterSelect = new TomSelect('#counter_account_id', settings);
            const clientSelect = new TomSelect('#client_id', settings);

            // Sync with Alpine
            accountSelect.on('change', function(val) {
                const alpine = document.querySelector('[x-data]').__x.$data;
                alpine.accountId = val;
                alpine.accountName = accountSelect.options[val] ? accountSelect.options[val].text : '';
            });

            counterSelect.on('change', function(val) {
                const alpine = document.querySelector('[x-data]').__x.$data;
                alpine.counterId = val;
                alpine.counterName = counterSelect.options[val] ? counterSelect.options[val].text : '';
            });
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('cashBookForm', () => ({
                type: 'money_out',
                date: new Date().toISOString().split('T')[0],
                amount: '',
                accountId: '',
                counterId: '',
                accountName: '',
                counterName: '',

                isReadyForPreview() {
                    return this.amount > 0 && this.accountId && this.counterId;
                },
                formatRupiah(angka) {
                    if(!angka) return '0';
                    return new Intl.NumberFormat('id-ID').format(angka);
                }
            }))
        })
    </script>

    <style>
        .ts-control {
            padding: 0.5rem 0.75rem !important;
            line-height: 1.5 !important;
        }
        .ts-wrapper.single .ts-control {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
            background-position: right 0.5rem center !important;
            background-repeat: no-repeat !important;
            background-size: 1.5em 1.5em !important;
            padding-right: 2.5rem !important;
        }
        .dark .ts-control {
            color: white !important;
            background-color: rgb(17, 24, 39) !important;
        }
        .dark .ts-dropdown {
            background-color: rgb(17, 24, 39) !important;
            color: white !important;
            border-color: rgb(31, 41, 55) !important;
        }
        .dark .ts-dropdown .active {
            background-color: rgb(31, 41, 55) !important;
            color: white !important;
        }
        .dark .ts-dropdown .option:hover {
            background-color: rgb(55, 65, 81) !important;
        }
    </style>
</x-filament-panels::layout>
