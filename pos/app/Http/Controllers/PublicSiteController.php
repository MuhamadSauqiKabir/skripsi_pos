<?php

namespace App\Http\Controllers;

use App\Models\DiningTable;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Inertia\Inertia;
use Inertia\Response;

class PublicSiteController extends Controller
{
    public function home(): Response
    {
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        return Inertia::render('Public/Home', $this->sharedProps([
            'featuredMenus' => $this->featuredMenus(),
            'heroContent' => [
                'title' => $settings->get('landing_hero_title') ?? 'Crafting the Perfect Cup, Every Time.',
                'subtitle' => $settings->get('landing_hero_subtitle') ?? 'Rasakan harmoni antara biji kopi pilihan dan suasana estetik yang menyatukan setiap cerita.',
            ],
        ], 'home'));
    }

    public function about(): Response
    {
        $settings = \App\Models\Setting::all()->pluck('value', 'key');

        return Inertia::render('Public/About', $this->sharedProps([
            'storyTimeline' => $settings->has('about_story_timeline') 
                ? json_decode($settings->get('about_story_timeline'), true) 
                : [
                    ['year' => '2018', 'title' => 'Awal Perjalanan', 'description' => 'Nineties Coffee lahir sebagai ruang hangat untuk kopi, cerita, dan kreativitas.'],
                    ['year' => '2020', 'title' => 'Refining the Craft', 'description' => 'Kami mematangkan karakter rasa, pelayanan, dan identitas visual yang lebih elegan.'],
                    ['year' => '2026', 'title' => 'Digital Experience', 'description' => 'Kini pengalaman cafe diperluas lewat POS, QR table ordering, dan QRIS realtime.'],
                ],
            'location' => [
                'address' => $settings->get('shop_address') ?? 'Jl. Senopati No. 90, Jakarta Selatan',
                'phone'   => $settings->get('shop_phone') ?? '0812-9000-1990',
                'email'   => $settings->get('shop_email') ?? 'hello@ninetiescoffee.id',
                'hours'   => $settings->get('shop_hours') ?? 'Setiap Hari 07:00 - 22:00',
            ],
        ], 'about'));
    }



    public function menu(): Response
    {
        return Inertia::render('Public/Menu', $this->sharedProps([
            'categories' => MenuCategory::query()
                ->with(['menuItems' => fn ($query) => $query->where('is_available', true)->orderBy('name')])
                ->orderBy('sort_order')
                ->get(),
            'tables' => DiningTable::query()->where('is_active', true)->orderBy('name')->get(['name', 'public_token', 'floor']),
        ], 'menu'));
    }

    public function contact(): Response
    {
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        
        return Inertia::render('Public/Contact', $this->sharedProps([
            'contacts' => [
                'address' => $settings->get('shop_address') ?? 'Jl. Senopati No. 90, Jakarta Selatan',
                'phone' => $settings->get('shop_phone') ?? '0812-9000-1990',
                'email' => $settings->get('shop_email') ?? 'hello@ninetiescoffee.id',
                'hours' => $settings->get('shop_hours') ?? 'Setiap Hari 07:00 - 22:00',
            ],
        ], 'contact'));
    }

    private function sharedProps(array $props, string $currentPage): array
    {
        return array_merge([
            'currentPage' => $currentPage,
            'publicNav' => [
                ['label' => 'Beranda', 'href' => route('public.home'), 'key' => 'home'],
                ['label' => 'About', 'href' => route('public.about'), 'key' => 'about'],
                ['label' => 'Pesan/Menu', 'href' => route('public.menu'), 'key' => 'menu'],
                ['label' => 'Contacts', 'href' => route('public.contact'), 'key' => 'contact'],
            ],
        ], $props);
    }

    private function featuredMenus()
    {
        return MenuItem::query()
            ->where('is_available', true)
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->take(6)
            ->get(['id', 'name', 'description', 'price']);
    }
}
