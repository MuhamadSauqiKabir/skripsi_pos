<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@400;600;700;900&family=Be+Vietnam+Pro:wght@400;500;600;700&family=Material+Symbols+Outlined:wght@100..700" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            brand: {
                                gold: '#D4AF37',
                                cream: '#F5F5DC',
                                coffee: '#4B3621',
                                espresso: '#2A1F16',
                                mist: '#FBF8F1',
                                bronze: '#8C6A43',
                            },
                        },
                        fontFamily: {
                            serif: ['Noto Serif', 'serif'],
                            sans: ['Be Vietnam Pro', 'sans-serif'],
                        },
                        boxShadow: {
                            soft: '0 20px 45px rgba(42,31,22,0.10)',
                        },
                    },
                },
            };
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Nineties Coffee POS') }}</title>
        </x-inertia::head>
    </head>
    <body>
        <x-inertia::app />
    </body>
</html>
