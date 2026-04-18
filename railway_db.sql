-- File: schema.sql
-- Create database
CREATE DATABASE IF NOT EXISTS travel_agency;
USE travel_agency;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin table
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
INSERT INTO admin (username, password) VALUES ('admin', '$2y$10$wOqZ.jE.mZ0eD0/w4XwSBOt7/8Z9YmZtY0.06/4.00494.X00/00W');

-- Packages table
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration INT NOT NULL COMMENT 'Duration in days',
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Custom Packages table
CREATE TABLE IF NOT EXISTS custom_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    destination VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    persons INT NOT NULL,
    hotel_type ENUM('standard', 'deluxe', 'premium') NOT NULL,
    transport_type ENUM('flight', 'train', 'bus') NOT NULL,
    estimated_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Hotels table
CREATE TABLE IF NOT EXISTS hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    location VARCHAR(100) NOT NULL,
    price_per_night DECIMAL(10,2) NOT NULL,
    rating DECIMAL(2,1) DEFAULT 0,
    image VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Buses table
CREATE TABLE IF NOT EXISTS buses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_location VARCHAR(100) NOT NULL,
    to_location VARCHAR(100) NOT NULL,
    departure_date DATE NOT NULL,
    departure_time TIME NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total_seats INT NOT NULL,
    available_seats INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rentals table
CREATE TABLE IF NOT EXISTS rentals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('car', 'bike', 'cab') NOT NULL,
    name VARCHAR(100) NOT NULL,
    price_per_day DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    booking_type ENUM('package', 'hotel', 'bus', 'rental', 'custom') NOT NULL,
    item_id INT NOT NULL, 
    booking_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    start_date DATE NOT NULL,
    end_date DATE,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Contact Messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    response TEXT DEFAULT NULL,
    responded_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    response TEXT DEFAULT NULL,
    responded_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- File: seed_indian_data.sql
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


-- File: new_packages.sql
USE travel_agency;

-- Maharashtra Packages
INSERT INTO packages (name, destination, price, duration, description, image, latitude, longitude) VALUES
('Mumbai City Explorer', 'Maharashtra', 15000.00, 4, 'Discover the vibrant city of Mumbai — the Gateway of India, Marine Drive, Juhu Beach, Bollywood tours, and the buzzing street food scene. Explore the citys rich heritage and modern skyline.', 'https://images.unsplash.com/photo-1529253355930-ddbe423a2ac7?auto=format&fit=crop&w=1200&q=80', 19.0760, 72.8777),
('Lonavala Hill Station Retreat', 'Maharashtra', 9000.00, 3, 'Escape to the lush green hills of Lonavala. Visit the famous Tiger Point, Bhushi Dam, Karla Caves, and Rajmachi Fort. Perfect for a relaxing weekend getaway with scenic views and waterfalls.', 'https://images.unsplash.com/photo-1625505826533-5c80aca7d157?auto=format&fit=crop&w=1200&q=80', 18.7546, 73.4062),
('Mahabaleshwar Paradise', 'Maharashtra', 10000.00, 3, 'Experience the strawberry capital of India! Visit Venna Lake, Mapro Garden, Pratapgad Fort, Arthur Point, and Elephant Head Point. Enjoy cool climate, panoramic views, and fresh strawberries.', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=1200&q=80', 17.9307, 73.6477),
('Mumbai Heritage & Elephanta Caves Tour', 'Maharashtra', 12000.00, 2, 'Explore the historic heart of Mumbai and the mystical Elephanta Caves. Visit the Gateway of India, Chhatrapati Shivaji Maharaj Terminus (UNESCO site), Mani Bhavan, and take a ferry to the Gharapuri island to witness the ancient rock-cut temples dedicated to Lord Shiva.', 'assets/images/mumbai_heritage.png', 18.9220, 72.8347);

-- Gujarat Packages
INSERT INTO packages (name, destination, price, duration, description, image, latitude, longitude) VALUES
('Statue of Unity & Narmada Tour', 'Gujarat', 16000.00, 4, 'Visit the worlds tallest statue — the Statue of Unity (182m). Explore the Narmada Dam, Valley of Flowers, Jungle Safari, and laser light show. A blend of engineering marvel and natural beauty.', 'assets/images/statue_of_unity.png', 21.8380, 73.7191),
('Rann of Kutch Desert Safari', 'Gujarat', 20000.00, 5, 'Experience the magical white desert of Kutch! The Rann Utsav features folk music, cultural dances, handicraft bazaars, and a stunning moonlit white salt desert stretching to the horizon.', 'https://images.unsplash.com/photo-1509316785289-025f5b846b35?auto=format&fit=crop&w=1200&q=80', 23.7337, 69.8597),
('Sacred Dwarka & Somnath Pilgrimage', 'Gujarat', 14000.00, 4, 'Visit the sacred Dwarkadhish Temple, Nageshwar Jyotirlinga, and Somnath Temple — one of the 12 Jyotirlingas. Explore Bet Dwarka island and the magnificent Rukmini Temple by the sea.', 'assets/images/dwarka.png', 22.2394, 68.9678),
('Gir National Park & Wildlife Safari', 'Gujarat', 18000.00, 3, 'Experience the thrill of the jungle! Gir National Park is the only place in the world where you can see Asiatic lions in their natural habitat. This package includes a guided jeep safari, nature trails, and a visit to the Kamleshwar Dam.', 'assets/images/gir_lion.png', 21.1243, 70.8242);

-- Himachal Pradesh Packages
INSERT INTO packages (name, destination, price, duration, description, image, latitude, longitude) VALUES
('Shimla Queen of Hills', 'Himachal Pradesh', 15000.00, 5, 'Walk down the iconic Mall Road, visit the colonial-era Christ Church, Jakhoo Temple, and take the toy train to Kalka. Enjoy scenic views of snow-capped Himalayan peaks and lush cedar forests.', 'https://commons.wikimedia.org/wiki/Special:FilePath/Shimla_Ridge.jpg?width=1200', 31.1048, 77.1734),
('Dharamshala & McLeod Ganj Trek', 'Himachal Pradesh', 14000.00, 5, 'Explore the home of the Dalai Lama! Visit Namgyal Monastery, Bhagsu Waterfall, Triund Trek, and the vibrant Tibetan culture. Stunning mountain views with a blend of spirituality and adventure.', 'https://images.unsplash.com/photo-1620766182966-c6eb5ed2b788?auto=format&fit=crop&w=1200&q=80', 32.2190, 76.3234);

-- Tamil Nadu Packages
INSERT INTO packages (name, destination, price, duration, description, image, latitude, longitude) VALUES
('Ooty & Coonoor Hill Escape', 'Tamil Nadu', 13000.00, 4, 'Discover the Nilgiri Hills! Ride the famous Ooty Toy Train, visit the Botanical Gardens, Doddabetta Peak, tea plantations, and the serene Ooty Lake. A refreshing escape to green paradise.', 'https://commons.wikimedia.org/wiki/Special:FilePath/Tea_Plantations_in_Ooty.jpg?width=1200', 11.4102, 76.6950),
('Madurai & Rameswaram Temple Tour', 'Tamil Nadu', 12000.00, 4, 'Visit the legendary Meenakshi Amman Temple with its towering gopurams, the sacred Rameswaram Temple on Pamban Island, and the stunning Pamban Bridge. Rich history and Dravidian architecture.', 'https://images.unsplash.com/photo-1582510003544-4d00b7f74220?auto=format&fit=crop&w=1200&q=80', 9.9252, 78.1198);

-- North East & Spiritual Packages
INSERT INTO packages (name, destination, price, duration, description, image, latitude, longitude) VALUES
('Meghalaya - The Cloud Sanctuary', 'Meghalaya', 18000.00, 5, 'Explore the wettest place on Earth! Visit the stunning Double Decker Living Root Bridges, the crystal-clear waters of Umngot River in Dawki, and the majestic Nohkalikai Falls. A journey into the heart of the Khasi hills.', 'assets/images/meghalaya.png', 25.4670, 91.3662),
('Varanasi - The Eternal City', 'Uttar Pradesh', 12000.00, 3, 'Experience the oldest living city in the world. Witness the magical Ganga Aarti at Dashashwamedh Ghat, explore ancient temples, and take a sunrise boat ride on the holy Ganges. A soul-stirring journey into Indian spirituality.', 'assets/images/varanasi.png', 25.3176, 82.9739);


