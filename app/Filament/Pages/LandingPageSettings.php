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
    }

    public function deleteHero($id)
    {
        LandingHero::find($id)?->delete();
        $this->loadData();
    }

    public function toggleHeroActive($id)
    {
        $hero = LandingHero::find($id);
        if ($hero) {
            $hero->is_active = !$hero->is_active;
            $hero->save();
            $this->loadData();
        }
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
    }

    public function deleteValue($id)
    {
        LandingValue::find($id)?->delete();
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
    }

    public function deleteLogo($id)
    {
        LandingClientLogo::find($id)?->delete();
        $this->loadData();
    }

    public function toggleLogoActive($id)
    {
        $logo = LandingClientLogo::find($id);
        if ($logo) {
            $logo->is_active = !$logo->is_active;
            $logo->save();
            $this->loadData();
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
    }

    public function deleteCategory($id)
    {
        LandingCategory::find($id)?->delete();
        $this->loadData();
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
    }

    public function deletePopularProduct($id)
    {
        LandingPopularProduct::find($id)?->delete();
        $this->loadData();
    }

    // Banner CTA Methods
    public function toggleBannerActive()
    {
        $this->bannerActive = !$this->bannerActive;
        if ($this->bannerCta) {
            LandingBannerCta::where('id', $this->bannerCta['id'])->update(['is_active' => $this->bannerActive]);
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
    }

    public function deleteReview($id)
    {
        LandingReview::find($id)?->delete();
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
    }
}
