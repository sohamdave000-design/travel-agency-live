-- Add amenities to hotels if not exists
-- ALTER TABLE hotels ADD COLUMN amenities VARCHAR(255) DEFAULT 'Free Wi-Fi, AC, Room Service';

-- Clear existing sample data to replace with Indian context (optional, but good for clean state)
DELETE FROM packages;
DELETE FROM hotels;
DELETE FROM buses;
DELETE FROM rentals;

-- 1. Insert Indian Travel Packages
INSERT INTO packages (name, destination, price, duration, description, image, latitude, longitude) VALUES 
('Exotic Goa Getaway', 'Goa', 18000.00, 4, 'Enjoy pristine beaches, vibrant nightlife, and Portuguese heritage in Goa.', 'https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?auto=format&fit=crop&w=1200&q=80', 15.2993, 74.1240),
('Majestic Manali Trek', 'Manali', 15000.00, 5, 'Experience the snowy peaks, lush valleys, and adventurous treks in the Himalayas.', 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?auto=format&fit=crop&w=1200&q=80', 32.2432, 77.1892),
('Paradise on Earth - Kashmir', 'Kashmir', 30000.00, 6, 'Sail on Dal Lake, stroll through Mughal Gardens, and witness the stunning snow-clad mountains of Kashmir.', 'https://images.unsplash.com/photo-1595815771614-ade9d652a65d?auto=format&fit=crop&w=1200&q=80', 34.0837, 74.7973),
('Royal Rajasthan Tour', 'Rajasthan', 35000.00, 7, 'Explore majestic forts, royal palaces, and vibrant desert culture in Jaipur, Udaipur, and Jaisalmer.', 'https://images.unsplash.com/photo-1477587458883-47145ed94245?auto=format&fit=crop&w=1200&q=80', 26.9124, 75.7873),
('Kerala Backwaters Retreat', 'Kerala', 22000.00, 5, 'Relax in luxurious houseboats, enjoy Ayurvedic massages, and explore the green landscapes of God''s Own Country.', 'https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?auto=format&fit=crop&w=1200&q=80', 9.4981, 76.3329),
('Leh-Ladakh Adventure', 'Ladakh', 40000.00, 8, 'A thrilling road trip across high altitude passes, crystal clear lakes, and ancient monasteries.', 'https://images.unsplash.com/photo-1581793746485-04698e79a4e8?auto=format&fit=crop&w=1200&q=80', 34.1526, 77.5771),
('Andaman Island Escapade', 'Andaman', 45000.00, 6, 'Scuba diving, pristine white sand beaches, and crystal clear waters of Havelock and Neil islands.', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 11.6231, 92.7412);

-- 2. Insert Indian Hotels
INSERT INTO hotels (name, location, price_per_night, rating, description, image, amenities) VALUES 
('Taj Mahal Palace - 5-Star', 'Mumbai', 20000.00, 5.0, 'Iconic luxury sea-facing hotel with world-class hospitality.', 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/Taj_Mahal_Palace_Hotel_photo.jpg/1280px-Taj_Mahal_Palace_Hotel_photo.jpg', 'Free Wi-Fi, Pool, Spa, Sea View, Gym, Fine Dining'),
('The Leela Palace - 5-Star', 'New Delhi', 18000.00, 4.9, 'Experience premium luxury and royal architecture right in the capital.', 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/The_Leela_Palace_Chennai.jpg/1280px-The_Leela_Palace_Chennai.jpg', 'Free Wi-Fi, Pool, AC, Mini Bar, Gym'),
('Umaid Bhawan - 5-Star', 'Jodhpur', 65000.00, 5.0, 'Stay in a glorious heritage palace offering massive regal suites.', 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/1996_-218-20A_Jodhpur_Hotel_Umaid_Bhawan_Palace_%282233393509%29.jpg/1280px-1996_-218-20A_Jodhpur_Hotel_Umaid_Bhawan_Palace_%282233393509%29.jpg', 'Free Wi-Fi, Royal Spa, Indoor Pool, Heritage Walk'),
('Lemon Tree Premier - 4-Star', 'Jaipur', 6000.00, 4.2, 'Modern comforts mixed with refreshing service for business and leisure.', 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'Free Wi-Fi, AC, Breakfast Included, Gym'),
('Radisson Blu - 4-Star', 'Goa', 12000.00, 4.5, 'Resort located near the beach with immense pool area and shacks.', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'Free Wi-Fi, Pool, Beach Access, Bar'),
('Ginger Hotel - 3-Star', 'Bangalore', 3500.00, 3.8, 'Smart, clean, and affordable stays perfect for quick business trips.', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'Free Wi-Fi, AC, Room Service, TV'),
('Treebo Trend - 3-Star', 'Manali', 2500.00, 3.9, 'Cozy rooms with stunning snowy mountain views at budget prices.', 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7a/Cottages_Simsa_Manali_Himachal_May24_A7CR_00116.jpg/1280px-Cottages_Simsa_Manali_Himachal_May24_A7CR_00116.jpg', 'Free Wi-Fi, Heater, Breakfast, Great View');

-- 3. Insert Indian Bus Routes
INSERT INTO buses (from_location, to_location, departure_date, departure_time, price, total_seats, available_seats) VALUES 
('Delhi', 'Manali', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '18:30:00', 1250.00, 40, 40),
('Delhi', 'Jaipur', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '07:00:00', 750.00, 45, 45),
('Mumbai', 'Goa', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '20:00:00', 1600.00, 35, 35),
('Bangalore', 'Hyderabad', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '21:30:00', 1400.00, 40, 40),
('Chennai', 'Coimbatore', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '22:00:00', 950.00, 45, 45),
('Pune', 'Mumbai', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '06:00:00', 450.00, 30, 30);

-- 4. Insert Rentals (2-wheelers and 4-wheelers)
INSERT INTO rentals (type, name, city, seating_capacity, fuel_type, security_deposit, price_per_day, image, description, driver_option, driver_price) VALUES 
('bike', 'Honda Activa (Scooter)', 'Multiple', 2, 'Petrol', 2000.00, 500.00, 'https://images.unsplash.com/photo-1591637333184-19aa84b3e01f?auto=format&fit=crop&w=1200&q=80', 'Perfect scooter for navigating narrow city streets and local markets.', 0, 0.00),
('bike', 'Royal Enfield Classic 350', 'Multiple', 2, 'Petrol', 5000.00, 1200.00, 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=1200&q=80', 'Experience the thump and power, ideal for long mountain runs in Ladakh or hill stations.', 0, 0.00),
('bike', 'Bajaj Pulsar 150', 'Multiple', 2, 'Petrol', 3000.00, 800.00, 'https://images.unsplash.com/photo-1558981420-c532902e58b4?auto=format&fit=crop&w=1200&q=80', 'Sporty and reliable bike for daily city tours and short trips.', 0, 0.00),
('car', 'Toyota Innova Hycross', 'Bangalore', 7, 'Hybrid', 10000.00, 4500.00, 'assets/img/rentals/innova_hycross.png', 'Premium hybrid MPV with exceptional comfort and futuristic features.', 1, 1000.00),
('car', 'Mahindra Scorpio-N', 'Delhi', 7, 'Diesel', 8000.00, 4000.00, 'assets/img/rentals/mahindra_scorpio_n.png', 'The Big Daddy of SUVs. Rugged, powerful, and ready for any terrain.', 1, 1000.00),
('car', 'Kia Carens', 'Mumbai', 7, 'Petrol', 7000.00, 3200.00, 'assets/img/rentals/kia_carens.png', 'Sophisticated family mover with advanced safety and modern tech.', 1, 800.00),
('car', 'Hyundai Grand i10 Nios', 'Pune', 5, 'Petrol', 3000.00, 1500.00, 'assets/img/rentals/hyundai_i10.png', 'Stylish and compact hatch, perfect for zipping through urban traffic.', 0, 0.00),
('car', 'Maruti Suzuki Swift', 'Chandigarh', 5, 'Petrol', 3000.00, 1400.00, 'assets/img/rentals/maruti_swift.png', 'The iconic sporty hatchback, known for its fun drive and reliability.', 0, 0.00),
('car', 'Honda City', 'Hyderabad', 5, 'Petrol', 6000.00, 3000.00, 'assets/img/rentals/honda_city.png', 'The benchmark executive sedan. Elegant, spacious, and extremely smooth.', 1, 800.00),
('car', 'Mahindra Thar (SUV)', 'Goa', 4, 'Diesel', 10000.00, 3500.00, 'assets/img/rentals/mahindra_thar.png', '4x4 beast perfect for off-roading and adventurous rugged trips.', 0, 0.00),
('car', 'Toyota Innova Crysta', 'Multiple', 7, 'Diesel', 8000.00, 3800.00, 'assets/img/rentals/innova_crysta.png', 'The legendary workhorse for Indian roads. Unmatched comfort and reliability.', 1, 1000.00),
('cab', 'Luxury Sedan with Chauffeur', 'Multiple', 4, 'Petrol', 0.00, 2500.00, 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1200&q=80', 'Relax in the back seat with a professional local driver. Includes 80km limit.', 1, 0.00);
