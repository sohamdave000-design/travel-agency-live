-- Migration for Admin Responses
ALTER TABLE contact_messages ADD COLUMN response TEXT DEFAULT NULL;
ALTER TABLE contact_messages ADD COLUMN responded_at DATETIME DEFAULT NULL;

ALTER TABLE reviews ADD COLUMN response TEXT DEFAULT NULL;
ALTER TABLE reviews ADD COLUMN responded_at DATETIME DEFAULT NULL;
