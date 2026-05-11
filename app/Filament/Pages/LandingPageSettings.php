<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\LandingHero;
use App\Models\LandingValue;
use App\Models\LandingClientLogo;
use App\Models\LandingCategory;
use App\Models\LandingSection;
use App\Models\LandingSectionSetting;
use App\Models\LandingPopularProduct;
use App\Models\LandingBannerCta;
use App\Models\LandingReview;
use App\Models\LandingFooter;
use App\Models\Product;
use Livewire\WithFileUploads;

class LandingPageSettings extends Page
{
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationGroup = 'Page Editor';
    protected static ?string $navigationLabel = 'Landing Page';
    protected static ?string $slug = 'landing-page';
    protected static string $view = 'filament.pages.landing-page-settings';

    public static function canAccess(): bool
    {
        return canAccessMenu('admin/landing-page');
    }

    public $heroes = [];
    public $values = [];
    public $logos = [];
    public $categories = [];
    public $popularProducts = [];
    public $availableProducts = [];
    public $bannerCta;
    public $reviews = [];
    public $footer;

    // Settings
    public $clientLogoActive = true;
    public $categoryRows = 1;
    public $bannerActive = true;

    // Form fields for new Hero
    public $newHeroImage;
    public $newHeroLink;

    // Form fields for new Value
    public $newValueImage;
    public $newValueTitle;
    public $newValueDescription;

    // Form fields for new Logo
    public $newLogoImage;

    // Form fields for new Category
    public $newCategoryImage;
    public $newCategoryTitle;
    public $newCategoryLink;

    // Form fields for Popular Products
    public $newPopularProductId;

    // Form fields for Banner CTA
    public $newBannerTitle;
    public $newBannerDescription;
    public $newBannerImage;
    public $newBannerLink;

    // Form fields for Reviews
    public $newReviewText;
    public $newReviewerName;
    public $newClientName;

    // Form fields for Footer
    public $footerCompanyName;
    public $footerAddress;
    public $footerPhone;
    public $footerEmail;
    public $footerYoutubeUrl;
    public $footerInstagramUrl;
    public $footerTiktokUrl;
    public $footerTokopediaUrl;
    public $footerShopeeUrl;
    public $footerFacebookUrl;

    public function mount()
    {
        $this->loadData();
        if ($this->bannerCta) {
            $this->newBannerTitle = $this->bannerCta['title'];
            $this->newBannerDescription = $this->bannerCta['description'];
            $this->newBannerLink = $this->bannerCta['link'];
        }
        if ($this->footer) {
            $this->footerCompanyName = $this->footer['company_name'];
            $this->footerAddress = $this->footer['address'];
            $this->footerPhone = $this->footer['phone'];
            $this->footerEmail = $this->footer['email'];
            $this->footerYoutubeUrl = $this->footer['youtube_url'];
            $this->footerInstagramUrl = $this->footer['instagram_url'];
            $this->footerTiktokUrl = $this->footer['tiktok_url'];
            $this->footerTokopediaUrl = $this->footer['tokopedia_url'];
            $this->footerShopeeUrl = $this->footer['shopee_url'];
            $this->footerFacebookUrl = $this->footer['facebook_url'];
        }
    }

    public function loadData()
    {
        $this->heroes = LandingHero::orderBy('sort_order')->get()->toArray();
        $this->values = LandingValue::orderBy('sort_order')->get()->toArray();
        $this->logos = LandingClientLogo::orderBy('sort_order')->get()->toArray();
        $this->categories = LandingCategory::orderBy('sort_order')->get()->toArray();
        $this->popularProducts = LandingPopularProduct::with('product')->orderBy('sort_order')->get()->toArray();
        $this->availableProducts = Product::where('is_active', true)->get()->toArray();
        $this->reviews = LandingReview::orderBy('sort_order')->get()->toArray();

        $clientLogoSection = LandingSection::firstOrCreate(['key' => 'client_logo']);
        $this->clientLogoActive = (bool) $clientLogoSection->is_active;

        $categoryRowsSetting = LandingSectionSetting::firstOrCreate(['key' => 'category_rows'], ['value' => '1']);
        $this->categoryRows = (int) $categoryRowsSetting->value;

        $banner = LandingBannerCta::first();
        if ($banner) {
            $this->bannerCta = $banner->toArray();
            $this->bannerActive = (bool) $banner->is_active;
        }

        $footer = LandingFooter::first();
        if ($footer) {
            $this->footer = $footer->toArray();
        }
    }

    public function toggleClientLogoSection()
    {
        $this->clientLogoActive = !$this->clientLogoActive;
        LandingSection::where('key', 'client_logo')->update(['is_active' => $this->clientLogoActive]);
    }

    public function updatedCategoryRows($value)
    {
        LandingSectionSetting::where('key', 'category_rows')->update(['value' => $value]);
    }

    // Hero Methods
    public function saveHero()
    {
        if (count($this->heroes) >= 5) {
            $this->addError('newHero', 'Maximum 5 hero images allowed.');
            return;
        }

        $this->validate([
            'newHeroImage' => 'required|image|max:2048',
            'newHeroLink' => 'nullable|url',
        ]);

        $path = $this->newHeroImage->store('landing-heroes', 'public');

        LandingHero::create([
            'image' => $path,
            'link' => $this->newHeroLink,
            'sort_order' => count($this->heroes) + 1,
            'is_active' => true,
        ]);

        $this->reset(['newHeroImage', 'newHeroLink']);
        $this->loadData();
        
        \Filament\Notifications\Notification::make()
            ->title('Hero image uploaded successfully')
            ->success()
            ->send();
    }

    public function deleteHero($id)
    {
        LandingHero::find($id)?->delete();
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title('Hero image deleted')
            ->success()
            ->send();
    }

    public function toggleHeroActive($id)
    {
        $hero = LandingHero::find($id);
        if ($hero) {
            $hero->is_active = !$hero->is_active;
            $hero->save();
            $this->loadData();

            \Filament\Notifications\Notification::make()
                ->title('Hero status updated')
                ->success()
                ->send();
        }
    }

    // Edit Hero states
    public $editHeroId = null;
    public $editHeroImage;
    public $editHeroLink;

    public function editHero($id)
    {
        $hero = LandingHero::find($id);
        if ($hero) {
            $this->editHeroId = $hero->id;
            $this->editHeroLink = $hero->link;
            $this->editHeroImage = null; // Reset image upload
        }
    }

    public function cancelEditHero()
    {
        $this->editHeroId = null;
        $this->editHeroLink = null;
        $this->editHeroImage = null;
    }

    public function updateHero()
    {
        $hero = LandingHero::find($this->editHeroId);
        if (!$hero) return;

        $this->validate([
            'editHeroImage' => 'nullable|image|max:2048',
            'editHeroLink' => 'nullable|url',
        ]);

        if ($this->editHeroImage) {
            $hero->image = $this->editHeroImage->store('landing-heroes', 'public');
        }
        $hero->link = $this->editHeroLink;
        $hero->save();

        $this->cancelEditHero();
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title('Hero updated successfully')
            ->success()
            ->send();
    }

    public function updateHeroOrder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            LandingHero::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        $this->loadData();
    }

    // Value Methods
    public function saveValue()
    {
        if (count($this->values) >= 3) {
            $this->addError('newValue', 'Maximum 3 value cards allowed.');
            return;
        }

        $this->validate([
            'newValueImage' => 'required|image|max:2048',
            'newValueTitle' => 'required|string|max:255',
            'newValueDescription' => 'required|string',
        ]);

        $path = $this->newValueImage->store('landing-values', 'public');

        LandingValue::create([
            'image' => $path,
            'title' => $this->newValueTitle,
            'description' => $this->newValueDescription,
            'sort_order' => count($this->values) + 1,
        ]);

        $this->reset(['newValueImage', 'newValueTitle', 'newValueDescription']);
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title('Value card added successfully')
            ->success()
            ->send();
    }

    public function deleteValue($id)
    {
        LandingValue::find($id)?->delete();
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title('Value card deleted')
            ->success()
            ->send();
    }

    // Edit Value states
    public $editValueId = null;
    public $editValueImage;
    public $editValueTitle;
    public $editValueDescription;

    public function editValue($id)
    {
        $value = LandingValue::find($id);
        if ($value) {
            $this->editValueId = $value->id;
            $this->editValueTitle = $value->title;
            $this->editValueDescription = $value->description;
            $this->editValueImage = null; // Reset image upload
        }
    }

    public function cancelEditValue()
    {
        $this->editValueId = null;
        $this->editValueTitle = null;
        $this->editValueDescription = null;
        $this->editValueImage = null;
    }

    public function updateValue()
    {
        $value = LandingValue::find($this->editValueId);
        if (!$value) return;

        $this->validate([
            'editValueImage' => 'nullable|image|max:2048',
            'editValueTitle' => 'required|string|max:255',
            'editValueDescription' => 'required|string',
        ]);

        if ($this->editValueImage) {
            $value->image = $this->editValueImage->store('landing-values', 'public');
        }
        $value->title = $this->editValueTitle;
        $value->description = $this->editValueDescription;
        $value->save();

        $this->cancelEditValue();
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title('Value card updated successfully')
            ->success()
            ->send();
    }

    public function updateValueOrder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            LandingValue::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        $this->loadData();
    }

    // Logo Methods
    public function saveLogo()
    {
        if (count($this->logos) >= 6) {
            $this->addError('newLogo', 'Maximum 6 logos allowed.');
            return;
        }

        $this->validate([
            'newLogoImage' => 'required|image|max:2048',
        ]);

        $path = $this->newLogoImage->store('landing-logos', 'public');

        LandingClientLogo::create([
            'image' => $path,
            'is_active' => true,
            'sort_order' => count($this->logos) + 1,
        ]);

        $this->reset(['newLogoImage']);
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title('Logo uploaded successfully')
            ->success()
            ->send();
    }

    public function deleteLogo($id)
    {
        LandingClientLogo::find($id)?->delete();
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title('Logo deleted')
            ->success()
            ->send();
    }

    public function toggleLogoActive($id)
    {
        $logo = LandingClientLogo::find($id);
        if ($logo) {
            $logo->is_active = !$logo->is_active;
            $logo->save();
            $this->loadData();

            \Filament\Notifications\Notification::make()
                ->title('Logo status updated')
                ->success()
                ->send();
        }
    }

    // Category Methods
    public function saveCategory()
    {
        if (count($this->categories) >= 6) {
            $this->addError('newCategory', 'Maximum 6 categories allowed.');
            return;
        }

        $this->validate([
            'newCategoryImage' => 'required|image|max:2048',
            'newCategoryTitle' => 'required|string|max:255',
            'newCategoryLink' => 'required|string|max:255',
        ]);

        $path = $this->newCategoryImage->store('landing-categories', 'public');

        LandingCategory::create([
            'image' => $path,
            'title' => $this->newCategoryTitle,
            'link' => $this->newCategoryLink,
            'sort_order' => count($this->categories) + 1,
        ]);

        $this->reset(['newCategoryImage', 'newCategoryTitle', 'newCategoryLink']);
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title('Category added successfully')
            ->success()
            ->send();
    }

    public function deleteCategory($id)
    {
        LandingCategory::find($id)?->delete();
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title('Category deleted')
            ->success()
            ->send();
    }

    // Popular Products Methods
    public function savePopularProduct()
    {
        if (count($this->popularProducts) >= 4) {
            $this->addError('newPopularProduct', 'Maximum 4 products allowed.');
            return;
        }

        $this->validate([
            'newPopularProductId' => 'required|exists:products,id|unique:landing_popular_products,product_id',
        ]);

        LandingPopularProduct::create([
            'product_id' => $this->newPopularProductId,
            'sort_order' => count($this->popularProducts) + 1,
        ]);

        $this->reset(['newPopularProductId']);
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title('Product added to popular section')
            ->success()
            ->send();
    }

    public function deletePopularProduct($id)
    {
        LandingPopularProduct::find($id)?->delete();
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title('Product removed from popular section')
            ->success()
            ->send();
    }

    public function updatePopularProductOrder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            LandingPopularProduct::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        $this->loadData();
    }

    // Banner CTA Methods
    public function toggleBannerActive()
    {
        $this->bannerActive = !$this->bannerActive;
        if ($this->bannerCta) {
            LandingBannerCta::where('id', $this->bannerCta['id'])->update(['is_active' => $this->bannerActive]);
            
            \Filament\Notifications\Notification::make()
                ->title('Banner status updated')
                ->success()
                ->send();
        }
    }

    public function saveBannerCta()
    {
        $rules = [
            'newBannerTitle' => 'required|string|max:255',
            'newBannerDescription' => 'nullable|string',
            'newBannerLink' => 'required|url',
        ];

        if (!$this->bannerCta) {
            $rules['newBannerImage'] = 'required|image|max:2048';
        } else {
            $rules['newBannerImage'] = 'nullable|image|max:2048';
        }

        $this->validate($rules);

        $data = [
            'title' => $this->newBannerTitle,
            'description' => $this->newBannerDescription,
            'link' => $this->newBannerLink,
            'is_active' => $this->bannerActive,
        ];

        if ($this->newBannerImage) {
            $data['image'] = $this->newBannerImage->store('landing-banner', 'public');
        } elseif ($this->bannerCta) {
            $data['image'] = $this->bannerCta['image'];
        }

        if ($this->bannerCta) {
            LandingBannerCta::where('id', $this->bannerCta['id'])->update($data);
        } else {
            LandingBannerCta::create($data);
        }

        $this->reset(['newBannerImage']);
        $this->loadData();
        $this->bannerCta = LandingBannerCta::first()->toArray();

        \Filament\Notifications\Notification::make()
            ->title('Banner settings saved successfully')
            ->success()
            ->send();
    }

    // Review Methods
    public function saveReview()
    {
        if (count($this->reviews) >= 8) {
            $this->addError('newReview', 'Maximum 8 reviews allowed.');
            return;
        }

        $this->validate([
            'newReviewText' => 'required|string',
            'newReviewerName' => 'required|string|max:255',
            'newClientName' => 'required|string|max:255',
        ]);

        LandingReview::create([
            'review_text' => $this->newReviewText,
            'reviewer_name' => $this->newReviewerName,
            'client_name' => $this->newClientName,
            'sort_order' => count($this->reviews) + 1,
        ]);

        $this->reset(['newReviewText', 'newReviewerName', 'newClientName']);
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title('Review added successfully')
            ->success()
            ->send();
    }

    public function deleteReview($id)
    {
        LandingReview::find($id)?->delete();
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title('Review deleted')
            ->success()
            ->send();
    }

    // Edit Review states
    public $editReviewId = null;
    public $editReviewText;
    public $editReviewerName;
    public $editClientName;

    public function editReview($id)
    {
        $review = LandingReview::find($id);
        if ($review) {
            $this->editReviewId = $review->id;
            $this->editReviewText = $review->review_text;
            $this->editReviewerName = $review->reviewer_name;
            $this->editClientName = $review->client_name;
        }
    }

    public function cancelEditReview()
    {
        $this->editReviewId = null;
        $this->editReviewText = null;
        $this->editReviewerName = null;
        $this->editClientName = null;
    }

    public function updateReview()
    {
        $review = LandingReview::find($this->editReviewId);
        if (!$review) return;

        $this->validate([
            'editReviewText' => 'required|string',
            'editReviewerName' => 'required|string|max:255',
            'editClientName' => 'required|string|max:255',
        ]);

        $review->review_text = $this->editReviewText;
        $review->reviewer_name = $this->editReviewerName;
        $review->client_name = $this->editClientName;
        $review->save();

        $this->cancelEditReview();
        $this->loadData();

        \Filament\Notifications\Notification::make()
            ->title('Review updated successfully')
            ->success()
            ->send();
    }

    public function updateReviewOrder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            LandingReview::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        $this->loadData();
    }

    // Footer Methods
    public function saveFooter()
    {
        $this->validate([
            'footerCompanyName' => 'required|string|max:255',
            'footerAddress' => 'nullable|string',
            'footerPhone' => 'nullable|string|max:50',
            'footerEmail' => 'nullable|email|max:255',
            'footerYoutubeUrl' => 'nullable|url',
            'footerInstagramUrl' => 'nullable|url',
            'footerTiktokUrl' => 'nullable|url',
            'footerTokopediaUrl' => 'nullable|url',
            'footerShopeeUrl' => 'nullable|url',
            'footerFacebookUrl' => 'nullable|url',
        ]);

        $data = [
            'company_name' => $this->footerCompanyName,
            'address' => $this->footerAddress,
            'phone' => $this->footerPhone,
            'email' => $this->footerEmail,
            'youtube_url' => $this->footerYoutubeUrl,
            'instagram_url' => $this->footerInstagramUrl,
            'tiktok_url' => $this->footerTiktokUrl,
            'tokopedia_url' => $this->footerTokopediaUrl,
            'shopee_url' => $this->footerShopeeUrl,
            'facebook_url' => $this->footerFacebookUrl,
        ];

        if ($this->footer) {
            LandingFooter::where('id', $this->footer['id'])->update($data);
        } else {
            LandingFooter::create($data);
        }

        $this->loadData();
        $this->footer = LandingFooter::first()->toArray();

        \Filament\Notifications\Notification::make()
            ->title('Footer settings saved successfully')
            ->success()
            ->send();
    }
}
