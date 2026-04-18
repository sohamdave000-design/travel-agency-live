USE travel_agency;

-- Wishlist table
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    package_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wish (user_id, package_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE
);

-- Add room_types to hotels (JSON-like text)
ALTER TABLE hotels ADD COLUMN IF NOT EXISTS room_types TEXT DEFAULT '{"standard":1,"deluxe":1.5,"suite":2.5}';
ALTER TABLE hotels ADD COLUMN IF NOT EXISTS latitude DECIMAL(10,7) DEFAULT NULL;
ALTER TABLE hotels ADD COLUMN IF NOT EXISTS longitude DECIMAL(10,7) DEFAULT NULL;

-- Add lat/lng to packages
ALTER TABLE packages ADD COLUMN IF NOT EXISTS latitude DECIMAL(10,7) DEFAULT NULL;
ALTER TABLE packages ADD COLUMN IF NOT EXISTS longitude DECIMAL(10,7) DEFAULT NULL;

-- Add driver option to rentals
ALTER TABLE rentals ADD COLUMN IF NOT EXISTS driver_option TINYINT(1) DEFAULT 0;
ALTER TABLE rentals ADD COLUMN IF NOT EXISTS driver_price DECIMAL(10,2) DEFAULT 0.00;

-- Add bus name
ALTER TABLE buses ADD COLUMN IF NOT EXISTS bus_name VARCHAR(100) DEFAULT 'Express';

-- Update existing data with coordinates and new fields
UPDATE packages SET latitude=15.2993, longitude=74.1240 WHERE destination='Goa';
UPDATE packages SET latitude=32.2396, longitude=77.1887 WHERE destination='Manali';
UPDATE packages SET latitude=34.0837, longitude=74.7973 WHERE destination='Kashmir';
UPDATE packages SET latitude=26.9124, longitude=75.7873 WHERE destination='Rajasthan';
UPDATE packages SET latitude=10.8505, longitude=76.2711 WHERE destination='Kerala';
UPDATE packages SET latitude=34.1526, longitude=77.5771 WHERE destination='Ladakh';
UPDATE packages SET latitude=11.7401, longitude=92.6586 WHERE destination='Andaman';

UPDATE hotels SET latitude=18.9220, longitude=72.8347 WHERE location='Mumbai';
UPDATE hotels SET latitude=28.6139, longitude=77.2090 WHERE location='New Delhi';
UPDATE hotels SET latitude=26.2389, longitude=73.0243 WHERE location='Jodhpur';
UPDATE hotels SET latitude=26.9124, longitude=75.7873 WHERE location='Jaipur';
UPDATE hotels SET latitude=15.2993, longitude=74.1240 WHERE location='Goa';
UPDATE hotels SET latitude=12.9716, longitude=77.5946 WHERE location='Bangalore';
UPDATE hotels SET latitude=32.2396, longitude=77.1887 WHERE location='Manali';

UPDATE rentals SET driver_option=1, driver_price=15.00 WHERE type='car';
UPDATE rentals SET driver_option=0, driver_price=0.00 WHERE type='bike';
UPDATE rentals SET driver_option=1, driver_price=0.00 WHERE type='cab';

UPDATE buses SET bus_name='Volvo AC Sleeper' WHERE from_location='Delhi' AND to_location='Manali';
UPDATE buses SET bus_name='Rajasthan Royals' WHERE from_location='Delhi' AND to_location='Jaipur';
UPDATE buses SET bus_name='Konkan Express' WHERE from_location='Mumbai' AND to_location='Goa';
UPDATE buses SET bus_name='South Star' WHERE from_location='Bangalore' AND to_location='Hyderabad';
UPDATE buses SET bus_name='Tamil Nadu Express' WHERE from_location='Chennai' AND to_location='Coimbatore';
UPDATE buses SET bus_name='Shivneri Deluxe' WHERE from_location='Pune' AND to_location='Mumbai';
