-- Enhance payments table
ALTER TABLE payments ADD COLUMN transaction_id VARCHAR(100) DEFAULT NULL AFTER booking_id;
ALTER TABLE payments ADD COLUMN card_last4 VARCHAR(4) DEFAULT NULL AFTER payment_method;
ALTER TABLE payments ADD COLUMN payer_email VARCHAR(100) DEFAULT NULL AFTER card_last4;

-- Ensure enum includes more statuses if needed
ALTER TABLE payments MODIFY COLUMN status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending';
