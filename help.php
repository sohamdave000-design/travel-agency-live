<?php
require_once 'config/database.php';
include 'includes/header.php';
?>

<style>
    /* Help & Support Custom Styles */
    .help-hero {
        background: linear-gradient(rgba(37, 99, 235, 0.8), rgba(15, 23, 42, 0.8)), url('https://images.unsplash.com/photo-1557426272-fc759fdf7a8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover;
        height: 50vh;
        min-height: 350px;
        margin-top: -2rem;
        margin-left: -2rem;
        margin-right: -2rem;
    }

    .help-hero h1 {
        font-size: 3rem;
    }

    .help-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        max-width: 1200px;
        margin: 4rem auto;
        padding: 0 2rem;
    }

    .faq-section h2, .contact-section h2 {
        color: var(--primary-dark);
        margin-bottom: 2rem;
        font-size: 2rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .faq-item {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 2px 4px var(--shadow-color);
    }

    .faq-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 8px var(--shadow-hover);
        border-color: var(--primary-color);
    }

    .faq-item h4 {
        color: var(--text-color);
        font-size: 1.2rem;
        margin-bottom: 0.75rem;
    }

    .faq-item p {
        color: var(--text-muted);
        font-size: 0.95rem;
    }

    .contact-details {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .contact-card {
        background: var(--card-bg);
        padding: 1.5rem;
        border-radius: 8px;
        border-left: 4px solid var(--primary-color);
        box-shadow: 0 2px 4px var(--shadow-color);
    }

    .contact-card h5 {
        font-size: 1.1rem;
        color: var(--text-color);
        margin-bottom: 0.5rem;
    }

    .contact-card p {
        color: var(--text-muted);
    }

    .help-form {
        margin: 0;
        max-width: 100%;
        background: var(--card-bg);
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 4px 6px var(--shadow-color);
        border: 1px solid var(--border-color);
    }

    @media (max-width: 900px) {
        .help-container {
            grid-template-columns: 1fr;
        }
        .help-hero h1 {
            font-size: 2.5rem;
        }
    }
</style>

<div class="hero help-hero">
    <div style="max-width: 800px; padding: 2rem; text-align: center;">
        <h1>How can we help you?</h1>
        <p>Find answers to common questions or reach out to our team directly.</p>
    </div>
</div>

<div class="help-container">
    <!-- FAQ Section -->
    <div class="faq-section">
        <h2>ℹ️ Frequently Asked Questions</h2>
        
        <div class="faq-item">
            <h4>How do I cancel my booking?</h4>
            <p>You can cancel your booking by navigating to your Dashboard and selecting the 'Cancel' button next to your active booking. Please check our cancellation policy for applicable charges.</p>
        </div>
        
        <div class="faq-item">
            <h4>What payment methods do you accept?</h4>
            <p>We accept all major credit/debit cards, UPI, Internet Banking, and digital wallets. All transactions are securely encrypted for your safety.</p>
        </div>
        
        <div class="faq-item">
            <h4>When will I receive my refund?</h4>
            <p>Once a cancellation is initiated, refunds are processed within 5-7 business days depending on your original payment method and bank processing times.</p>
        </div>
        
        <div class="faq-item">
            <h4>Can I modify my booking details?</h4>
            <p>Yes, booking details can be modified up to 48 hours before the scheduled travel time. Additional charges may apply based on availability and price differences.</p>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="contact-section">
        <h2>📞 Contact Us</h2>
        
        <div class="contact-details">
            <div class="contact-card">
                <h5>Email Support</h5>
                <p><a href="mailto:ksetcse@gmail.com" style="color: inherit; text-decoration: none;">ksetcse@gmail.com</a></p>
                <p>We aim to reply within 24 hours.</p>
            </div>
            <div class="contact-card border-secondary">
                <h5>Phone Support</h5>
                <p><a href="tel:9510243015" style="color: inherit; text-decoration: none;">9510243015</a></p>
                <p>Mon - Fri, 9:00 AM - 6:00 PM</p>
            </div>
            <div class="contact-card border-danger">
                <h5>Office Address</h5>
                <p>123 Wanderlust Avenue,</p>
                <p>Travel Tech District, NY 10001</p>
            </div>
        </div>

        <form class="help-form form-container">
            <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" placeholder="John Doe" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" class="form-control" placeholder="john@example.com" required>
            </div>
            <div class="form-group">
                <label>Subject</label>
                <select class="form-control" required>
                    <option value="" disabled selected>Select a subject</option>
                    <option value="booking">Booking Inquiry</option>
                    <option value="cancellation">Cancellation Request</option>
                    <option value="refund">Refund Status</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea class="form-control" rows="5" placeholder="How can we help you?" required></textarea>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Send Message</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
