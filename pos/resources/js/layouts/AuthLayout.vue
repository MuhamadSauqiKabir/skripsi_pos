<template>
    <div class="login-shell">
        <section class="login-panel login-copy">
            <p class="eyebrow">Nineties Coffee</p>
            <h1>Vintage Elegant POS</h1>
            <p class="muted">
                Sistem Point of Sales berbasis web untuk QR meja, pembayaran QRIS, dan dashboard operasional multipage.
            </p>

            <div class="highlight-grid">
                <div class="highlight-card">
                    <span class="caption">Secure Access</span>
                    <strong>RBAC 4 role</strong>
                </div>
                <div class="highlight-card">
                    <span class="caption">Realtime Ops</span>
                    <strong>Order + Stock Alert</strong>
                </div>
                <div class="highlight-card">
                    <span class="caption">Business Insight</span>
                    <strong>Profit + Prediction</strong>
                </div>
            </div>
        </section>

        <section class="login-panel">
            <slot />
        </section>
    </div>
</template>
