<?php
require_once 'config/database.php';
include 'includes/header.php';
?>

<div style="background: linear-gradient(to right, #64748b, #475569); margin: -2rem -2rem 2rem; padding: 4rem 2rem; text-align: center; color: white;">
    <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Frequently Asked Questions</h1>
    <p style="font-size: 1.1rem; max-width: 600px; margin: 0 auto;">Everything you need to know about booking with Travel Agency.</p>
</div>

<div style="max-width: 800px; margin: 0 auto 4rem;">
    
    <div style="margin-bottom: 2rem;">
        <h2 style="color: var(--primary-dark); margin-bottom: 1rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem;">Booking & Payments</h2>
        
        <div style="background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 0.5rem; color: #1e293b;">How do I make a booking?</h3>
            <p style="color: #475569; line-height: 1.6;">You can make a booking directly through our website by creating an account. Browse our packages, hotels, buses, or rentals, select your dates, and proceed to checkout.</p>
        </div>
        
        <div style="background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 0.5rem; color: #1e293b;">What payment methods do you accept?</h3>
            <p style="color: #475569; line-height: 1.6;">We accept all major credit and debit cards, PayPal, and standard bank transfers. Payment is processed securely.</p>
        </div>
        
        <div style="background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 0.5rem; color: #1e293b;">Can I cancel my booking?</h3>
            <p style="color: #475569; line-height: 1.6;">Yes, you can cancel your booking from your dashboard. Pending bookings can be cancelled immediately. For confirmed bookings, cancellation policies may apply depending on the specific service provided.</p>
        </div>
    </div>
    
    <div style="margin-bottom: 2rem;">
        <h2 style="color: var(--primary-dark); margin-bottom: 1rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem;">Custom Packages</h2>
        
        <div style="background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 0.5rem; color: #1e293b;">How does the Custom Tour Builder work?</h3>
            <p style="color: #475569; line-height: 1.6;">Our completely customized tour builder allows you to specify your destination, dates, hotel preference, and transport. An estimated price is shown instantly. Once submitted, our admins review and approve your customized itinerary.</p>
        </div>
        
        <div style="background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 0.5rem; color: #1e293b;">Will the estimated price change?</h3>
            <p style="color: #475569; line-height: 1.6;">The estimate is based on our standard pricing algorithms. Upon admin review, the price might be adjusted slightly based on real-time flight rates and hotel availability before final approval.</p>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 3rem; padding: 2rem; background: #f8fafc; border-radius: 8px; border: 1px dashed var(--primary-color);">
        <h3 style="margin-bottom: 0.5rem;">Still have questions?</h3>
        <p style="color: #64748b; margin-bottom: 1rem;">We're happy to help you with any queries you might have.</p>
        <a href="contact.html" class="btn-primary">Contact Us</a>
    </div>

</div>

<?php include 'includes/footer.php'; ?>

