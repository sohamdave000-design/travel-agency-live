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
