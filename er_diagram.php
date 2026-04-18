<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; }
  body { background: #fff; }
  svg text { font-family: Arial, sans-serif; }
</style>
</head>
<body>
<svg id="ersvg" width="1200" height="1150" xmlns="http://www.w3.org/2000/svg">

<!-- ===== LINES (drawn first, behind everything) ===== -->

<!-- User to places diamond (vertical) -->
<line x1="575" y1="200" x2="575" y2="305" stroke="black" stroke-width="1.5"/>
<!-- places diamond to Bookings (vertical) -->
<line x1="575" y1="355" x2="575" y2="445" stroke="black" stroke-width="1.5"/>
<!-- Cardinality -->
<text x="583" y="285" font-size="14" font-family="Arial">1</text>
<text x="583" y="435" font-size="14" font-family="Arial">N</text>

<!-- Bookings to has diamond (vertical) -->
<line x1="575" y1="495" x2="575" y2="590" stroke="black" stroke-width="1.5"/>
<!-- has diamond to Payments (vertical) -->
<line x1="575" y1="640" x2="575" y2="730" stroke="black" stroke-width="1.5"/>
<text x="583" y="575" font-size="14" font-family="Arial">1</text>
<text x="583" y="720" font-size="14" font-family="Arial">1</text>

<!-- Bookings to books-Rentals diamond (horizontal) -->
<line x1="510" y1="470" x2="380" y2="470" stroke="black" stroke-width="1.5"/>
<!-- books-Rentals diamond to Rentals entity (horizontal) -->
<line x1="300" y1="470" x2="240" y2="470" stroke="black" stroke-width="1.5"/>
<text x="470" y="460" font-size="14" font-family="Arial">N</text>
<text x="255" y="460" font-size="14" font-family="Arial">1</text>

<!-- Bookings to books-Packages diamond (horizontal) -->
<line x1="640" y1="470" x2="770" y2="470" stroke="black" stroke-width="1.5"/>
<!-- books-Packages diamond to Packages entity (horizontal) -->
<line x1="850" y1="470" x2="920" y2="470" stroke="black" stroke-width="1.5"/>
<text x="665" y="460" font-size="14" font-family="Arial">N</text>
<text x="875" y="460" font-size="14" font-family="Arial">1</text>

<!-- Bookings to books-Buses diamond (diagonal lower-left) -->
<line x1="545" y1="495" x2="400" y2="600" stroke="black" stroke-width="1.5"/>
<!-- books-Buses diamond to Buses entity (diagonal lower-left) -->
<line x1="345" y1="640" x2="240" y2="730" stroke="black" stroke-width="1.5"/>
<text x="500" y="530" font-size="14" font-family="Arial">N</text>
<text x="262" y="710" font-size="14" font-family="Arial">1</text>

<!-- Bookings to books-Hotels diamond (diagonal lower-right) -->
<line x1="605" y1="495" x2="750" y2="600" stroke="black" stroke-width="1.5"/>
<!-- books-Hotels diamond to Hotels entity (diagonal lower-right) -->
<line x1="805" y1="640" x2="920" y2="730" stroke="black" stroke-width="1.5"/>
<text x="620" y="530" font-size="14" font-family="Arial">N</text>
<text x="875" y="710" font-size="14" font-family="Arial">1</text>

<!-- User to maintains diamond (left horizontal) -->
<line x1="510" y1="175" x2="380" y2="175" stroke="black" stroke-width="1.5"/>
<!-- maintains diamond to Wishlist entity (left horizontal) -->
<line x1="300" y1="175" x2="220" y2="175" stroke="black" stroke-width="1.5"/>
<text x="465" y="165" font-size="14" font-family="Arial">1</text>
<text x="235" y="165" font-size="14" font-family="Arial">N</text>

<!-- User to writes diamond (right horizontal) -->
<line x1="640" y1="175" x2="770" y2="175" stroke="black" stroke-width="1.5"/>
<!-- writes diamond to Reviews entity (right horizontal) -->
<line x1="850" y1="175" x2="920" y2="175" stroke="black" stroke-width="1.5"/>
<text x="655" y="165" font-size="14" font-family="Arial">1</text>
<text x="875" y="165" font-size="14" font-family="Arial">N</text>

<!-- Packages to listed-in diamond (vertical up) -->
<line x1="970" y1="445" x2="970" y2="355" stroke="black" stroke-width="1.5"/>
<!-- listed-in diamond to User (diagonal up-left to Packages top area -> User) -->
<!-- Actually listed-in is between Packages and Wishlist -->
<!-- Wishlist is at (170, 175) center. Packages at (970, 470).
     Diamond at midpoint: (570,310) but that overlaps places diamond. 
     Let's put listed-in near Packages going up, and connect to Wishlist via a vertical+horizontal path -->
<!-- Better: listed-in diamond at (970, 310), then line down to Packages and line up+left to Wishlist via two segments -->
<line x1="920" y1="310" x2="220" y2="175" stroke="black" stroke-width="1.5"/>
<text x="930" y="430" font-size="14" font-family="Arial">1</text>
<text x="235" y="197" font-size="14" font-family="Arial">N</text>

<!-- User to creates diamond → AI Plans (diagonal lower-right) -->
<line x1="620" y1="200" x2="860" y2="830" stroke="black" stroke-width="1.5"/>
<line x1="575" y1="200" x2="970" y2="830" stroke="black" stroke-width="1.5"/>
<!-- Actually let me use a cleaner path:
     User bottom (575, 200) → creates diamond at (820, 580) → AI Plans top (970, 730) -->
<line x1="610" y1="198" x2="800" y2="565" stroke="black" stroke-width="1.5"/>
<line x1="840" y1="600" x2="960" y2="730" stroke="black" stroke-width="1.5"/>
<text x="680" y="350" font-size="14" font-family="Arial">1</text>
<text x="935" y="715" font-size="14" font-family="Arial">N</text>

<!-- ===== ENTITIES (rectangles) ===== -->

<!-- USER (center top) -->
<rect x="510" y="150" width="130" height="50" fill="white" stroke="black" stroke-width="2"/>
<text x="575" y="180" font-size="15" font-weight="bold" font-family="Arial" text-anchor="middle">User</text>

<!-- BOOKINGS (center) -->
<rect x="510" y="445" width="130" height="50" fill="white" stroke="black" stroke-width="2"/>
<text x="575" y="475" font-size="15" font-weight="bold" font-family="Arial" text-anchor="middle">Bookings</text>

<!-- PAYMENTS (center bottom) -->
<rect x="510" y="730" width="130" height="50" fill="white" stroke="black" stroke-width="2"/>
<text x="575" y="760" font-size="15" font-weight="bold" font-family="Arial" text-anchor="middle">Payments</text>

<!-- RENTALS (left center) -->
<rect x="110" y="445" width="130" height="50" fill="white" stroke="black" stroke-width="2"/>
<text x="175" y="475" font-size="15" font-weight="bold" font-family="Arial" text-anchor="middle">Rentals</text>

<!-- PACKAGES (right center) -->
<rect x="920" y="445" width="130" height="50" fill="white" stroke="black" stroke-width="2"/>
<text x="985" y="475" font-size="15" font-weight="bold" font-family="Arial" text-anchor="middle">Packages</text>

<!-- BUSES (lower left) -->
<rect x="110" y="730" width="130" height="50" fill="white" stroke="black" stroke-width="2"/>
<text x="175" y="760" font-size="15" font-weight="bold" font-family="Arial" text-anchor="middle">Buses</text>

<!-- HOTELS (lower right) -->
<rect x="920" y="730" width="130" height="50" fill="white" stroke="black" stroke-width="2"/>
<text x="985" y="760" font-size="15" font-weight="bold" font-family="Arial" text-anchor="middle">Hotels</text>

<!-- WISHLIST (far left) -->
<rect x="90" y="150" width="130" height="50" fill="white" stroke="black" stroke-width="2"/>
<text x="155" y="180" font-size="15" font-weight="bold" font-family="Arial" text-anchor="middle">Wishlist</text>

<!-- REVIEWS (far right) -->
<rect x="920" y="150" width="130" height="50" fill="white" stroke="black" stroke-width="2"/>
<text x="985" y="180" font-size="15" font-weight="bold" font-family="Arial" text-anchor="middle">Reviews</text>

<!-- AI PLANS (far lower right) -->
<rect x="920" y="950" width="130" height="50" fill="white" stroke="black" stroke-width="2"/>
<text x="985" y="980" font-size="15" font-weight="bold" font-family="Arial" text-anchor="middle">AI Plans</text>

<!-- ===== RELATIONSHIP DIAMONDS ===== -->

<!-- places (User - Bookings) -->
<polygon points="575,305 615,330 575,355 535,330" fill="white" stroke="black" stroke-width="1.5"/>
<text x="575" y="334" font-size="12" font-family="Arial" text-anchor="middle">places</text>

<!-- has (Bookings - Payments) -->
<polygon points="575,590 615,615 575,640 535,615" fill="white" stroke="black" stroke-width="1.5"/>
<text x="575" y="619" font-size="12" font-family="Arial" text-anchor="middle">has</text>

<!-- books (Bookings - Rentals) (left) -->
<polygon points="340,470 380,445 380,495 340,470" fill="white" stroke="black" stroke-width="1.5"/>
<polygon points="300,470 340,445 380,470 340,495" fill="white" stroke="black" stroke-width="1.5"/>
<text x="340" y="474" font-size="12" font-family="Arial" text-anchor="middle">books</text>

<!-- books (Bookings - Packages) (right) -->
<polygon points="810,470 850,445 850,495 810,470" fill="white" stroke="black" stroke-width="1.5"/>
<polygon points="810,470 850,445 890,470 850,495" fill="white" stroke="black" stroke-width="1.5"/>
<text x="850" y="474" font-size="12" font-family="Arial" text-anchor="middle">books</text>

<!-- books (Bookings - Buses) (lower left diagonal) -->
<polygon points="370,600 410,575 410,625 370,600" fill="white" stroke="black" stroke-width="1.5"/>
<polygon points="345,620 385,595 385,645 345,620" fill="white" stroke="black" stroke-width="1.5"/>
<polygon points="345,620 385,595 425,620 385,645" fill="white" stroke="black" stroke-width="1.5"/>
<text x="375" y="624" font-size="12" font-family="Arial" text-anchor="middle">books</text>

<!-- books (Bookings - Hotels) (lower right diagonal) -->
<polygon points="775,620 815,595 815,645 775,620" fill="white" stroke="black" stroke-width="1.5"/>
<polygon points="775,620 815,595 855,620 815,645" fill="white" stroke="black" stroke-width="1.5"/>
<text x="815" y="624" font-size="12" font-family="Arial" text-anchor="middle">books</text>

<!-- maintains (User - Wishlist) -->
<polygon points="300,175 340,150 380,175 340,200" fill="white" stroke="black" stroke-width="1.5"/>
<text x="340" y="179" font-size="12" font-family="Arial" text-anchor="middle">maintains</text>

<!-- writes (User - Reviews) -->
<polygon points="770,175 810,150 850,175 810,200" fill="white" stroke="black" stroke-width="1.5"/>
<text x="810" y="179" font-size="12" font-family="Arial" text-anchor="middle">writes</text>

<!-- listed in (Packages - Wishlist) -->
<polygon points="920,310 960,285 1000,310 960,335" fill="white" stroke="black" stroke-width="1.5"/>
<text x="960" y="314" font-size="12" font-family="Arial" text-anchor="middle">listed in</text>

<!-- creates (User - AI Plans) -->
<polygon points="800,565 840,540 880,565 840,590" fill="white" stroke="black" stroke-width="1.5"/>
<text x="840" y="569" font-size="12" font-family="Arial" text-anchor="middle">creates</text>

<!-- Line: listed in diamond to Wishlist entity top -->
<line x1="920" y1="310" x2="220" y2="175" stroke="black" stroke-width="1.5"/>
<text x="300" y="215" font-size="14" font-family="Arial">N</text>

<!-- AI Plans: line from creates diamond to AI Plans entity -->
<line x1="840" y1="590" x2="985" y2="950" stroke="black" stroke-width="1.5"/>
<text x="930" y="760" font-size="14" font-family="Arial">N</text>

<!-- ===== USER ATTRIBUTES ===== -->
<line x1="575" y1="150" x2="500" y2="90" stroke="black" stroke-width="1.2"/>
<ellipse cx="470" cy="75" rx="48" ry="18" fill="white" stroke="black" stroke-width="1.2"/>
<text x="470" y="79" font-size="11" font-family="Arial" text-anchor="middle" text-decoration="underline">user_id</text>

<line x1="575" y1="150" x2="575" y2="90" stroke="black" stroke-width="1.2"/>
<ellipse cx="575" cy="72" rx="52" ry="18" fill="white" stroke="black" stroke-width="1.2"/>
<text x="575" y="76" font-size="11" font-family="Arial" text-anchor="middle">user_name</text>

<line x1="575" y1="150" x2="650" y2="90" stroke="black" stroke-width="1.2"/>
<ellipse cx="680" cy="75" rx="36" ry="18" fill="white" stroke="black" stroke-width="1.2"/>
<text x="680" y="79" font-size="11" font-family="Arial" text-anchor="middle">email</text>

<line x1="575" y1="150" x2="720" y2="100" stroke="black" stroke-width="1.2"/>
<ellipse cx="750" cy="87" rx="35" ry="18" fill="white" stroke="black" stroke-width="1.2"/>
<text x="750" y="91" font-size="11" font-family="Arial" text-anchor="middle">phone</text>

<!-- ===== BOOKINGS ATTRIBUTES ===== -->
<line x1="520" y1="445" x2="430" y2="400" stroke="black" stroke-width="1.2"/>
<ellipse cx="390" cy="390" rx="57" ry="18" fill="white" stroke="black" stroke-width="1.2"/>
<text x="390" y="394" font-size="11" font-family="Arial" text-anchor="middle">booking_id</text>

<line x1="560" y1="445" x2="530" y2="400" stroke="black" stroke-width="1.2"/>
<ellipse cx="515" cy="390" rx="62" ry="18" fill="white" stroke="black" stroke-width="1.2"/>
<text x="515" y="394" font-size="11" font-family="Arial" text-anchor="middle">booking_date</text>

<line x1="635" y1="445" x2="665" y2="405" stroke="black" stroke-width="1.2"/>
<ellipse cx="680" cy="395" rx="36" ry="18" fill="white" stroke="black" stroke-width="1.2"/>
<text x="680" y="399" font-size="11" font-family="Arial" text-anchor="middle">status</text>

<line x1="640" y1="455" x2="720" y2="430" stroke="black" stroke-width="1.2"/>
<ellipse cx="770" cy="424" rx="52" ry="18" fill="white" stroke="black" stroke-width="1.2"/>
<text x="770" y="428" font-size="11" font-family="Arial" text-anchor="middle">total_price</text>

<!-- ===== PAYMENTS ATTRIBUTES ===== -->
<line x1="540" y1="780" x2="480" y2="840" stroke="black" stroke-width="1.2"/>
<ellipse cx="455" cy="855" rx="55" ry="18" fill="white" stroke="black" stroke-width="1.2"/>
<text x="455" y="859" font-size="11" font-family="Arial" text-anchor="middle">payment_id</text>

<line x1="575" y1="780" x2="575" y2="840" stroke="black" stroke-width="1.2"/>
<ellipse cx="575" cy="858" rx="36" ry="18" fill="white" stroke="black" stroke-width="1.2"/>
<text x="575" y="862" font-size="11" font-family="Arial" text-anchor="middle">amount</text>

<line x1="610" y1="780" x2="650" y2="840" stroke="black" stroke-width="1.2"/>
<ellipse cx="680" cy="855" rx="36" ry="18" fill="white" stroke="black" stroke-width="1.2"/>
<text x="680" y="859" font-size="11" font-family="Arial" text-anchor="middle">status</text>

<line x1="540" y1="780" x2="430" y2="870" stroke="black" stroke-width="1.2"/>
<ellipse cx="388" cy="885" rx="62" ry="18" fill="white" stroke="black" stroke-width="1.2"/>
<text x="388" y="889" font-size="11" font-family="Arial" text-anchor="middle">transaction_id</text>

<line x1="610" y1="780" x2="710" y2="875" stroke="black" stroke-width="1.2"/>
<ellipse cx="738" cy="890" rx="70" ry="18" fill="white" stroke="black" stroke-width="1.2"/>
<text x="738" y="894" font-size="11" font-family="Arial" text-anchor="middle">payment_method</text>

<!-- ===== RENTALS ATTRIBUTES ===== -->
<line x1="175" y1="445" x2="100" y2="390" stroke="black" stroke-width="1.2"/>
<ellipse cx="68" cy="378" rx="46" ry="18" fill="white" stroke="black" stroke-width="1.2"/>
<text x="68" y="382" font-size="11" font-family="Arial" text-anchor="middle">rental_id</text>

<line x1="140" y1="470" x2="55" y2="465" stroke="black" stroke-width="1.2"/>
<ellipse cx="28" cy="463" rx="30" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="28" y="467" font-size="11" font-family="Arial" text-anchor="middle">name</text>

<line x1="140" y1="490" x2="60" y2="520" stroke="black" stroke-width="1.2"/>
<ellipse cx="36" cy="530" rx="27" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="36" y="534" font-size="11" font-family="Arial" text-anchor="middle">type</text>

<line x1="175" y1="495" x2="130" y2="550" stroke="black" stroke-width="1.2"/>
<ellipse cx="110" cy="562" rx="26" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="110" y="566" font-size="11" font-family="Arial" text-anchor="middle">city</text>

<line x1="210" y1="490" x2="240" y2="530" stroke="black" stroke-width="1.2"/>
<ellipse cx="260" cy="543" rx="58" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="260" y="547" font-size="11" font-family="Arial" text-anchor="middle">price_per_day</text>

<!-- ===== PACKAGES ATTRIBUTES ===== -->
<line x1="985" y1="445" x2="1050" y2="390" stroke="black" stroke-width="1.2"/>
<ellipse cx="1080" cy="378" rx="52" ry="18" fill="white" stroke="black" stroke-width="1.2"/>
<text x="1080" y="382" font-size="11" font-family="Arial" text-anchor="middle">package_id</text>

<line x1="985" y1="445" x2="1140" y2="460" stroke="black" stroke-width="1.2"/>
<ellipse cx="1165" cy="460" rx="28" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="1165" y="464" font-size="11" font-family="Arial" text-anchor="middle">name</text>

<line x1="1000" y1="495" x2="1090" y2="540" stroke="black" stroke-width="1.2"/>
<ellipse cx="1138" cy="548" rx="53" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="1138" y="552" font-size="11" font-family="Arial" text-anchor="middle">destination</text>

<line x1="985" y1="495" x2="985" y2="548" stroke="black" stroke-width="1.2"/>
<ellipse cx="985" cy="562" rx="30" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="985" y="566" font-size="11" font-family="Arial" text-anchor="middle">price</text>

<line x1="955" y1="495" x2="900" y2="545" stroke="black" stroke-width="1.2"/>
<ellipse cx="875" cy="558" rx="38" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="875" y="562" font-size="11" font-family="Arial" text-anchor="middle">duration</text>

<!-- ===== BUSES ATTRIBUTES ===== -->
<line x1="150" y1="730" x2="70" y2="700" stroke="black" stroke-width="1.2"/>
<ellipse cx="40" cy="693" rx="36" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="40" y="697" font-size="11" font-family="Arial" text-anchor="middle">bus_id</text>

<line x1="130" y1="755" x2="30" y2="755" stroke="black" stroke-width="1.2"/>
<ellipse cx="28" cy="755" rx="52" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="28" y="759" on font-size="11" font-family="Arial" text-anchor="middle">from_loc</text>

<line x1="130" y1="770" x2="30" y2="800" stroke="black" stroke-width="1.2"/>
<ellipse cx="28" cy="810" rx="48" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="28" y="814" font-size="11" font-family="Arial" text-anchor="middle">to_location</text>

<line x1="175" y1="780" x2="175" y2="840" stroke="black" stroke-width="1.2"/>
<ellipse cx="175" cy="855" rx="30" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="175" y="859" font-size="11" font-family="Arial" text-anchor="middle">price</text>

<!-- ===== HOTELS ATTRIBUTES ===== -->
<line x1="1030" y1="730" x2="1105" y2="693" stroke="black" stroke-width="1.2"/>
<ellipse cx="1135" cy="686" rx="40" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="1135" y="690" font-size="11" font-family="Arial" text-anchor="middle">hotel_id</text>

<line x1="1050" y1="755" x2="1155" y2="755" stroke="black" stroke-width="1.2"/>
<ellipse cx="1178" cy="755" rx="28" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="1178" y="759" font-size="11" font-family="Arial" text-anchor="middle">name</text>

<line x1="1050" y1="770" x2="1155" y2="800" stroke="black" stroke-width="1.2"/>
<ellipse cx="1180" cy="808" rx="38" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="1180" y="812" font-size="11" font-family="Arial" text-anchor="middle">location</text>

<line x1="985" y1="780" x2="985" y2="840" stroke="black" stroke-width="1.2"/>
<ellipse cx="985" cy="857" rx="58" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="985" y="861" font-size="11" font-family="Arial" text-anchor="middle">price_per_night</text>

<line x1="940" y1="770" x2="870" y2="810" stroke="black" stroke-width="1.2"/>
<ellipse cx="843" cy="820" rx="32" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="843" y="824" font-size="11" font-family="Arial" text-anchor="middle">rating</text>

<!-- ===== REVIEWS ATTRIBUTES ===== -->
<line x1="985" y1="150" x2="985" y2="95" stroke="black" stroke-width="1.2"/>
<ellipse cx="985" cy="78" rx="50" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="985" y="82" font-size="11" font-family="Arial" text-anchor="middle">review_id</text>

<line x1="1020" y1="155" x2="1090" y2="110" stroke="black" stroke-width="1.2"/>
<ellipse cx="1115" cy="98" rx="32" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="1115" y="102" font-size="11" font-family="Arial" text-anchor="middle">rating</text>

<line x1="1050" y1="175" x2="1130" y2="175" stroke="black" stroke-width="1.2"/>
<ellipse cx="1164" cy="175" rx="38" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="1164" y="179" font-size="11" font-family="Arial" text-anchor="middle">comment</text>

<!-- ===== WISHLIST ATTRIBUTES ===== -->
<line x1="155" y1="150" x2="90" y2="100" stroke="black" stroke-width="1.2"/>
<ellipse cx="60" cy="88" rx="52" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="60" y="92" font-size="11" font-family="Arial" text-anchor="middle">wishlist_id</text>

<line x1="155" y1="150" x2="55" y2="130" stroke="black" stroke-width="1.2"/>
<ellipse cx="30" cy="127" rx="48" ry="16" fill="white" stroke="black" stroke-width="1.2"/>
<text x="30" y="131" font-size="11" font-family="Arial" text-anchor="middle">created_at</text>

<!-- ===== AI PLANS ATTRIBUTES ===== -->
<line x1="985" y1="950" x2="985" y2="890" stroke="black" stroke-width="1.2"/>
<ellipse cx="985" cy="875" rx="40" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="985" y="879" font-size="11" font-family="Arial" text-anchor="middle">plan_id</text>

<line x1="1020" y1="960" x2="1100" y2="940" stroke="black" stroke-width="1.2"/>
<ellipse cx="1140" cy="935" rx="53" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="1140" y="939" font-size="11" font-family="Arial" text-anchor="middle">destination</text>

<line x1="1050" y1="975" x2="1120" y2="990" stroke="black" stroke-width="1.2"/>
<ellipse cx="1155" cy="993" rx="36" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="1155" y="997" font-size="11" font-family="Arial" text-anchor="middle">budget</text>

<line x1="985" y1="1000" x2="985" y2="1050" stroke="black" stroke-width="1.2"/>
<ellipse cx="985" cy="1065" rx="52" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="985" y="1069" font-size="11" font-family="Arial" text-anchor="middle">travel_style</text>

<line x1="950" y1="990" x2="840" y2="1040" stroke="black" stroke-width="1.2"/>
<ellipse cx="808" cy="1050" rx="38" ry="17" fill="white" stroke="black" stroke-width="1.2"/>
<text x="808" y="1054" font-size="11" font-family="Arial" text-anchor="middle">duration</text>

</svg>
<p style="text-align:center; font-family:Arial; font-size:15px; margin:10px 0">Fig — E-R Diagram of Travel Agency Website</p>
</body>
</html>
