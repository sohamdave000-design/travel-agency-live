USE travel_agency;

CREATE TABLE IF NOT EXISTS rentals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('car', 'bike', 'cab') NOT NULL,
    name VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    seating_capacity INT DEFAULT 4,
    fuel_type ENUM('Petrol', 'Diesel', 'Electric', 'CNG', 'Hybrid') DEFAULT 'Petrol',
    luggage_capacity INT DEFAULT 2,
    security_deposit DECIMAL(10,2) DEFAULT 0.00,
    km_limit_per_day INT DEFAULT 100,
    price_per_day DECIMAL(10,2) NOT NULL,
    hourly_price DECIMAL(10,2),
    available BOOLEAN DEFAULT true,
    image VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Safely add 'extra_details' to bookings table if it doesnt exist by ignoring error or just adding it
-- Actually in mysql we can't do IF NOT EXISTS for adding a column easily without dynamic SQL,
-- Let's just create a quick procedure for this to avoid error if I run it again or just add it normally.
-- Since I know it doesn't exist right now, I'll just ALTER it.
ALTER TABLE bookings ADD COLUMN extra_details TEXT NULL COMMENT 'JSON string for rental add-ons, driver option, etc.' AFTER item_id;

-- Seed data for rentals
INSERT INTO rentals (type, name, city, price_per_day, hourly_price, seating_capacity, fuel_type, luggage_capacity, security_deposit, km_limit_per_day, available, image, description) VALUES
('bike', 'Royal Enfield Classic 350', 'Goa', 800.00, 100.00, 2, 'Petrol', 0, 1000.00, 100, true, 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'Classic cruiser for Goan roads'),
('car', 'Toyota Camry', 'Manali', 2500.00, 300.00, 5, 'Diesel', 3, 5000.00, 150, true, 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'Comfortable sedan for hills'),
('bike', 'Honda Activa 6G', 'Goa', 400.00, 50.00, 2, 'Petrol', 1, 500.00, 70, true, 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'Easy to navigate scooter in the city'),
('car', 'Hyundai Creta', 'Kashmir', 3000.00, 350.00, 5, 'Diesel', 4, 6000.00, 150, true, 'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 'Premium SUV for mountainous terrain');
