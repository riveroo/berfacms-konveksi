<x-filament-panels::page>
    <div class="space-y-4" x-data="{ open: 'hero' }">
        
        <!-- Hero Section CMS -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <button @click="open = open === 'hero' ? null : 'hero'" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                <div class="flex flex-col items-start text-left">
                    <h2 class="text-xl font-bold text-gray-900">Hero Section</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage up to 5 hero images for the landing page.</p>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': open === 'hero'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open === 'hero'" x-collapse class="px-6 pb-6 border-t border-gray-100 pt-4">
                <div class="flex justify-end mb-4">
                    <div class="text-sm text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200 inline-block">
                        <span class="font-bold">Recommended:</span> 1920x800px (16:9), JPG/PNG, Max 2MB
                    </div>
                </div>

                @if(count($heroes) < 5)
                    <form wire:submit.prevent="saveHero" class="bg-gray-50 rounded-lg p-5 mb-6 border border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-4">Add New Hero Image</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Image <span class="text-red-500">*</span></label>
                                <input type="file" wire:model="newHeroImage" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-md p-1 bg-white">
                                @error('newHeroImage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Redirect Link (Optional)</label>
                                <input type="url" wire:model="newHeroLink" placeholder="https://example.com" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                @error('newHeroLink') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @error('newHero') <div class="text-red-500 text-sm mt-3">{{ $message }}</div> @enderror
                        <div class="mt-4">
                            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-500 transition-colors" wire:loading.attr="disabled" wire:target="saveHero, newHeroImage">
                                <span wire:loading.remove wire:target="saveHero">Upload Image</span>
                                <span wire:loading wire:target="saveHero">Uploading...</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bg-blue-50 text-blue-700 p-4 rounded-lg mb-6 border border-blue-100 text-sm">
                        Maximum 5 hero images reached. Delete an existing image to add a new one.
                    </div>
                @endif

                <div class="space-y-4">
                    @foreach($heroes as $hero)
                        <div class="flex items-center gap-4 p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="w-40 h-20 rounded-lg overflow-hidden bg-gray-100 shrink-0 border border-gray-200">
                                <img src="{{ asset('storage/' . $hero['image']) }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-grow">
                                <div class="text-sm font-medium text-gray-900">Sort Order: {{ $hero['sort_order'] }}</div>
                                <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">
                                    Link: <a href="{{ $hero['link'] }}" target="_blank" class="text-primary-600 hover:underline">{{ $hero['link'] ?: 'None' }}</a>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <button wire:click="toggleHeroActive({{ $hero['id'] }})" class="px-3 py-1.5 rounded-lg text-xs font-medium {{ $hero['is_active'] ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    {{ $hero['is_active'] ? 'Active' : 'Inactive' }}
                                </button>
                                <button wire:click="deleteHero({{ $hero['id'] }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Our Value Section CMS -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <button @click="open = open === 'value' ? null : 'value'" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                <div class="flex flex-col items-start text-left">
                    <h2 class="text-xl font-bold text-gray-900">Our Value Section</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage up to 3 value proposition cards.</p>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': open === 'value'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open === 'value'" x-collapse class="px-6 pb-6 border-t border-gray-100 pt-4">
                @if(count($values) < 3)
                    <form wire:submit.prevent="saveValue" class="bg-gray-50 rounded-lg p-5 mb-6 border border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-4">Add New Value Card</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Icon/Image <span class="text-red-500">*</span></label>
                                <input type="file" wire:model="newValueImage" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-md p-1 bg-white">
                                @error('newValueImage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="newValueTitle" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                @error('newValueTitle') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                            <textarea wire:model="newValueDescription" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"></textarea>
                            @error('newValueDescription') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        @error('newValue') <div class="text-red-500 text-sm mt-3">{{ $message }}</div> @enderror
                        <div>
                            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-500 transition-colors" wire:loading.attr="disabled" wire:target="saveValue, newValueImage">
                                <span wire:loading.remove wire:target="saveValue">Add Card</span>
                                <span wire:loading wire:target="saveValue">Saving...</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bg-blue-50 text-blue-700 p-4 rounded-lg mb-6 border border-blue-100 text-sm">
                        Maximum 3 value cards reached. Delete an existing card to add a new one.
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($values as $value)
                        <div class="border border-gray-200 rounded-xl p-5 relative hover:border-primary-300 transition-colors bg-white">
                            <button wire:click="deleteValue({{ $value['id'] }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" class="absolute top-3 right-3 p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            <div class="w-16 h-16 bg-gray-50 rounded-xl overflow-hidden mb-4 border border-gray-100">
                                <img src="{{ asset('storage/' . $value['image']) }}" class="w-full h-full object-cover">
                            </div>
                            <h4 class="font-bold text-gray-900 text-lg mb-2">{{ $value['title'] }}</h4>
                            <p class="text-sm text-gray-600 line-clamp-3">{{ $value['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Client Logo Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <button @click="open = open === 'logo' ? null : 'logo'" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                <div class="flex flex-col items-start text-left">
                    <h2 class="text-xl font-bold text-gray-900">Client Logo Section</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage up to 6 client logos (horizontal scrolling).</p>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': open === 'logo'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open === 'logo'" x-collapse class="px-6 pb-6 border-t border-gray-100 pt-4">
                <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
                    <div>
                        <span class="font-semibold text-gray-900 block">Section Status</span>
                        <span class="text-sm text-gray-500">Toggle visibility of this section on the landing page</span>
                    </div>
                    <button type="button" wire:click="toggleClientLogoSection" class="relative inline-flex items-center cursor-pointer">
                        <span class="sr-only">Toggle section</span>
                        <div class="w-11 h-6 rounded-full transition-colors {{ $clientLogoActive ? 'bg-primary-600' : 'bg-gray-200' }}"></div>
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $clientLogoActive ? 'translate-x-5' : 'translate-x-0' }}"></div>
                    </button>
                </div>

                @if(count($logos) < 6)
                    <form wire:submit.prevent="saveLogo" class="bg-gray-50 rounded-lg p-5 mb-6 border border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-4">Add New Logo</h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Logo Image <span class="text-red-500">*</span></label>
                            <input type="file" wire:model="newLogoImage" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-md p-1 bg-white">
                            @error('newLogoImage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        @error('newLogo') <div class="text-red-500 text-sm mt-3">{{ $message }}</div> @enderror
                        <div>
                            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-500 transition-colors" wire:loading.attr="disabled" wire:target="saveLogo, newLogoImage">
                                <span wire:loading.remove wire:target="saveLogo">Add Logo</span>
                                <span wire:loading wire:target="saveLogo">Saving...</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bg-blue-50 text-blue-700 p-4 rounded-lg mb-6 border border-blue-100 text-sm">
                        Maximum 6 logos reached. Delete an existing logo to add a new one.
                    </div>
                @endif

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach($logos as $logo)
                        <div class="border border-gray-200 rounded-xl p-4 relative hover:border-primary-300 transition-colors bg-white flex flex-col items-center justify-center">
                            <button wire:click="deleteLogo({{ $logo['id'] }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" class="absolute top-2 right-2 p-1 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            <div class="w-full aspect-[3/2] flex items-center justify-center p-2">
                                <img src="{{ asset('storage/' . $logo['image']) }}" class="max-w-full max-h-full object-contain mix-blend-multiply">
                            </div>
                            <button wire:click="toggleLogoActive({{ $logo['id'] }})" class="mt-2 px-2 py-1 rounded text-[10px] font-medium w-full {{ $logo['is_active'] ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                {{ $logo['is_active'] ? 'Active' : 'Inactive' }}
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Product Category Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <button @click="open = open === 'category' ? null : 'category'" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                <div class="flex flex-col items-start text-left">
                    <h2 class="text-xl font-bold text-gray-900">Product Category Section</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage product categories (max 6).</p>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': open === 'category'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open === 'category'" x-collapse class="px-6 pb-6 border-t border-gray-100 pt-4">
                <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
                    <div>
                        <span class="font-semibold text-gray-900 block">Number of Rows</span>
                        <span class="text-sm text-gray-500">1 row = 3 items, 2 rows = 6 items</span>
                    </div>
                    <select wire:model.live="categoryRows" class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <option value="1">1 Row (3 items)</option>
                        <option value="2">2 Rows (6 items)</option>
                    </select>
                </div>

                @if(count($categories) < 6)
                    <form wire:submit.prevent="saveCategory" class="bg-gray-50 rounded-lg p-5 mb-6 border border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-4">Add New Category</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Image <span class="text-red-500">*</span></label>
                                <input type="file" wire:model="newCategoryImage" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-md p-1 bg-white">
                                @error('newCategoryImage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="newCategoryTitle" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                @error('newCategoryTitle') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Link URL <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="newCategoryLink" placeholder="/products/category-name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            @error('newCategoryLink') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        @error('newCategory') <div class="text-red-500 text-sm mt-3">{{ $message }}</div> @enderror
                        <div>
                            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-500 transition-colors" wire:loading.attr="disabled" wire:target="saveCategory, newCategoryImage">
                                <span wire:loading.remove wire:target="saveCategory">Add Category</span>
                                <span wire:loading wire:target="saveCategory">Saving...</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bg-blue-50 text-blue-700 p-4 rounded-lg mb-6 border border-blue-100 text-sm">
                        Maximum 6 categories reached. Delete an existing category to add a new one.
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($categories as $category)
                        <div class="border border-gray-200 rounded-xl p-4 relative hover:border-primary-300 transition-colors bg-white">
                            <button wire:click="deleteCategory({{ $category['id'] }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" class="absolute top-2 right-2 p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors z-10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            <div class="aspect-[4/3] rounded-lg overflow-hidden bg-gray-100 mb-3 relative">
                                <img src="{{ asset('storage/' . $category['image']) }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-3">
                                    <h4 class="font-bold text-white text-lg">{{ $category['title'] }}</h4>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 truncate mt-2">
                                Link: <a href="{{ $category['link'] }}" class="text-primary-600 hover:underline">{{ $category['link'] }}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Popular Products Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <button @click="open = open === 'popular' ? null : 'popular'" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                <div class="flex flex-col items-start text-left">
                    <h2 class="text-xl font-bold text-gray-900">Popular Products Section</h2>
                    <p class="text-sm text-gray-500 mt-1">Select up to 4 products to feature on the homepage.</p>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': open === 'popular'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open === 'popular'" x-collapse class="px-6 pb-6 border-t border-gray-100 pt-4">
                @if(count($popularProducts) < 4)
                    <form wire:submit.prevent="savePopularProduct" class="bg-gray-50 rounded-lg p-5 mb-6 border border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-4">Add Popular Product</h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select Product <span class="text-red-500">*</span></label>
                            <select wire:model="newPopularProductId" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                <option value="">-- Choose Product --</option>
                                @foreach($availableProducts as $product)
                                    <option value="{{ $product['id'] }}">{{ $product['product_name'] ?? 'Unknown' }}</option>
                                @endforeach
                            </select>
                            @error('newPopularProductId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        @error('newPopularProduct') <div class="text-red-500 text-sm mt-3">{{ $message }}</div> @enderror
                        <div>
                            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-500 transition-colors" wire:loading.attr="disabled" wire:target="savePopularProduct">
                                <span wire:loading.remove wire:target="savePopularProduct">Add Product</span>
                                <span wire:loading wire:target="savePopularProduct">Saving...</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bg-blue-50 text-blue-700 p-4 rounded-lg mb-6 border border-blue-100 text-sm">
                        Maximum 4 popular products reached. Delete an existing one to add a new one.
                    </div>
                @endif

                <div class="space-y-3">
                    @foreach($popularProducts as $pop)
                        <div class="flex items-center gap-4 p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors bg-white">
                            <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 shrink-0 border border-gray-200">
                                @if(isset($pop['product']['thumbnail']) && $pop['product']['thumbnail'])
                                    <img src="{{ asset('storage/' . $pop['product']['thumbnail']) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow">
                                <div class="text-sm font-bold text-gray-900">{{ $pop['product']['product_name'] ?? 'Unknown Product' }}</div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <button wire:click="deletePopularProduct({{ $pop['id'] }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Banner CTA Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <button @click="open = open === 'banner' ? null : 'banner'" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                <div class="flex flex-col items-start text-left">
                    <h2 class="text-xl font-bold text-gray-900">Banner CTA Section</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage the promotional banner section.</p>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': open === 'banner'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open === 'banner'" x-collapse class="px-6 pb-6 border-t border-gray-100 pt-4">
                <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
                    <div>
                        <span class="font-semibold text-gray-900 block">Section Status</span>
                        <span class="text-sm text-gray-500">Toggle visibility of this banner on the landing page</span>
                    </div>
                    <button type="button" wire:click="toggleBannerActive" class="relative inline-flex items-center cursor-pointer">
                        <span class="sr-only">Toggle section</span>
                        <div class="w-11 h-6 rounded-full transition-colors {{ $bannerActive ? 'bg-primary-600' : 'bg-gray-200' }}"></div>
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $bannerActive ? 'translate-x-5' : 'translate-x-0' }}"></div>
                    </button>
                </div>

                <form wire:submit.prevent="saveBannerCta" class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                    <div class="flex justify-end mb-4">
                        <div class="text-sm text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200 inline-block">
                            <span class="font-bold">Recommended:</span> 1200x450px (16:6), JPG/PNG, Max 2MB
                        </div>
                    </div>

                    @if($bannerCta && isset($bannerCta['image']))
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                            <div class="w-full max-w-2xl aspect-[16/6] bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                <img src="{{ asset('storage/' . $bannerCta['image']) }}" class="w-full h-full object-cover">
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload New Image @if(!$bannerCta) <span class="text-red-500">*</span> @endif</label>
                            <input type="file" wire:model="newBannerImage" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-md p-1 bg-white">
                            @error('newBannerImage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="newBannerTitle" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            @error('newBannerTitle') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                        <textarea wire:model="newBannerDescription" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"></textarea>
                        @error('newBannerDescription') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Redirect Link <span class="text-red-500">*</span></label>
                        <input type="url" wire:model="newBannerLink" placeholder="https://example.com" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        @error('newBannerLink') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-500 transition-colors" wire:loading.attr="disabled" wire:target="saveBannerCta, newBannerImage">
                            <span wire:loading.remove wire:target="saveBannerCta">Save Banner</span>
                            <span wire:loading wire:target="saveBannerCta">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <button @click="open = open === 'review' ? null : 'review'" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                <div class="flex flex-col items-start text-left">
                    <h2 class="text-xl font-bold text-gray-900">Client Reviews Section</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage up to 8 client testimonials.</p>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': open === 'review'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open === 'review'" x-collapse class="px-6 pb-6 border-t border-gray-100 pt-4">
                @if(count($reviews) < 8)
                    <form wire:submit.prevent="saveReview" class="bg-gray-50 rounded-lg p-5 mb-6 border border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-4">Add New Review</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reviewer Name <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="newReviewerName" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                @error('newReviewerName') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Company / Client Name <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="newClientName" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                @error('newClientName') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Review Text <span class="text-red-500">*</span></label>
                            <textarea wire:model="newReviewText" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"></textarea>
                            @error('newReviewText') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        @error('newReview') <div class="text-red-500 text-sm mt-3">{{ $message }}</div> @enderror
                        <div>
                            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-500 transition-colors" wire:loading.attr="disabled" wire:target="saveReview">
                                <span wire:loading.remove wire:target="saveReview">Add Review</span>
                                <span wire:loading wire:target="saveReview">Saving...</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bg-blue-50 text-blue-700 p-4 rounded-lg mb-6 border border-blue-100 text-sm">
                        Maximum 8 reviews reached. Delete an existing one to add a new one.
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($reviews as $review)
                        <div class="border border-gray-200 rounded-xl p-5 relative hover:border-primary-300 transition-colors bg-white">
                            <button wire:click="deleteReview({{ $review['id'] }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" class="absolute top-3 right-3 p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            <p class="text-sm text-gray-700 mb-4 pr-6 italic">"{{ $review['review_text'] }}"</p>
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 text-sm">{{ $review['reviewer_name'] }}</span>
                                <span class="text-xs text-gray-500">{{ $review['client_name'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <button @click="open = open === 'footer' ? null : 'footer'" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                <div class="flex flex-col items-start text-left">
                    <h2 class="text-xl font-bold text-gray-900">Footer Settings</h2>
                    <p class="text-sm text-gray-500 mt-1">Manage company information and social media links.</p>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': open === 'footer'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open === 'footer'" x-collapse class="px-6 pb-6 border-t border-gray-100 pt-4">
                <form wire:submit.prevent="saveFooter" class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-4">General Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company Name <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="footerCompanyName" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            @error('footerCompanyName') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" wire:model="footerPhone" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            @error('footerPhone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" wire:model="footerEmail" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            @error('footerEmail') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea wire:model="footerAddress" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"></textarea>
                        @error('footerAddress') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <h3 class="font-semibold text-gray-700 mb-4 pt-4 border-t border-gray-200">Social Media Links (Optional)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">YouTube URL</label>
                            <input type="url" wire:model="footerYoutubeUrl" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instagram URL</label>
                            <input type="url" wire:model="footerInstagramUrl" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">TikTok URL</label>
                            <input type="url" wire:model="footerTiktokUrl" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tokopedia URL</label>
                            <input type="url" wire:model="footerTokopediaUrl" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shopee URL</label>
                            <input type="url" wire:model="footerShopeeUrl" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Facebook URL</label>
                            <input type="url" wire:model="footerFacebookUrl" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-500 transition-colors" wire:loading.attr="disabled" wire:target="saveFooter">
                            <span wire:loading.remove wire:target="saveFooter">Save Footer Settings</span>
                            <span wire:loading wire:target="saveFooter">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-filament-panels::page>
