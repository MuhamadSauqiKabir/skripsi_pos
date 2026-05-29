import { computed, ref } from 'vue';

type Language = 'IND' | 'EN';

const language = ref<Language>('IND');

if (typeof window !== 'undefined') {
    const saved = window.localStorage.getItem('nineties-language');

    if (saved === 'EN' || saved === 'IND') {
        language.value = saved;
    }
}

const dictionary = {
    IND: {
        home: 'Beranda',
        menu: 'Menu',
        about: 'Tentang',
        store: 'Gerai',
        contact: 'Kontak',
        staffLogin: 'Masuk Staf',
        visitStore: 'Lihat Situs',
        dashboard: 'Dasbor',
        notifications: 'Notifikasi',
        markRead: 'Tandai dibaca',
        languageChanged: 'Bahasa diubah ke Indonesia',
    },
    EN: {
        home: 'Home',
        menu: 'Menu',
        about: 'About',
        store: 'Store',
        contact: 'Contact',
        staffLogin: 'Staff Login',
        visitStore: 'Visit Website',
        dashboard: 'Dashboard',
        notifications: 'Notifications',
        markRead: 'Mark all read',
        languageChanged: 'Language changed to English',
    },
};

export function useLanguage() {
    const labels = computed(() => dictionary[language.value]);

    const setLanguage = (value: Language) => {
        language.value = value;
        if (typeof window !== 'undefined') {
            window.localStorage.setItem('nineties-language', value);
        }
    };

    const toggleLanguage = () => {
        setLanguage(language.value === 'IND' ? 'EN' : 'IND');
    };

    return {
        language,
        labels,
        setLanguage,
        toggleLanguage,
    };
}
