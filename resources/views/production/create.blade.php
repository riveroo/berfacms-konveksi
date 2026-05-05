<x-filament-panels::layout>
    <div class="px-8 py-8 mx-auto w-full max-w-full min-w-0" x-data="productionForm()">
        <form action="{{ route('production.store') }}" method="POST" class="space-y-8">
            @csrf

            {{-- Header --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">Record Production</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Create a new production batch and update stock</p>
                </div>
                <div class="flex items-center gap-3">
                    <x-button href="{{ route('production.index') }}" variant="outline">
                        Cancel
                    </x-button>
                    <x-button type="submit" variant="primary">
                        Save Production
                    </x-button>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Section 1: Information --}}
            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-bold">1</span>
                    General Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Production Date</label>
                        <input type="date" name="production_date" value="{{ now()->format('Y-m-d') }}" required
                            class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Batch Code</label>
                        <input type="text" name="batch_code" value="{{ $batchCode }}" readonly
                            class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 font-bold outline-none cursor-not-allowed">
                        <p class="text-[10px] text-gray-500 mt-1 italic">Auto-generated format: DDMMYY-NNNN</p>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Production Label / Name</label>
                        <input type="text" name="production_name" placeholder="e.g. Kaos Polos Batch A" required
                            class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User</label>
                        <input type="text" value="{{ auth()->user()->name }}" readonly
                            class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 outline-none cursor-not-allowed">
                    </div>
                </div>
            </div>

            {{-- Section 2: Material Consumption --}}
            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold">2</span>
                        Material Consumption (Stock Out)
                    </h3>
                    <button type="button" @click="addMaterial()" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Add Material
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead>
                            <tr class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-4 py-2">Material</th>
                                <th class="px-4 py-2 w-32">Qty</th>
                                <th class="px-4 py-2 w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <template x-for="(mat, matIndex) in selectedMaterials" :key="matIndex">
                                <tr>
                                    <td class="px-2 py-3">
                                        <select :name="'materials['+matIndex+'][item_id]'" x-model="mat.item_id" required
                                            class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none cursor-pointer">
                                            <option value="">Select Material</option>
                                            @foreach($materials as $m)
                                                <option value="{{ $m->id }}">{{ $m->item_name }} (Stock: {{ number_format($m->stock, 2) }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-2 py-3">
                                        <input type="number" :name="'materials['+matIndex+'][quantity]'" x-model="mat.quantity" step="0.01" min="0.01" required placeholder="0.00"
                                            class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none">
                                    </td>
                                    <td class="px-2 py-3 text-right">
                                        <button type="button" @click="removeMaterial(matIndex)" class="text-red-500 hover:text-red-700 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div x-show="selectedMaterials.length === 0" class="py-10 text-center text-sm text-gray-500 italic">
                        No materials added yet. Click "Add Material" to start.
                    </div>
                </div>
            </div>

            {{-- Section 3: Product Output --}}
            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-bold">3</span>
                        Product Output (Stock In)
                    </h3>
                    <button type="button" @click="addProduct()" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Add Product
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead>
                            <tr class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-4 py-2">Product</th>
                                <th class="px-4 py-2">Variant</th>
                                <th class="px-4 py-2">Size</th>
                                <th class="px-4 py-2 w-32">Qty</th>
                                <th class="px-4 py-2 w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <template x-for="(prod, prodIndex) in selectedProducts" :key="prodIndex">
                                <tr>
                                    <td class="px-2 py-3">
                                        <select :name="'products['+prodIndex+'][product_id]'" x-model="prod.product_id" @change="onProductChange(prod)" required
                                            class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none cursor-pointer">
                                            <option value="">Select Product</option>
                                            <template x-for="p in allProducts" :key="p.id">
                                                <option :value="p.id" x-text="p.product_name"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="px-2 py-3">
                                        <select :name="'products['+prodIndex+'][variant_id]'" x-model="prod.variant_id" @change="onVariantChange(prod)" required :disabled="!prod.product_id"
                                            class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none cursor-pointer disabled:bg-gray-100 disabled:cursor-not-allowed">
                                            <option value="">Select Variant</option>
                                            <template x-for="v in getVariants(prod.product_id)" :key="v.id">
                                                <option :value="v.id" x-text="v.variant_name"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="px-2 py-3">
                                        <select :name="'products['+prodIndex+'][size_option_id]'" x-model="prod.size_option_id" :disabled="!prod.variant_id"
                                            class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none cursor-pointer disabled:bg-gray-100 disabled:cursor-not-allowed">
                                            <option value="">Select Size</option>
                                            <template x-for="s in getSizes(prod.variant_id)" :key="s.id">
                                                <option :value="s.id" x-text="s.name"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="px-2 py-3">
                                        <input type="number" :name="'products['+prodIndex+'][quantity]'" x-model="prod.quantity" min="1" required placeholder="0"
                                            :disabled="!prod.variant_id || (getSizes(prod.variant_id).length > 0 && !prod.size_option_id)"
                                            class="w-full h-10 px-3 rounded-lg border border-gray-300 bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none disabled:bg-gray-100 disabled:cursor-not-allowed">
                                    </td>
                                    <td class="px-2 py-3 text-right">
                                        <button type="button" @click="removeProduct(prodIndex)" class="text-red-500 hover:text-red-700 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div x-show="selectedProducts.length === 0" class="py-10 text-center text-sm text-gray-500 italic">
                        No products added yet. Click "Add Product" to start.
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function productionForm() {
            return {
                allProducts: @json($products),
                selectedMaterials: [{ item_id: '', quantity: '' }],
                selectedProducts: [{ product_id: '', variant_id: '', size_option_id: '', quantity: '' }],

                addMaterial() {
                    this.selectedMaterials.push({ item_id: '', quantity: '' });
                },
                removeMaterial(index) {
                    this.selectedMaterials.splice(index, 1);
                },

                addProduct() {
                    this.selectedProducts.push({ product_id: '', variant_id: '', size_option_id: '', quantity: '' });
                },
                removeProduct(index) {
                    this.selectedProducts.splice(index, 1);
                },

                onProductChange(prod) {
                    prod.variant_id = '';
                    prod.size_option_id = '';
                },
                onVariantChange(prod) {
                    prod.size_option_id = '';
                },

                getVariants(productId) {
                    if (!productId) return [];
                    const product = this.allProducts.find(p => p.id == productId);
                    return product ? product.variants : [];
                },

                getSizes(variantId) {
                    if (!variantId) return [];
                    
                    // Search all products to find the variant
                    for (let p of this.allProducts) {
                        const variant = p.variants.find(v => v.id == variantId);
                        if (variant) {
                            // Collect sizes from stocks
                            return variant.stocks
                                .filter(s => s.size_option_id !== null && s.size_option !== null)
                                .map(s => ({
                                    id: s.size_option_id,
                                    name: s.size_option.name
                                }));
                        }
                    }
                    return [];
                }
            }
        }
    </script>
</x-filament-panels::layout>
