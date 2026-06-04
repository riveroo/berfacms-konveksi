<x-filament-panels::page>
    <div class="space-y-4" x-data="{ open: 'hero' }">
        <style>
            .loader {
                border: 2px solid #f3f3f3;
                border-top: 2px solid #3498db;
                border-radius: 50%;
                width: 14px;
                height: 14px;
                animation: spin 1s linear infinite;
                display: inline-block;
                margin-right: 8px;
                vertical-align: middle;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
        
        <!-- Hero Section CMS -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <button @click="open = open === 'hero' ? null : 'hero'" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                <div class="flex flex-col items-start text-left">
                    <h2 class="text-xl font-bold text-gray-900">{{ __('landing.hero_title') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('landing.hero_subtitle') }}</p>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': open === 'hero'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open === 'hero'" x-collapse class="px-6 pb-6 border-t border-gray-100 pt-4">
                <div class="flex justify-end mb-4">
                    <div class="text-sm text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200 inline-block">
                        <span class="font-bold">{{ __('landing.hero_recommended') }}</span> 1920x800px (16:9), JPG/PNG, Max 2MB
                    </div>
                </div>

                @if(count($heroes) < 5)
                    <form wire:submit.prevent="saveHero" class="bg-gray-50 rounded-lg p-5 mb-6 border border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-4">{{ __('landing.hero_add_title') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.hero_label_image') }} <span class="text-red-500">*</span></label>
                                <input type="file" wire:model="newHeroImage" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-md p-1 bg-white">
                                @error('newHeroImage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.hero_label_link') }}</label>
                                <input type="url" wire:model="newHeroLink" placeholder="https://example.com" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                @error('newHeroLink') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @error('newHero') <div class="text-red-500 text-sm mt-3">{{ $message }}</div> @enderror
                        <div class="mt-4 flex items-center gap-4">
                            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-500 transition-colors inline-flex items-center" wire:loading.attr="disabled" wire:target="saveHero, newHeroImage">
                                <span wire:loading.remove wire:target="saveHero">{{ __('landing.hero_upload_btn') }}</span>
                                <span wire:loading wire:target="saveHero"><span class="loader"></span>{{ __('landing.hero_uploading') }}</span>
                            </button>
                            <div wire:loading wire:target="newHeroImage" class="text-xs text-primary-600 flex items-center">
                                <span class="loader"></span> Memproses gambar...
                            </div>
                        </div>
                    </form>
                @else
                    <div class="bg-blue-50 text-blue-700 p-4 rounded-lg mb-6 border border-blue-100 text-sm">
                        {{ __('landing.hero_limit_reached') }}
                    </div>
                @endif

                <div class="space-y-4" x-data="sortableList('updateHeroOrder')">
                    @foreach($heroes as $hero)
                        <div data-id="{{ $hero['id'] }}" class="flex items-center gap-4 p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors bg-white shadow-sm relative group" wire:loading.class="opacity-50 pointer-events-none" wire:target="saveHero, updateHero">
                            <!-- Drag Handle -->
                            <div class="drag-handle cursor-grab active:cursor-grabbing p-2 text-gray-400 hover:text-gray-600 transition-colors" title="{{ __('landing.hero_drag_hint') }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                            </div>

                            @if($editHeroId === $hero['id'])
                                <!-- Edit Mode -->
                                <div class="flex-grow flex flex-col gap-3">
                                    <div class="flex items-center gap-4">
                                        <div class="w-32 h-16 rounded-lg overflow-hidden bg-gray-100 shrink-0 border border-gray-200">
                                            <img src="{{ asset('storage/' . $hero['image']) }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-grow">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('landing.hero_edit_upload_new') }}</label>
                                            <input type="file" wire:model="editHeroImage" class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded p-1 bg-white">
                                            @error('editHeroImage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('landing.hero_edit_link') }}</label>
                                        <input type="url" wire:model="editHeroLink" class="w-full rounded border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500 py-1.5 px-3">
                                        @error('editHeroLink') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="flex gap-2">
                                        <button wire:click="updateHero" wire:loading.attr="disabled" class="bg-primary-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-primary-500 disabled:opacity-50">{{ __('landing.hero_save') }}</button>
                                        <button wire:click="cancelEditHero" wire:loading.attr="disabled" class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded text-xs font-medium hover:bg-gray-300 disabled:opacity-50">{{ __('landing.hero_cancel') }}</button>
                                        <div wire:loading wire:target="editHeroImage" class="text-xs text-primary-600 ml-2 mt-1">Uploading...</div>
                                    </div>
                                </div>
                            @else
                                <!-- Display Mode -->
                                <div class="w-40 h-20 rounded-lg overflow-hidden bg-gray-100 shrink-0 border border-gray-200">
                                    <img src="{{ asset('storage/' . $hero['image']) }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-grow">
                                    <div class="text-sm font-medium text-gray-900">{{ __('landing.hero_sort_order') }}: {{ $hero['sort_order'] }}</div>
                                    <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">
                                        {{ __('landing.hero_link') }}: <a href="{{ $hero['link'] }}" target="_blank" class="text-primary-600 hover:underline">{{ $hero['link'] ?: __('landing.hero_none') }}</a>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button wire:click="toggleHeroActive({{ $hero['id'] }})" wire:loading.attr="disabled" class="px-3 py-1.5 rounded-lg text-xs font-medium disabled:opacity-50 {{ $hero['is_active'] ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                        {{ $hero['is_active'] ? __('landing.hero_active') : __('landing.hero_inactive') }}
                                    </button>
                                    <button wire:click="editHero({{ $hero['id'] }})" wire:loading.attr="disabled" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors disabled:opacity-50" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="deleteHero({{ $hero['id'] }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:loading.attr="disabled" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-50" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Our Value Section CMS -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <button @click="open = open === 'value' ? null : 'value'" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                <div class="flex flex-col items-start text-left">
                    <h2 class="text-xl font-bold text-gray-900">{{ __('landing.value_title') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('landing.value_subtitle') }}</p>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': open === 'value'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open === 'value'" x-collapse class="px-6 pb-6 border-t border-gray-100 pt-4">
                @if(count($values) < 5)
                    <form wire:submit.prevent="saveValue" class="bg-gray-50 rounded-lg p-5 mb-6 border border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-4">{{ __('landing.value_add_title') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.value_label_image') }} <span class="text-red-500">*</span></label>
                                <input type="file" wire:model="newValueImage" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-md p-1 bg-white">
                                @error('newValueImage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.value_label_title') }} <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="newValueTitle" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                @error('newValueTitle') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.value_label_desc') }} <span class="text-red-500">*</span></label>
                            <textarea wire:model="newValueDescription" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"></textarea>
                            @error('newValueDescription') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        @error('newValue') <div class="text-red-500 text-sm mt-3">{{ $message }}</div> @enderror
                        <div>
                            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-500 transition-colors" wire:loading.attr="disabled" wire:target="saveValue, newValueImage">
                                <span wire:loading.remove wire:target="saveValue">{{ __('landing.value_add_btn') }}</span>
                                <span wire:loading wire:target="saveValue">{{ __('landing.value_saving') }}</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bg-blue-50 text-blue-700 p-4 rounded-lg mb-6 border border-blue-100 text-sm">
                        {{ __('landing.value_limit_reached') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4" x-data="sortableList('updateValueOrder')">
                    @foreach($values as $value)
                        <div data-id="{{ $value['id'] }}" class="border border-gray-200 rounded-xl p-5 relative hover:border-primary-300 transition-colors bg-white group" wire:loading.class="opacity-50 pointer-events-none" wire:target="saveValue, updateValue">
                            <!-- Drag Handle -->
                            <div class="drag-handle absolute top-3 left-3 cursor-grab active:cursor-grabbing p-1.5 text-gray-400 hover:text-gray-600 transition-colors" title="{{ __('landing.hero_drag_hint') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                            </div>
                            
                            @if($editValueId === $value['id'])
                                <!-- Edit Mode -->
                                <div class="mt-8 flex flex-col gap-3">
                                    <div class="w-16 h-16 bg-gray-50 rounded-xl overflow-hidden border border-gray-100 shrink-0">
                                        <img src="{{ asset('storage/' . $value['image']) }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('landing.value_edit_upload_new') }}</label>
                                        <input type="file" wire:model="editValueImage" class="w-full text-xs text-gray-500 border border-gray-300 rounded p-1">
                                        @error('editValueImage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('landing.value_label_title') }}</label>
                                        <input type="text" wire:model="editValueTitle" class="w-full rounded border-gray-300 text-sm py-1.5 px-3">
                                        @error('editValueTitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('landing.value_label_desc') }}</label>
                                        <textarea wire:model="editValueDescription" rows="3" class="w-full rounded border-gray-300 text-sm py-1.5 px-3"></textarea>
                                        @error('editValueDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="flex gap-2 mt-2">
                                        <button wire:click="updateValue" wire:loading.attr="disabled" class="bg-primary-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-primary-500 disabled:opacity-50">{{ __('landing.hero_save') }}</button>
                                        <button wire:click="cancelEditValue" wire:loading.attr="disabled" class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded text-xs font-medium hover:bg-gray-300 disabled:opacity-50">{{ __('landing.hero_cancel') }}</button>
                                    </div>
                                </div>
                            @else
                                <!-- Display Mode -->
                                <div class="absolute top-3 right-3 flex gap-1">
                                    <button wire:click="editValue({{ $value['id'] }})" wire:loading.attr="disabled" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors disabled:opacity-50" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="deleteValue({{ $value['id'] }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:loading.attr="disabled" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-50" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                                <div class="w-16 h-16 bg-gray-50 rounded-xl overflow-hidden mb-4 mt-6 border border-gray-100">
                                    <img src="{{ asset('storage/' . $value['image']) }}" class="w-full h-full object-cover">
                                </div>
                                <h4 class="font-bold text-gray-900 text-lg mb-2">{{ $value['title'] }}</h4>
                                <p class="text-sm text-gray-600 line-clamp-3">{{ $value['description'] }}</p>
                                <div class="text-xs text-gray-400 mt-2">{{ __('landing.value_sort') }}: {{ $value['sort_order'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Client Logo Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <button @click="open = open === 'logo' ? null : 'logo'" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                <div class="flex flex-col items-start text-left">
                    <h2 class="text-xl font-bold text-gray-900">{{ __('landing.logo_title') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('landing.logo_subtitle') }}</p>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': open === 'logo'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open === 'logo'" x-collapse class="px-6 pb-6 border-t border-gray-100 pt-4">
                <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
                    <div>
                        <span class="font-semibold text-gray-900 block">{{ __('landing.logo_status') }}</span>
                        <span class="text-sm text-gray-500">{{ __('landing.logo_status_desc') }}</span>
                    </div>
                    <button type="button" wire:click="toggleClientLogoSection" class="relative inline-flex items-center cursor-pointer">
                        <span class="sr-only">Toggle section</span>
                        <div class="w-11 h-6 rounded-full transition-colors {{ $clientLogoActive ? 'bg-primary-600' : 'bg-gray-200' }}"></div>
                        <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform {{ $clientLogoActive ? 'translate-x-5' : 'translate-x-0' }}"></div>
                    </button>
                </div>

                @if(count($logos) < 6)
                    <form wire:submit.prevent="saveLogo" class="bg-gray-50 rounded-lg p-5 mb-6 border border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-4">{{ __('landing.logo_add_title') }}</h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.logo_label_image') }} <span class="text-red-500">*</span></label>
                            <input type="file" wire:model="newLogoImage" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-md p-1 bg-white">
                            @error('newLogoImage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        @error('newLogo') <div class="text-red-500 text-sm mt-3">{{ $message }}</div> @enderror
                        <div class="mt-4 flex items-center gap-4">
                            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-500 transition-colors inline-flex items-center" wire:loading.attr="disabled" wire:target="saveLogo, newLogoImage">
                                <span wire:loading.remove wire:target="saveLogo">{{ __('landing.logo_add_btn') }}</span>
                                <span wire:loading wire:target="saveLogo"><span class="loader"></span>{{ __('landing.value_saving') }}</span>
                            </button>
                            <div wire:loading wire:target="newLogoImage" class="text-xs text-primary-600 flex items-center">
                                <span class="loader"></span> Memproses logo...
                            </div>
                        </div>
                    </form>
                @else
                    <div class="bg-blue-50 text-blue-700 p-4 rounded-lg mb-6 border border-blue-100 text-sm">
                        {{ __('landing.logo_limit_reached') }}
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
                                {{ $logo['is_active'] ? __('landing.hero_active') : __('landing.hero_inactive') }}
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
                    <h2 class="text-xl font-bold text-gray-900">{{ __('landing.category_title') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('landing.category_subtitle') }}</p>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': open === 'category'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open === 'category'" x-collapse class="px-6 pb-6 border-t border-gray-100 pt-4">
                <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
                    <div>
                        <span class="font-semibold text-gray-900 block">{{ __('landing.category_rows') }}</span>
                        <span class="text-sm text-gray-500">{{ __('landing.category_rows_desc') }}</span>
                    </div>
                    <select wire:model.live="categoryRows" class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        <option value="1">{{ __('landing.category_row_1') }}</option>
                        <option value="2">{{ __('landing.category_row_2') }}</option>
                    </select>
                </div>

                @if(count($categories) < 6)
                    <form wire:submit.prevent="saveCategory" class="bg-gray-50 rounded-lg p-5 mb-6 border border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-4">{{ __('landing.category_add_title') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.category_label_image') }} <span class="text-red-500">*</span></label>
                                <input type="file" wire:model="newCategoryImage" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-md p-1 bg-white">
                                @error('newCategoryImage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.category_label_title') }} <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="newCategoryTitle" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                @error('newCategoryTitle') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.category_label_link') }} <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="newCategoryLink" placeholder="/products/category-name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            @error('newCategoryLink') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        @error('newCategory') <div class="text-red-500 text-sm mt-3">{{ $message }}</div> @enderror
                        <div class="mt-4 flex items-center gap-4">
                            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-500 transition-colors inline-flex items-center" wire:loading.attr="disabled" wire:target="saveCategory, newCategoryImage">
                                <span wire:loading.remove wire:target="saveCategory">{{ __('landing.category_add_btn') }}</span>
                                <span wire:loading wire:target="saveCategory"><span class="loader"></span>{{ __('landing.value_saving') }}</span>
                            </button>
                            <div wire:loading wire:target="newCategoryImage" class="text-xs text-primary-600 flex items-center">
                                <span class="loader"></span> Memproses gambar...
                            </div>
                        </div>
                    </form>
                @else
                    <div class="bg-blue-50 text-blue-700 p-4 rounded-lg mb-6 border border-blue-100 text-sm">
                        {{ __('landing.category_limit_reached') }}
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
                                {{ __('landing.category_link') }}: <a href="{{ $category['link'] }}" class="text-primary-600 hover:underline">{{ $category['link'] }}</a>
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
                    <h2 class="text-xl font-bold text-gray-900">{{ __('landing.popular_title') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('landing.popular_subtitle') }}</p>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': open === 'popular'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open === 'popular'" x-collapse class="px-6 pb-6 border-t border-gray-100 pt-4">
                @if(count($popularProducts) < 4)
                    <form wire:submit.prevent="savePopularProduct" class="bg-gray-50 rounded-lg p-5 mb-6 border border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-4">{{ __('landing.popular_add_title') }}</h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.popular_label_select') }} <span class="text-red-500">*</span></label>
                            <select wire:model="newPopularProductId" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                <option value="">{{ __('landing.popular_select_placeholder') }}</option>
                                @foreach($availableProducts as $product)
                                    <option value="{{ $product['id'] }}">{{ $product['product_name'] ?? 'Unknown' }}</option>
                                @endforeach
                            </select>
                            @error('newPopularProductId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        @error('newPopularProduct') <div class="text-red-500 text-sm mt-3">{{ $message }}</div> @enderror
                        <div>
                            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-500 transition-colors" wire:loading.attr="disabled" wire:target="savePopularProduct">
                                <span wire:loading.remove wire:target="savePopularProduct">{{ __('landing.popular_add_btn') }}</span>
                                <span wire:loading wire:target="savePopularProduct">{{ __('landing.value_saving') }}</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bg-blue-50 text-blue-700 p-4 rounded-lg mb-6 border border-blue-100 text-sm">
                        {{ __('landing.popular_limit_reached') }}
                    </div>
                @endif

                <div class="space-y-3" x-data="sortableList('updatePopularProductOrder')">
                    @foreach($popularProducts as $pop)
                        <div data-id="{{ $pop['id'] }}" class="flex items-center gap-4 p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors bg-white shadow-sm relative group">
                            <!-- Drag Handle -->
                            <div class="drag-handle cursor-grab active:cursor-grabbing p-2 text-gray-400 hover:text-gray-600 transition-colors" title="{{ __('landing.hero_drag_hint') }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                            </div>
                            
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
                                <div class="text-sm font-bold text-gray-900">{{ $pop['product']['product_name'] ?? __('landing.popular_unknown') }}</div>
                                <div class="text-xs text-gray-400 mt-1">{{ __('landing.popular_sort') }}: {{ $pop['sort_order'] }}</div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <button wire:click="deletePopularProduct({{ $pop['id'] }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:loading.attr="disabled" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-50">
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
                    <h2 class="text-xl font-bold text-gray-900">{{ __('landing.banner_title') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('landing.banner_subtitle') }}</p>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': open === 'banner'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open === 'banner'" x-collapse class="px-6 pb-6 border-t border-gray-100 pt-4">
                <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
                    <div>
                        <span class="font-semibold text-gray-900 block">{{ __('landing.banner_status') }}</span>
                        <span class="text-sm text-gray-500">{{ __('landing.banner_status_desc') }}</span>
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
                            <span class="font-bold">{{ __('landing.banner_recommended') }}</span> 1200x450px (16:6), JPG/PNG, Max 2MB
                        </div>
                    </div>

                    @if($bannerCta && isset($bannerCta['image']))
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('landing.banner_current_image') }}</label>
                            <div class="w-full max-w-2xl aspect-[16/6] bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                <img src="{{ asset('storage/' . $bannerCta['image']) }}" class="w-full h-full object-cover">
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.banner_upload_new') }} @if(!$bannerCta) <span class="text-red-500">*</span> @endif</label>
                            <input type="file" wire:model="newBannerImage" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-md p-1 bg-white">
                            @error('newBannerImage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.banner_label_title') }} <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="newBannerTitle" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            @error('newBannerTitle') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.banner_label_desc') }}</label>
                        <textarea wire:model="newBannerDescription" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"></textarea>
                        @error('newBannerDescription') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.banner_label_link') }} <span class="text-red-500">*</span></label>
                        <input type="url" wire:model="newBannerLink" placeholder="https://example.com" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        @error('newBannerLink') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mt-4 flex items-center gap-4">
                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-500 transition-colors inline-flex items-center" wire:loading.attr="disabled" wire:target="saveBannerCta, newBannerImage">
                            <span wire:loading.remove wire:target="saveBannerCta">{{ __('landing.banner_save_btn') }}</span>
                            <span wire:loading wire:target="saveBannerCta"><span class="loader"></span>Saving...</span>
                        </button>
                        <div wire:loading wire:target="newBannerImage" class="text-xs text-primary-600 flex items-center">
                            <span class="loader"></span> Memproses banner...
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <button @click="open = open === 'review' ? null : 'review'" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                <div class="flex flex-col items-start text-left">
                    <h2 class="text-xl font-bold text-gray-900">{{ __('landing.review_title') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('landing.review_subtitle') }}</p>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': open === 'review'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open === 'review'" x-collapse class="px-6 pb-6 border-t border-gray-100 pt-4">
                @if(count($reviews) < 8)
                    <form wire:submit.prevent="saveReview" class="bg-gray-50 rounded-lg p-5 mb-6 border border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-4">{{ __('landing.review_add_title') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.review_label_reviewer') }} <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="newReviewerName" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                @error('newReviewerName') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.review_label_client') }} <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="newClientName" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                @error('newClientName') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.review_label_text') }} <span class="text-red-500">*</span></label>
                            <textarea wire:model="newReviewText" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"></textarea>
                            @error('newReviewText') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        @error('newReview') <div class="text-red-500 text-sm mt-3">{{ $message }}</div> @enderror
                        <div>
                            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-500 transition-colors" wire:loading.attr="disabled" wire:target="saveReview">
                                <span wire:loading.remove wire:target="saveReview">{{ __('landing.review_add_btn') }}</span>
                                <span wire:loading wire:target="saveReview">{{ __('landing.value_saving') }}</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bg-blue-50 text-blue-700 p-4 rounded-lg mb-6 border border-blue-100 text-sm">
                        {{ __('landing.review_limit_reached') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="sortableList('updateReviewOrder')">
                    @foreach($reviews as $review)
                        <div data-id="{{ $review['id'] }}" class="border border-gray-200 rounded-xl p-5 relative hover:border-primary-300 transition-colors bg-white group" wire:loading.class="opacity-50 pointer-events-none" wire:target="saveReview, updateReview">
                            <!-- Drag Handle -->
                            <div class="drag-handle absolute top-3 left-3 cursor-grab active:cursor-grabbing p-1.5 text-gray-400 hover:text-gray-600 transition-colors" title="{{ __('landing.hero_drag_hint') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                            </div>

                            @if($editReviewId === $review['id'])
                                <!-- Edit Mode -->
                                <div class="mt-8 flex flex-col gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('landing.review_edit_text') }}</label>
                                        <textarea wire:model="editReviewText" rows="3" class="w-full rounded border-gray-300 text-sm py-1.5 px-3"></textarea>
                                        @error('editReviewText') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('landing.review_edit_reviewer') }}</label>
                                            <input type="text" wire:model="editReviewerName" class="w-full rounded border-gray-300 text-sm py-1.5 px-3">
                                            @error('editReviewerName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('landing.review_edit_client') }}</label>
                                            <input type="text" wire:model="editClientName" class="w-full rounded border-gray-300 text-sm py-1.5 px-3">
                                            @error('editClientName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="flex gap-2 mt-2">
                                        <button wire:click="updateReview" wire:loading.attr="disabled" class="bg-primary-600 text-white px-3 py-1.5 rounded text-xs font-medium hover:bg-primary-500 disabled:opacity-50">{{ __('landing.hero_save') }}</button>
                                        <button wire:click="cancelEditReview" wire:loading.attr="disabled" class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded text-xs font-medium hover:bg-gray-300 disabled:opacity-50">{{ __('landing.hero_cancel') }}</button>
                                    </div>
                                </div>
                            @else
                                <!-- Display Mode -->
                                <div class="absolute top-3 right-3 flex gap-1">
                                    <button wire:click="editReview({{ $review['id'] }})" wire:loading.attr="disabled" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors disabled:opacity-50" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button wire:click="deleteReview({{ $review['id'] }})" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:loading.attr="disabled" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-50" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                                <p class="text-sm text-gray-700 mb-4 pr-16 mt-6 italic">"{{ $review['review_text'] }}"</p>
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-900 text-sm">{{ $review['reviewer_name'] }}</span>
                                    <span class="text-xs text-gray-500">{{ $review['client_name'] }}</span>
                                </div>
                                <div class="text-xs text-gray-400 mt-3 border-t border-gray-100 pt-2">{{ __('landing.review_sort') }}: {{ $review['sort_order'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <button @click="open = open === 'footer' ? null : 'footer'" class="w-full flex items-center justify-between p-6 bg-white hover:bg-gray-50 transition-colors focus:outline-none">
                <div class="flex flex-col items-start text-left">
                    <h2 class="text-xl font-bold text-gray-900">{{ __('landing.footer_title') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ __('landing.footer_subtitle') }}</p>
                </div>
                <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': open === 'footer'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div x-show="open === 'footer'" x-collapse class="px-6 pb-6 border-t border-gray-100 pt-4">
                <form wire:submit.prevent="saveFooter" class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-4">{{ __('landing.footer_general_title') }}</h3>
                    
                    @if($footerLogoPath)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('landing.footer_current_logo') }}</label>
                            <div class="p-4 bg-white rounded-lg inline-block border border-gray-200">
                                <img src="{{ asset('storage/' . $footerLogoPath) }}" alt="Footer Logo" class="h-10 w-auto object-contain">
                            </div>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.footer_upload_logo') }}</label>
                        <input type="file" wire:model="footerLogo" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-md p-1 bg-white">
                        @error('footerLogo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        <div wire:loading wire:target="footerLogo" class="text-xs text-primary-600 mt-1">Uploading logo...</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.footer_label_company') }} <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="footerCompanyName" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            @error('footerCompanyName') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.footer_label_phone') }}</label>
                            <input type="text" wire:model="footerPhone" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            @error('footerPhone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.footer_label_email') }}</label>
                            <input type="email" wire:model="footerEmail" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                            @error('footerEmail') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.footer_label_address') }}</label>
                        <textarea wire:model="footerAddress" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"></textarea>
                        @error('footerAddress') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.footer_label_desc') }}</label>
                        <textarea wire:model="footerDescription" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"></textarea>
                        @error('footerDescription') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <h3 class="font-semibold text-gray-700 mb-4 pt-4 border-t border-gray-200">{{ __('landing.footer_social_title') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.footer_youtube') }}</label>
                            <input type="url" wire:model="footerYoutubeUrl" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.footer_instagram') }}</label>
                            <input type="url" wire:model="footerInstagramUrl" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.footer_tiktok') }}</label>
                            <input type="url" wire:model="footerTiktokUrl" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.footer_tokopedia') }}</label>
                            <input type="url" wire:model="footerTokopediaUrl" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.footer_shopee') }}</label>
                            <input type="url" wire:model="footerShopeeUrl" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('landing.footer_facebook') }}</label>
                            <input type="url" wire:model="footerFacebookUrl" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-500 transition-colors" wire:loading.attr="disabled" wire:target="saveFooter">
                            <span wire:loading.remove wire:target="saveFooter">{{ __('landing.footer_save_btn') }}</span>
                            <span wire:loading wire:target="saveFooter">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SortableJS and Alpine Integration -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sortableList', (updateMethod) => ({
                init() {
                    if (typeof Sortable !== 'undefined') {
                        new Sortable(this.$el, {
                            animation: 150,
                            handle: '.drag-handle',
                            ghostClass: 'opacity-50',
                            onEnd: (evt) => {
                                // Get all item IDs in the new order
                                let items = Array.from(this.$el.children);
                                let ids = items.map(item => item.getAttribute('data-id')).filter(id => id);
                                
                                if (ids.length > 0) {
                                    this.$wire[updateMethod](ids);
                                }
                            }
                        });
                    }
                }
            }));
        });
    </script>
</x-filament-panels::page>
