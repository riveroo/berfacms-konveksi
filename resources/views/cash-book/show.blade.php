<x-filament-panels::layout>
    <div class="px-8 py-8 mx-auto w-full max-w-4xl min-w-0">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('cash-book.index') }}" class="p-2 text-gray-500 hover:text-gray-900 bg-white border border-gray-200 rounded-lg dark:bg-gray-900 dark:border-gray-800 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                    {{ app()->getLocale() === 'id' ? 'Detail Transaksi' : 'Transaction Detail' }}
                </h2>
            </div>
            
            <div class="flex gap-2">
                @if($cashBook->reference_type !== 'transfer' && $cashBook->date->format('Y-m') === now()->format('Y-m'))
                <a href="{{ route('cash-book.edit', $cashBook->id) }}" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    {{ app()->getLocale() === 'id' ? 'Ubah' : 'Edit' }}
                </a>
                @endif
                <form action="{{ route('cash-book.destroy', $cashBook->id) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() === 'id' ? 'Apakah Anda yakin ingin menghapus transaksi ini?' : 'Are you sure you want to delete this transaction?' }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-danger-600 text-white rounded-lg hover:bg-danger-500">
                        {{ app()->getLocale() === 'id' ? 'Hapus' : 'Delete' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Transaction Info --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-800">
                    {{ app()->getLocale() === 'id' ? 'Ringkasan' : 'Overview' }}
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <span class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ app()->getLocale() === 'id' ? 'Tanggal' : 'Date' }}
                        </span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $cashBook->date->format('d M Y') }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ app()->getLocale() === 'id' ? 'Jumlah' : 'Amount' }}
                        </span>
                        <span class="text-xl font-bold {{ in_array($cashBook->type, ['money_in', 'in']) ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                            Rp {{ number_format($cashBook->amount, 0, ',', '.') }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ app()->getLocale() === 'id' ? 'Tipe' : 'Type' }}
                        </span>
                        @if($cashBook->reference_type === 'transfer')
                            <span class="inline-block px-2 py-1 text-xs font-semibold bg-primary-100 text-primary-800 rounded-full dark:bg-primary-900/30 dark:text-primary-400">
                                @if(app()->getLocale() === 'id')
                                    Transfer {{ $cashBook->type === 'in' ? 'Masuk' : 'Keluar' }}
                                @else
                                    Transfer {{ ucfirst($cashBook->type) }}
                                @endif
                            </span>
                        @else
                            <span class="inline-block px-2 py-1 text-xs font-semibold {{ in_array($cashBook->type, ['money_in', 'in']) ? 'bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-400' : 'bg-danger-100 text-danger-800 dark:bg-danger-900/30 dark:text-danger-400' }} rounded-full">
                                @if(app()->getLocale() === 'id')
                                    Uang {{ in_array($cashBook->type, ['money_in', 'in']) ? 'Masuk' : 'Keluar' }}
                                @else
                                    Money {{ in_array($cashBook->type, ['money_in', 'in']) ? 'In' : 'Out' }}
                                @endif
                            </span>
                        @endif
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ app()->getLocale() === 'id' ? 'Akun (Kas/Bank)' : 'Account (Cash/Bank)' }}
                        </span>
                        <span class="text-gray-900 dark:text-white">{{ $cashBook->account->name }}</span>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ app()->getLocale() === 'id' ? 'Kategori / Kontra Akun' : 'Category / Counter Account' }}
                        </span>
                        <span class="text-gray-900 dark:text-white">{{ $cashBook->counterAccount->name ?? '-' }}</span>
                    </div>
                    @if($cashBook->client_id)
                    <div>
                        <span class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ app()->getLocale() === 'id' ? 'Pelanggan' : 'Client' }}
                        </span>
                        <span class="text-gray-900 dark:text-white font-semibold">{{ $cashBook->client->client_name }}</span>
                        <span class="text-xs text-gray-500">({{ app()->getLocale() === 'id' ? ($cashBook->client->type === 'customer' ? 'Pelanggan' : 'Pemasok') : ucfirst($cashBook->client->type) }})</span>
                    </div>
                    @endif
                    @if($cashBook->receive_from)
                    <div>
                        <span class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ app()->getLocale() === 'id' ? 'Diterima Dari / Dibayar Kepada' : 'Receive From / Pay To' }}
                        </span>
                        <span class="text-gray-900 dark:text-white">{{ $cashBook->receive_from }}</span>
                    </div>
                    @endif
                    <div>
                        <span class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ app()->getLocale() === 'id' ? 'Deskripsi' : 'Description' }}
                        </span>
                        <p class="text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 p-3 rounded-lg mt-1 text-sm">{{ $cashBook->description }}</p>
                    </div>
                </div>
            </div>

            {{-- Journal Preview --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    {{ app()->getLocale() === 'id' ? 'Catatan Jurnal' : 'Journal Record' }}
                </h3>
                
                @if($cashBook->journalEntry)
                    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="w-full text-sm text-left divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-200">
                                        {{ app()->getLocale() === 'id' ? 'Akun' : 'Account' }}
                                    </th>
                                    <th class="px-4 py-3 font-semibold text-right text-gray-900 dark:text-gray-200">Debit</th>
                                    <th class="px-4 py-3 font-semibold text-right text-gray-900 dark:text-gray-200">Kredit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 font-mono">
                                @foreach($cashBook->journalEntry->details as $detail)
                                <tr>
                                    <td class="px-4 py-3 text-gray-900 dark:text-gray-300">
                                        {{ $detail->account->code }} - {{ $detail->account->name }}
                                    </td>
                                    <td class="px-4 py-3 text-right {{ $detail->debit > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-400' }}">
                                        {{ $detail->debit > 0 ? number_format($detail->debit, 2) : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right {{ $detail->credit > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-400' }}">
                                        {{ $detail->credit > 0 ? number_format($detail->credit, 2) : '-' }}
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="bg-gray-50 dark:bg-gray-800 font-bold border-t-2 border-gray-300 dark:border-gray-600">
                                    <td class="px-4 py-3 text-right">TOTAL</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($cashBook->journalEntry->details->sum('debit'), 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($cashBook->journalEntry->details->sum('credit'), 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 text-sm rounded-lg flex items-start gap-2">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p>
                            {{ app()->getLocale() === 'id' 
                                ? 'Entri jurnal ini dipelihara secara otomatis oleh sistem untuk memastikan pembukuan Anda selalu seimbang.' 
                                : 'This journal entry is automatically maintained by the system. It ensures that your books are always balanced.' }}
                        </p>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        {{ app()->getLocale() === 'id' ? 'Tidak ada entri jurnal untuk transaksi ini.' : 'No journal entry found for this transaction.' }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::layout>
