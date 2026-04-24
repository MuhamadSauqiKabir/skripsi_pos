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
        return Inertia::render('Public/Home', $this->sharedProps([
            'featuredMenus' => $this->featuredMenus(),
        ], 'home'));
    }

    public function about(): Response
    {
        return Inertia::render('Public/About', $this->sharedProps([
            'storyTimeline' => [
                ['year' => '2018', 'title' => 'Awal Perjalanan', 'description' => 'Nineties Coffee lahir sebagai ruang hangat untuk kopi, cerita, dan kreativitas.'],
                ['year' => '2020', 'title' => 'Refining the Craft', 'description' => 'Kami mematangkan karakter rasa, pelayanan, dan identitas visual yang lebih elegan.'],
                ['year' => '2026', 'title' => 'Digital Experience', 'description' => 'Kini pengalaman cafe diperluas lewat POS, QR table ordering, dan QRIS realtime.'],
            ],
        ], 'about'));
    }

    public function store(): Response
    {
        return Inertia::render('Public/Store', $this->sharedProps([
            'stores' => [
                ['name' => 'Nineties Senopati', 'hours' => '07:00 - 23:00', 'address' => 'Jl. Senopati No. 90, Jakarta Selatan'],
                ['name' => 'Nineties Cikajang', 'hours' => '08:00 - 22:00', 'address' => 'Jl. Cikajang No. 14, Jakarta Selatan'],
                ['name' => 'Nineties Tebet', 'hours' => '07:00 - 22:00', 'address' => 'Jl. Tebet Timur Dalam Raya No. 8, Jakarta Selatan'],
            ],
        ], 'store'));
    }

    public function menu(): Response
    {
        return Inertia::render('Public/Menu', $this->sharedProps([
            'categories' => MenuCategory::query()
                ->with(['menuItems' => fn ($query) => $query->where('is_available', true)->orderBy('name')])
                ->orderBy('sort_order')
                ->get(),
            'tables' => DiningTable::query()->where('is_active', true)->orderBy('name')->get(['name', 'public_token']),
        ], 'menu'));
    }

    public function contact(): Response
    {
        return Inertia::render('Public/Contact', $this->sharedProps([
            'contacts' => [
                'address' => 'Jl. Senopati No. 90, Jakarta Selatan',
                'phone' => '0812-9000-1990',
                'email' => 'hello@ninetiescoffee.id',
                'hours' => 'Setiap Hari 07:00 - 22:00',
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
                ['label' => 'Store', 'href' => route('public.store'), 'key' => 'store'],
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
