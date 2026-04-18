<?php
require_once 'config/database.php';
include 'includes/header.php';

// Fetch recommended/trending packages
$recommended = [];
if (isLoggedIn()) {
    $user_dests = $pdo->prepare("
        SELECT DISTINCT p.destination FROM bookings b 
        JOIN packages p ON b.item_id = p.id 
        WHERE b.user_id = ? AND b.booking_type = 'package'
    ");
    $user_dests->execute([$_SESSION['user_id']]);
    $booked_dests = $user_dests->fetchAll(PDO::FETCH_COLUMN);
    if (!empty($booked_dests)) {
        $placeholders = str_repeat('?,', count($booked_dests) - 1) . '?';
        $rec = $pdo->prepare("SELECT * FROM packages WHERE destination NOT IN ($placeholders) ORDER BY RAND() LIMIT 3");
        $rec->execute($booked_dests);
        $recommended = $rec->fetchAll();
    }
}
if (empty($recommended)) {
    $recommended = $pdo->query("SELECT * FROM packages ORDER BY RAND() LIMIT 3")->fetchAll();
}

// Stats
$total_packages  = $pdo->query("SELECT COUNT(*) FROM packages")->fetchColumn();
$total_hotels    = $pdo->query("SELECT COUNT(*) FROM hotels")->fetchColumn();
$total_bookings  = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='confirmed'")->fetchColumn();
$total_cities    = $pdo->query("SELECT COUNT(DISTINCT city) FROM rentals")->fetchColumn();

// Latest reviews
$reviews = $pdo->query("SELECT r.rating, r.comment, u.name FROM reviews r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC LIMIT 3")->fetchAll();

// All rental cities dynamically from DB
$rental_cities = $pdo->query("SELECT DISTINCT city FROM rentals ORDER BY city ASC")->fetchAll(PDO::FETCH_COLUMN);

// City image map - covers all major Indian cities + fallback
$city_images = [
    'Goa'          => 'https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?auto=format&fit=crop&w=600&q=80',
    'Manali'       => 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=600&q=80',
    'Kashmir'      => 'https://images.unsplash.com/photo-1566837945700-30057527ade0?auto=format&fit=crop&w=600&q=80',
    'Jaipur'       => 'https://images.unsplash.com/photo-1599661046289-e31897846e41?auto=format&fit=crop&w=600&q=80',
    'Kerala'       => 'https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?auto=format&fit=crop&w=600&q=80',
    'Mumbai'       => 'https://images.unsplash.com/photo-1529253355930-ddbe423a2ac7?auto=format&fit=crop&w=600&q=80',
    'Delhi'        => 'https://images.unsplash.com/photo-1587474260584-136574528ed5?auto=format&fit=crop&w=600&q=80',
    'Agra'         => 'https://images.unsplash.com/photo-1564507592333-c60657eea523?auto=format&fit=crop&w=600&q=80',
    'Varanasi'     => 'https://images.unsplash.com/photo-1561361058-c24cecae35ca?auto=format&fit=crop&w=600&q=80',
    'Rajasthan'    => 'https://images.unsplash.com/photo-1477587458883-47145ed6979c?auto=format&fit=crop&w=600&q=80',
    'Ooty'         => 'https://images.unsplash.com/photo-1590050752117-238cb0fb12b1?auto=format&fit=crop&w=600&q=80',
    'Darjeeling'   => 'https://images.unsplash.com/photo-1598091383021-15ddea10925d?auto=format&fit=crop&w=600&q=80',
    'Shimla'       => 'https://images.unsplash.com/photo-1597739239353-50270c927586?auto=format&fit=crop&w=600&q=80',
    'Mysuru'       => 'https://images.unsplash.com/photo-1600100397608-c739f4db7c7e?auto=format&fit=crop&w=600&q=80',
    'Rishikesh'    => 'https://images.unsplash.com/photo-1591437932770-26e0128b33ce?auto=format&fit=crop&w=600&q=80',
    'Leh'          => 'https://images.unsplash.com/photo-1608306451433-0ef3f2b0c63a?auto=format&fit=crop&w=600&q=80',
    'Udaipur'      => 'https://images.unsplash.com/photo-1568495248636-6432b97bd949?auto=format&fit=crop&w=600&q=80',
    'Kolkata'      => 'https://images.unsplash.com/photo-1558431382-27e303142255?auto=format&fit=crop&w=600&q=80',
    'Hyderabad'    => 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?auto=format&fit=crop&w=600&q=80',
    'Bangalore'    => 'https://images.unsplash.com/photo-1596176530529-78163a4f7af2?auto=format&fit=crop&w=600&q=80',
    'Chennai'      => 'https://images.unsplash.com/photo-1582510003544-4d00b7f74220?auto=format&fit=crop&w=600&q=80',
    'Pune'         => 'assets/img/pune.png',
    'Ahmedabad'    => 'https://images.unsplash.com/photo-1559561853-08451507cbe7?auto=format&fit=crop&w=600&q=80',
    'Amritsar'     => 'https://images.unsplash.com/photo-1609167830220-7164aa360951?auto=format&fit=crop&w=600&q=80',
    'Chandigarh'   => 'assets/img/chandigarh.png',
    'Leh Ladakh'   => 'https://images.unsplash.com/photo-1544084944-15269ec7b5a0?auto=format&fit=crop&w=600&q=80',
    'Multiple'     => 'https://images.unsplash.com/photo-1506461883276-594a12b11cf3?auto=format&fit=crop&w=600&q=80',
    // Fallback for any city not in the map
    '_default'     => 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=600&q=80',
];
?>

<style>
/* ============================
   HOME PAGE STYLES
   ============================ */

/* --- Hero --- */
.home-hero {
    background: linear-gradient(135deg, rgba(10,15,40,0.88) 0%, rgba(37,99,235,0.75) 100%),
                url('https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover no-repeat;
    min-height: 88vh;
    margin: -2rem -2rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 4rem 2rem 5rem;
    position: relative;
}

.home-hero::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 80px;
    background: linear-gradient(to bottom, transparent, var(--bg-color));
}

.hero-content { position: relative; z-index: 1; max-width: 860px; }

.hero-eyebrow {
    display: inline-block;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    color: #bfdbfe;
    border: 1px solid rgba(255,255,255,0.25);
    padding: 0.4rem 1.25rem;
    border-radius: 9999px;
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    margin-bottom: 1.5rem;
}

.hero-title {
    font-size: clamp(2.5rem, 6vw, 4.5rem);
    font-weight: 800;
    color: white;
    line-height: 1.1;
    margin-bottom: 1.25rem;
    text-shadow: 0 2px 20px rgba(0,0,0,0.4);
}

.hero-title span { color: #60a5fa; }

.hero-sub {
    font-size: clamp(1rem, 2vw, 1.25rem);
    color: rgba(255,255,255,0.85);
    margin-bottom: 2.5rem;
    max-width: 640px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.7;
}

.hero-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }

.btn-hero-primary {
    background: linear-gradient(135deg, #2563eb, #4f46e5);
    color: white;
    padding: 0.9rem 2.25rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1.05rem;
    text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 20px rgba(37,99,235,0.5);
}
.btn-hero-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(37,99,235,0.6); }

.btn-hero-secondary {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(8px);
    color: white;
    padding: 0.9rem 2.25rem;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1.05rem;
    text-decoration: none;
    border: 2px solid rgba(255,255,255,0.4);
    transition: background 0.2s, transform 0.2s;
}
.btn-hero-secondary:hover { background: rgba(255,255,255,0.28); transform: translateY(-2px); }

/* --- Search bar --- */
.search-bar {
    background: white;
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 16px 48px rgba(37,99,235,0.15);
    display: flex;
    gap: 1rem;
    align-items: flex-end;
    flex-wrap: wrap;
    max-width: 800px;
    margin: 2rem auto 0;
}
.search-bar .form-group { flex: 1; min-width: 140px; margin: 0; }
.search-bar label { font-weight: 700; font-size: 0.8rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.06em; }
.search-bar select, .search-bar input { border: 2px solid #e2e8f0; border-radius: 10px; padding: 0.6rem 0.9rem; font-size: 0.95rem; width: 100%; background: #f8fafc; transition: border-color 0.2s; }
.search-bar select:focus, .search-bar input:focus { outline: none; border-color: #2563eb; background: white; }
.search-bar .btn-search { background: linear-gradient(135deg, #2563eb, #4f46e5); color: white; border: none; border-radius: 10px; padding: 0.7rem 1.5rem; font-weight: 700; font-size: 0.95rem; cursor: pointer; white-space: nowrap; transition: opacity 0.2s; }
.search-bar .btn-search:hover { opacity: 0.9; }

/* --- Stats strip --- */
.stats-strip {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    padding: 3rem 2rem;
    margin: 4rem -2rem;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 2rem;
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
}
.stat-item { }
.stat-number { font-size: 2.75rem; font-weight: 800; color: #60a5fa; line-height: 1; margin-bottom: 0.4rem; }
.stat-label { color: #94a3b8; font-size: 0.95rem; font-weight: 500; }

/* --- Section heading --- */
.section-heading { text-align: center; margin-bottom: 2.5rem; }
.section-heading .eyebrow { display: inline-block; color: #2563eb; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem; }
.section-heading h2 { font-size: clamp(1.75rem, 3vw, 2.5rem); font-weight: 800; color: #0f172a; margin-bottom: 0.75rem; }
.section-heading p { color: #64748b; font-size: 1.05rem; max-width: 560px; margin: 0 auto; }

/* --- Service cards row --- */
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 5rem;
}
.service-card {
    background: white;
    border-radius: 16px;
    padding: 2rem 1.75rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.service-card:hover { transform: translateY(-6px); box-shadow: 0 12px 32px rgba(37,99,235,0.12); border-color: #93c5fd; }
.service-icon { font-size: 2.25rem; }
.service-name { font-size: 1.2rem; font-weight: 700; color: #0f172a; }
.service-desc { font-size: 0.9rem; color: #64748b; line-height: 1.6; flex: 1; }
.service-link { color: #2563eb; font-weight: 600; font-size: 0.9rem; margin-top: 0.5rem; }

/* --- Package cards --- */
.pkg-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transition: transform 0.3s, box-shadow 0.3s;
    display: flex;
    flex-direction: column;
}
.pkg-card:hover { transform: translateY(-6px); box-shadow: 0 16px 36px rgba(37,99,235,0.12); }
.pkg-img { width: 100%; height: 200px; object-fit: cover; }
.pkg-badge { position: absolute; top: 0.75rem; left: 0.75rem; background: linear-gradient(135deg, #2563eb, #7c3aed); color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; }
.pkg-body { padding: 1.5rem; flex: 1; display: flex; flex-direction: column; }
.pkg-dest { color: #64748b; font-size: 0.85rem; margin-bottom: 0.35rem; }
.pkg-name { font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; }
.pkg-desc { font-size: 0.88rem; color: #64748b; line-height: 1.5; flex: 1; margin-bottom: 1rem; }
.pkg-footer { display: flex; justify-content: space-between; align-items: center; margin-top: auto; }
.pkg-price { font-size: 1.35rem; font-weight: 800; color: #2563eb; }
.pkg-price small { font-size: 0.8rem; font-weight: 400; color: #94a3b8; }

/* --- City destination grid --- */
.dest-city-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 1rem;
    margin-bottom: 5rem;
}
.dest-city-card {
    border-radius: 14px; overflow: hidden; position: relative; height: 150px;
    cursor: pointer; transition: transform 0.3s, box-shadow 0.3s; text-decoration: none; display: block;
}
.dest-city-card:hover { transform: translateY(-4px) scale(1.03); box-shadow: 0 12px 28px rgba(0,0,0,0.18); }
.dest-city-card img { width: 100%; height: 100%; object-fit: cover; }
.dest-city-card .overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 60%);
    display: flex; align-items: flex-end; padding: 0.85rem;
}
.dest-city-card .overlay span { color: white; font-weight: 700; font-size: 1rem; }

/* --- Why choose us --- */
.why-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 2rem;
    margin-bottom: 5rem;
}
.why-card {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-radius: 16px;
    padding: 2rem 1.5rem;
    border: 1px solid #bae6fd;
    text-align: center;
}
.why-icon { font-size: 2.5rem; margin-bottom: 1rem; }
.why-title { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; }
.why-text { font-size: 0.9rem; color: #475569; line-height: 1.6; }

/* --- Testimonials --- */
.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 5rem;
}
.testimonial-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    position: relative;
}
.testimonial-card::before { content: '"'; position: absolute; top: 1rem; right: 1.5rem; font-size: 4rem; color: #dbeafe; font-family: Georgia, serif; line-height: 1; }
.testimonial-stars { color: #f59e0b; font-size: 1.1rem; margin-bottom: 0.75rem; }
.testimonial-text { color: #475569; font-size: 0.95rem; line-height: 1.7; margin-bottom: 1.25rem; font-style: italic; }
.testimonial-author { font-weight: 700; color: #0f172a; }

/* --- CTA banner --- */
.cta-banner {
    background: linear-gradient(135deg, #2563eb 0%, #4f46e5 50%, #7c3aed 100%);
    border-radius: 20px;
    padding: 4rem 2rem;
    text-align: center;
    margin-bottom: 4rem;
    position: relative;
    overflow: hidden;
}
.cta-banner::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 250px; height: 250px;
    background: rgba(255,255,255,0.06);
    border-radius: 50%;
}
.cta-banner::after {
    content: '';
    position: absolute;
    bottom: -80px; left: -50px;
    width: 300px; height: 300px;
    background: rgba(255,255,255,0.04);
    border-radius: 50%;
}
.cta-banner h2 { font-size: 2.25rem; font-weight: 800; color: white; margin-bottom: 1rem; }
.cta-banner p { color: rgba(255,255,255,0.85); font-size: 1.1rem; margin-bottom: 2rem; }
.cta-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
.btn-cta-white { background: white; color: #2563eb; padding: 0.85rem 2rem; border-radius: 10px; font-weight: 700; text-decoration: none; transition: transform 0.2s, box-shadow 0.2s; }
.btn-cta-white:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
.btn-cta-outline { background: transparent; color: white; padding: 0.85rem 2rem; border-radius: 10px; font-weight: 700; text-decoration: none; border: 2px solid rgba(255,255,255,0.6); transition: background 0.2s; }
.btn-cta-outline:hover { background: rgba(255,255,255,0.15); }

/* --- Responsive --- */
@media (max-width: 768px) {
    .home-hero { min-height: 70vh; padding: 3rem 1.5rem 4rem; }
    .search-bar { flex-direction: column; }
    .stats-strip { margin: 3rem -1.5rem; }
    .cta-banner { padding: 3rem 1.5rem; }
    .cta-banner h2 { font-size: 1.75rem; }
}
</style>

<!-- ============================
     HERO SECTION
     ============================ -->
<div class="home-hero">
    <div class="hero-content">
        <div class="hero-eyebrow">India's Premier Travel Platform</div>
        <h1 class="hero-title">
            Discover <span>Incredible India</span><br>Your Way
        </h1>
        <p class="hero-sub">
            Book tour packages, luxury hotels, bus transfers and rent local vehicles across Goa, Manali, Kashmir & more — all in one place.
        </p>
        <div class="hero-btns">
            <a href="packages.html" class="btn-hero-primary">&#127757; Explore Packages</a>
            <a href="rentals.html" class="btn-hero-secondary">&#128663; Rent a Vehicle</a>
        </div>

        <!-- Inline search bar -->
        <form class="search-bar" action="packages.html" method="GET">
            <div class="form-group">
                <label>Destination</label>
                <input type="text" name="destination" placeholder="e.g. Goa, Manali, Kerala...">
            </div>
            <div class="form-group">
                <label>Service</label>
                <select name="type">
                    <option value="">All Services</option>
                    <option value="package">&#127757; Tour Package</option>
                    <option value="hotel">&#127970; Hotel</option>
                    <option value="bus">&#128652; Bus</option>
                    <option value="rental">&#128663; Rental</option>
                </select>
            </div>
            <button type="submit" class="btn-search">&#128269; Search</button>
        </form>
    </div>
</div>

<!-- ============================
     STATS STRIP
     ============================ -->
<div class="stats-strip">
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-number"><?php echo $total_packages; ?>+</div>
            <div class="stat-label">Tour Packages</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo $total_hotels; ?>+</div>
            <div class="stat-label">Partner Hotels</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo $total_bookings; ?>+</div>
            <div class="stat-label">Happy Travelers</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo $total_cities; ?>+</div>
            <div class="stat-label">Rental Cities</div>
        </div>
    </div>
</div>

<!-- ============================
     SERVICES SECTION
     ============================ -->
<div class="section-heading" style="margin-top:1rem;">
    <span class="eyebrow">What We Offer</span>
    <h2>All Your Travel Needs, One Platform</h2>
    <p>From planning your trip to renting a bike at the destination — we've got everything covered.</p>
</div>

<div class="services-grid">
    <a href="packages.html" class="service-card">
        <div class="service-icon">&#127757;</div>
        <div class="service-name">Tour Packages</div>
        <div class="service-desc">Curated holiday packages across India — hill stations, beaches, heritage, and more with itinerary included.</div>
        <div class="service-link">Browse Packages &#8594;</div>
    </a>
    <a href="hotels.html" class="service-card">
        <div class="service-icon">&#127970;</div>
        <div class="service-name">Luxury Hotels</div>
        <div class="service-desc">Book top-rated hotels at the best prices with verified ratings and instant confirmation.</div>
        <div class="service-link">View Hotels &#8594;</div>
    </a>
    <a href="buses.html" class="service-card">
        <div class="service-icon">&#128652;</div>
        <div class="service-name">Bus Transport</div>
        <div class="service-desc">Safe, affordable inter-city and local bus bookings with seat selection and live availability.</div>
        <div class="service-link">Book a Bus &#8594;</div>
    </a>
    <a href="rentals.html" class="service-card">
        <div class="service-icon">&#128663;</div>
        <div class="service-name">Local Rentals</div>
        <div class="service-desc">Rent bikes, scooters, cars or SUVs right at your destination — Goa, Manali, Kashmir &amp; beyond.</div>
        <div class="service-link">Explore Rentals &#8594;</div>
    </a>
    <a href="help.html" class="service-card">
        <div class="service-icon">&#128172;</div>
        <div class="service-name">Help &amp; Support</div>
        <div class="service-desc">Got questions? Browse FAQs on bookings, payments, and refunds or contact our 24/7 support team.</div>
        <div class="service-link">Get Help &#8594;</div>
    </a>
    <a href="ai_planner.html" class="service-card" style="border: 2px solid #2563eb; background: #f0f7ff;">
        <div class="service-icon">✨</div>
        <div class="service-name">AI Trip Planner <span class="badge" style="background:#2563eb; color:white; font-size:0.6rem; vertical-align:middle;">NEW</span></div>
        <div class="service-desc">Get a personalized, day-by-day itinerary in seconds. Our AI crafts the perfect trip based on your budget and vibe.</div>
        <div class="service-link" style="font-weight:700;">Start Planning ✨</div>
    </a>
    <a href="reviews.html" class="service-card">
        <div class="service-icon">&#11088;</div>
        <div class="service-name">Reviews</div>
        <div class="service-desc">Read authentic traveler reviews and share your own experience to help others plan better trips.</div>
        <div class="service-link">Read Reviews &#8594;</div>
    </a>
</div>

<!-- ============================
     TRENDING PACKAGES
     ============================ -->
<div class="section-heading">
    <span class="eyebrow"><?php echo isLoggedIn() ? 'Personalised For You' : 'Trending Now'; ?></span>
    <h2><?php echo isLoggedIn() ? 'Recommended Packages' : 'Top Trending Packages'; ?></h2>
    <p><?php echo isLoggedIn() ? 'Handpicked based on your travel history' : 'Most popular packages chosen by our travelers'; ?></p>
</div>

<div class="card-grid" style="margin-bottom:5rem;">
    <?php foreach($recommended as $pkg): ?>
    <div class="pkg-card">
        <div style="position:relative;">
            <img src="<?php echo htmlspecialchars($pkg['image']); ?>" alt="<?php echo htmlspecialchars($pkg['name']); ?>" class="pkg-img">
            <span class="pkg-badge"><?php echo isLoggedIn() ? '&#129504; AI Pick' : '&#128293; Trending'; ?></span>
        </div>
        <div class="pkg-body">
            <div class="pkg-dest">&#128205; <?php echo htmlspecialchars($pkg['destination']); ?> &nbsp;&#183;&nbsp; &#9201;&#65039; <?php echo $pkg['duration']; ?> Days</div>
            <div class="pkg-name"><?php echo htmlspecialchars($pkg['name']); ?></div>
            <div class="pkg-desc"><?php echo substr(htmlspecialchars($pkg['description']), 0, 100) . '...'; ?></div>
            <div class="pkg-footer">
                <div class="pkg-price">&#8377;<?php echo number_format($pkg['price'], 0); ?> <small>/ person</small></div>
                <a href="package_details.html?id=<?php echo $pkg['id']; ?>" class="btn-primary" style="font-size:0.9rem; padding:0.5rem 1.25rem;">View Details</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if(empty($recommended)): ?>
    <div style="grid-column:1/-1; text-align:center; padding:3rem; color:#94a3b8;">
        <div style="font-size:3rem; margin-bottom:1rem;">&#127757;</div>
        <p>No packages available yet. Check back soon!</p>
    </div>
    <?php endif; ?>
</div>

<!-- ============================
     RENT AT YOUR DESTINATION
     ============================ -->
<div class="section-heading">
    <span class="eyebrow">Local Rentals</span>
    <h2>&#128663; Rent at Your Destination</h2>
    <p>Already there? Pick a city and choose from bikes, scooters, cars and SUVs available locally.</p>
</div>

<div class="dest-city-grid">
    <?php foreach($rental_cities as $city):
        $img = isset($city_images[$city]) ? $city_images[$city] : $city_images['_default'];
    ?>
    <a href="rentals.html?city=<?php echo urlencode($city); ?>" class="dest-city-card">
        <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($city); ?>" loading="lazy">
        <div class="overlay"><span><?php echo htmlspecialchars($city); ?></span></div>
    </a>
    <?php endforeach; ?>
    <?php if(!empty($rental_cities)): ?>
    <a href="rentals.html" class="dest-city-card" style="background:linear-gradient(135deg,#2563eb,#7c3aed);">
        <div class="overlay" style="background:transparent; align-items:center; justify-content:center;"><span style="font-size:1rem; text-align:center;">&#128269; All Cities</span></div>
    </a>
    <?php endif; ?>
    <?php if(empty($rental_cities)): ?>
    <div style="grid-column:1/-1; text-align:center; padding:2rem; color:#94a3b8;">
        No rental cities available yet. Add vehicles from the <a href="admin/rentals.html" style="color:#2563eb;">Admin Panel</a>.
    </div>
    <?php endif; ?>
</div>

<!-- ============================
     WHY CHOOSE US
     ============================ -->
<div class="section-heading">
    <span class="eyebrow">Why Us</span>
    <h2>Why Thousands Choose Us</h2>
    <p>We combine technology, trust, and travel expertise to give you the best experience.</p>
</div>

<div class="why-grid">
    <div class="why-card">
        <div class="why-icon">&#128274;</div>
        <div class="why-title">100% Secure Booking</div>
        <div class="why-text">All transactions are encrypted and processed through secure payment gateways. Your data is always safe.</div>
    </div>
    <div class="why-card">
        <div class="why-icon">&#128176;</div>
        <div class="why-title">Best Price Guarantee</div>
        <div class="why-text">We compare prices across the market and promise you the best rates — or we'll match them.</div>
    </div>
    <div class="why-card">
        <div class="why-icon">&#127775;</div>
        <div class="why-title">Curated Experiences</div>
        <div class="why-text">Every package and vehicle is personally verified by our team to ensure quality and comfort.</div>
    </div>
    <div class="why-card">
        <div class="why-icon">&#128222;</div>
        <div class="why-title">24/7 Support</div>
        <div class="why-text">Our team is available round the clock via phone, email and live chat to resolve any issue instantly.</div>
    </div>
</div>

<!-- ============================
     TESTIMONIALS
     ============================ -->
<?php if(!empty($reviews)): ?>
<div class="section-heading">
    <span class="eyebrow">Travelers Say</span>
    <h2>&#11088; Real Reviews from Real Travelers</h2>
    <p>Don't take our word for it — hear from people who've explored India with us.</p>
</div>

<div class="testimonials-grid">
    <?php foreach($reviews as $rev): ?>
    <div class="testimonial-card">
        <div class="testimonial-stars">
            <?php for($i=1;$i<=5;$i++) echo ($i<=$rev['rating']) ? '&#9733;' : '&#9734;'; ?>
        </div>
        <div class="testimonial-text"><?php echo htmlspecialchars($rev['comment'] ?: 'Great experience! Highly recommend.'); ?></div>
        <div class="testimonial-author">— <?php echo htmlspecialchars($rev['name']); ?></div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ============================
     CTA BANNER
     ============================ -->
<div class="cta-banner">
    <h2>Ready to Start Your Next Adventure?</h2>
    <p>Thousands of destinations. One platform. Let's plan your dream trip today.</p>
    <div class="cta-btns">
        <?php if(isLoggedIn()): ?>
        <a href="packages.html" class="btn-cta-white">&#127757; Browse Packages</a>
        <a href="rentals.html" class="btn-cta-outline">&#128663; Find a Vehicle</a>
        <?php else: ?>
        <a href="register.html" class="btn-cta-white">&#9999;&#65039; Create Free Account</a>
        <a href="packages.html" class="btn-cta-outline">&#127757; Explore Without Login</a>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
