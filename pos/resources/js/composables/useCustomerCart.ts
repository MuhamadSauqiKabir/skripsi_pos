const keyFor = (token: string) => `nineties-cart:${token}`;

export type CartItem = {
    menu_item_id: number;
    name: string;
    price: number;
    quantity: number;
    notes: string;
};

export function loadCart(token: string): CartItem[] {
    if (typeof window === 'undefined') return [];

    const raw = window.sessionStorage.getItem(keyFor(token));
    return raw ? (JSON.parse(raw) as CartItem[]) : [];
}

export function saveCart(token: string, items: CartItem[]): void {
    if (typeof window === 'undefined') return;
    window.sessionStorage.setItem(keyFor(token), JSON.stringify(items));
}

export function clearCart(token: string): void {
    if (typeof window === 'undefined') return;
    window.sessionStorage.removeItem(keyFor(token));
}
