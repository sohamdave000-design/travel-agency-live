// Multi-language support (English + Hindi)
const translations = {
    en: {
        // Homepage
        hero_title: "Discover the World with Us",
        hero_subtitle: "Explore breathtaking destinations, best hotels, and seamless travel experiences all in one place.",
        explore_btn: "Explore Packages",
        custom_btn: "Build Custom Tour",
        featured_services: "Featured Services",
        services_subtitle: "Comprehensive travel management services tailored for you",
        luxury_hotels: "Luxury Hotels",
        hotels_desc: "Book top-rated hotels at the best prices. Comfort guaranteed.",
        view_hotels: "View Hotels",
        bus_transport: "Bus Transport",
        bus_desc: "Safe and comfortable bus journey across the country.",
        book_bus_btn: "Book Bus",
        vehicle_rentals: "Vehicle Rentals",
        rentals_desc: "Rent cars, bikes, or cabs for your local commute.",
        rent_now: "Rent Now",
        // Packages
        explore_packages: "Explore Our Tour Packages",
        discover_text: "Discover the world's most amazing destinations with our curated travel packages.",
        search_dest: "Search Destination",
        search_btn: "Search Packages",
        // Hotels
        find_stay: "Find the Perfect Stay",
        // Buses
        book_bus: "Book Bus Tickets",
        // Reviews
        reviews_title: "Traveler's Reviews",
    },
    hi: {
        hero_title: "हमारे साथ दुनिया की खोज करें",
        hero_subtitle: "शानदार गंतव्य, बेहतरीन होटल और सहज यात्रा अनुभव एक ही स्थान पर।",
        explore_btn: "पैकेज देखें",
        custom_btn: "कस्टम टूर बनाएं",
        featured_services: "प्रमुख सेवाएं",
        services_subtitle: "आपके लिए व्यापक यात्रा प्रबंधन सेवाएं",
        luxury_hotels: "लग्जरी होटल",
        hotels_desc: "सर्वोत्तम मूल्यों पर शीर्ष रेटेड होटल बुक करें।",
        view_hotels: "होटल देखें",
        bus_transport: "बस ट्रांसपोर्ट",
        bus_desc: "देश भर में सुरक्षित और आरामदायक बस यात्रा।",
        book_bus_btn: "बस बुक करें",
        vehicle_rentals: "वाहन किराया",
        rentals_desc: "स्थानीय यात्रा के लिए कार, बाइक या कैब किराये पर लें।",
        rent_now: "अभी किराये पर लें",
        explore_packages: "हमारे टूर पैकेज देखें",
        discover_text: "हमारे चयनित ट्रैवल पैकेज के साथ दुनिया के सबसे अद्भुत गंतव्यों की खोज करें।",
        search_dest: "गंतव्य खोजें",
        search_btn: "पैकेज खोजें",
        find_stay: "सही ठहरने की जगह खोजें",
        book_bus: "बस टिकट बुक करें",
        reviews_title: "यात्रियों की समीक्षाएं",
    }
};

function setLanguage(lang) {
    localStorage.setItem('ta_lang', lang);
    document.querySelectorAll('[data-lang]').forEach(el => {
        const key = el.getAttribute('data-lang');
        if (translations[lang] && translations[lang][key]) {
            el.textContent = translations[lang][key];
        }
    });
    // Update switcher active state
    document.querySelectorAll('.lang-btn').forEach(btn => {
        btn.style.background = btn.dataset.lang === lang ? '#2563eb' : 'transparent';
        btn.style.color = btn.dataset.lang === lang ? 'white' : '#64748b';
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    const savedLang = localStorage.getItem('ta_lang') || 'en';
    setLanguage(savedLang);
});
