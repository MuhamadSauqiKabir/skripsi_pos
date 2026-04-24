<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Nineties Coffee POS" />

    <div class="landing-page">
        <section class="hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Prototype Evolutionary</p>
                <h1>
                    Point of Sales berbasis web untuk
                    <span>Nineties Coffee</span>
                </h1>
                <p class="hero-text">
                    Sistem ini menggabungkan pemesanan meja via QR code, pembayaran QRIS Xendit,
                    dashboard bisnis, peringatan stok, dan prediksi menu populer minggu depan.
                </p>

                <div class="action-row">
                    <Link class="primary-button" href="/login">Login Internal</Link>
                    <a class="secondary-button" href="#features">Lihat Fitur</a>
                </div>
            </div>

            <div class="hero-card">
                <div class="metric-card">
                    <span>Dynamic Table QR</span>
                    <strong>4 meja aktif</strong>
                </div>
                <div class="metric-card">
                    <span>QRIS & Receipt</span>
                    <strong>Realtime settlement</strong>
                </div>
                <div class="metric-card">
                    <span>Predictive Stock</span>
                    <strong>Trend-aware planning</strong>
                </div>
            </div>
        </section>

        <section id="features" class="feature-grid">
            <article class="feature-card">
                <h2>QR Meja Terkoordinat</h2>
                <p>Setiap meja punya token unik dan posisi denah untuk mempermudah mapping pesanan.</p>
            </article>
            <article class="feature-card">
                <h2>Behavioral Discount</h2>
                <p>Diskon otomatis untuk pelanggan loyal dan flash sale happy hour.</p>
            </article>
            <article class="feature-card">
                <h2>Owner Dashboard</h2>
                <p>Rekap revenue, gross profit, rating pelanggan, efisiensi meja, dan top menu.</p>
            </article>
        </section>
    </div>
</template>
