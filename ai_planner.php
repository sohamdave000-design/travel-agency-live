<?php
require_once 'config/database.php';
include 'includes/header.php';
?>

<style>
/* AI Planner Premium Styles */
.planner-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    padding: 6rem 2rem;
    text-align: center;
    color: white;
    border-radius: 20px;
    margin-bottom: 3rem;
    position: relative;
    overflow: hidden;
}

.planner-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url('https://images.unsplash.com/photo-1506012733851-00220bc0e998?auto=format&fit=crop&w=2000&q=80') center/cover;
    opacity: 0.2;
    mix-blend-mode: overlay;
}

.planner-hero h1 {
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 800;
    margin-bottom: 1rem;
    position: relative;
}

.planner-hero p {
    font-size: 1.25rem;
    color: #94a3b8;
    max-width: 600px;
    margin: 0 auto;
    position: relative;
}

.wizard-container {
    max-width: 800px;
    margin: -4rem auto 4rem;
    background: white;
    padding: 3rem;
    border-radius: 24px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.1);
    position: relative;
    z-index: 10;
}

/* Steps */
.step { display: none; }
.step.active { display: block; animation: slideIn 0.5s ease; }

@keyframes slideIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.step-header { margin-bottom: 2rem; }
.step-title { font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; }
.step-desc { color: #64748b; font-size: 1rem; }

/* Grid Selectors */
.vibe-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.vibe-card {
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.vibe-card:hover { border-color: #2563eb; background: #f8fafc; transform: translateY(-5px); }
.vibe-card.selected { border-color: #2563eb; background: #eff6ff; box-shadow: 0 0 0 4px rgba(37,99,235,0.1); }

.vibe-icon { font-size: 2.5rem; margin-bottom: 1rem; display: block; }
.vibe-name { font-weight: 700; color: #1e293b; }

/* Loading State */
#loading-step { text-align: center; padding: 4rem 2rem; }
.ai-loader {
    width: 60px;
    height: 60px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #2563eb;
    border-radius: 50%;
    margin: 0 auto 2rem;
    animation: spin 1s linear infinite;
}

@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

/* Itinerary Result */
#result-step { display: none; }
.itinerary-day {
    margin-bottom: 2rem;
    padding-left: 2rem;
    border-left: 2px solid #e2e8f0;
    position: relative;
}

.itinerary-day::before {
    content: '';
    position: absolute;
    left: -9px;
    top: 0;
    width: 16px;
    height: 16px;
    background: #2563eb;
    border-radius: 50%;
    border: 4px solid white;
}

.day-header { font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem; }
.activity-card {
    background: #f8fafc;
    padding: 1.25rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    border: 1px solid #e2e8f0;
}

.activity-time { font-size: 0.8rem; font-weight: 700; color: #2563eb; text-transform: uppercase; margin-bottom: 0.25rem; }
.activity-name { font-weight: 700; color: #0f172a; font-size: 1.05rem; }
.activity-desc { font-size: 0.9rem; color: #64748b; }

/* Booking Modal */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}
.modal-overlay.active { display: flex; }
.modal-content {
    background: white;
    padding: 2.5rem;
    border-radius: 20px;
    max-width: 450px;
    width: 90%;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    animation: modalIn 0.3s ease;
}
@keyframes modalIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}
.booking-summary {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 12px;
    margin: 1.5rem 0;
    border: 1px dashed #cbd5e1;
}
.price-row { display: flex; justify-content: space-between; font-weight: 700; font-size: 1.25rem; color: #2563eb; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; }

.btn-group { display: flex; gap: 1rem; margin-top: 2rem; justify-content: space-between; }
</style>

<div class="planner-hero">
    <h1>AI-Powered Trip Planner</h1>
    <p>Tell us your dreams, and our AI will craft the perfect day-by-day itinerary tailored just for you.</p>
</div>

<div class="wizard-container">
    <form id="ai-planner-form">
        <!-- Step 1: Destination -->
        <div class="step active" id="step-1">
            <div class="step-header">
                <div class="step-title">Where to?</div>
                <div class="step-desc">Enter a city or region you want to explore.</div>
            </div>
            <div class="form-group">
                <input type="text" id="destination" class="form-control" placeholder="e.g. Goa, Manali, Ladakh..." required style="padding: 1rem; font-size: 1.2rem;">
            </div>
            <div class="btn-group">
                <span></span>
                <button type="button" class="btn-primary" onclick="nextStep(2)">Continue &rarr;</button>
            </div>
        </div>

        <!-- Step 2: Duration & Budget -->
        <div class="step" id="step-2">
            <div class="step-header">
                <div class="step-title">The Small Details</div>
                <div class="step-desc">How long and what's the budget?</div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Duration (Days)</label>
                    <input type="number" id="duration" class="form-control" value="3" min="1" max="14">
                </div>
                <div class="form-group">
                    <label>Budget</label>
                    <select id="budget" class="form-control">
                        <option value="Economy">Economy (Budget Friendly)</option>
                        <option value="Balanced" selected>Standard (Value for Money)</option>
                        <option value="Luxury">Premium (High End)</option>
                    </select>
                </div>
            </div>
            <div class="btn-group">
                <button type="button" class="btn-secondary" onclick="nextStep(1)">&larr; Back</button>
                <button type="button" class="btn-primary" onclick="nextStep(3)">Next: Choose Vibe</button>
            </div>
        </div>

        <!-- Step 3: Vibes -->
        <div class="step" id="step-3">
            <div class="step-header">
                <div class="step-title">What's the vibe?</div>
                <div class="step-desc">Select the style of trip you're looking for.</div>
            </div>
            <div class="vibe-grid">
                <div class="vibe-card" onclick="selectVibe('Adventure', this)">
                    <span class="vibe-icon">🏔️</span>
                    <span class="vibe-name">Adventure</span>
                </div>
                <div class="vibe-card" onclick="selectVibe('Relaxation', this)">
                    <span class="vibe-icon">🏖️</span>
                    <span class="vibe-name">Relaxation</span>
                </div>
                <div class="vibe-card" onclick="selectVibe('Foodie', this)">
                    <span class="vibe-icon">🍲</span>
                    <span class="vibe-name">Foodie</span>
                </div>
                <div class="vibe-card" onclick="selectVibe('Cultural', this)">
                    <span class="vibe-icon">🏛️</span>
                    <span class="vibe-name">Cultural</span>
                </div>
                <div class="vibe-card selected" onclick="selectVibe('Balanced', this)">
                    <span class="vibe-icon">✨</span>
                    <span class="vibe-name">Balanced</span>
                </div>
            </div>
            <input type="hidden" id="vibe" value="Balanced">
            <div class="btn-group">
                <button type="button" class="btn-secondary" onclick="nextStep(2)">&larr; Back</button>
                <button type="button" class="btn-primary" onclick="generateItinerary()">✨ Generate Plan</button>
            </div>
        </div>

        <!-- Step 4: Loading -->
        <div class="step" id="step-loading">
            <div class="ai-loader"></div>
            <div class="step-title">Crafting your itinerary...</div>
            <p id="loading-tip">"Our AI is checking for the best spots in <span id="target-dest"></span>..."</p>
        </div>

        <!-- Final Step: Results -->
        <div class="step" id="step-result">
            <div class="step-header">
                <div class="step-title" id="res-title">My Trip to Goa</div>
                <div class="step-desc" id="res-summary">3 days of balanced adventure and culture.</div>
            </div>
            <div id="itinerary-container"></div>
            <div class="btn-group">
                <button type="button" class="btn-secondary" onclick="location.reload()">Reset</button>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="button" class="btn-primary" onclick="window.print()">🖨️ Print</button>
                    <?php if(!isLoggedIn()): ?>
                        <a href="login.html" class="btn-secondary" style="background:#10b981;">Login to Book</a>
                    <?php else: ?>
                        <button type="button" class="btn-primary" style="background:#10b981; border:none;" onclick="openBookingModal()">Book This Trip ✨</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Booking Modal -->
<div class="modal-overlay" id="booking-modal">
    <div class="modal-content">
        <h2 style="margin-bottom:0.5rem;">Confirm Your Trip</h2>
        <p style="color:#64748b; font-size:0.9rem;">Finalize your dates and traveler count.</p>
        
        <div class="form-group" style="margin-top:1.5rem;">
            <label>Start Date</label>
            <input type="date" id="book_start_date" class="form-control" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
        </div>
        
        <div class="form-group">
            <label>Number of Travelers</label>
            <input type="number" id="book_persons" class="form-control" value="1" min="1" oninput="updateBookingTotal()">
        </div>
        
        <div class="booking-summary">
            <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:#64748b; margin-bottom:0.5rem;">
                <span>Daily Rate (<span id="modal_vibe">Balanced</span>)</span>
                <span id="modal_rate">₹5,000</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:#64748b;">
                <span>Duration</span>
                <span id="modal_duration">3 Days</span>
            </div>
            <div class="price-row">
                <span>Total Amount</span>
                <span id="modal_total">₹15,000</span>
            </div>
        </div>
        
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
            <button class="btn-secondary" onclick="closeBookingModal()">Cancel</button>
            <button class="btn-primary" id="final-book-btn" onclick="confirmBooking()">Confirm Booking</button>
        </div>
    </div>
</div>

<script>
let currentStep = 1;
const tips = [
    "Searching for hidden gems...",
    "Finding the best local cuisine...",
    "Optimizing travel routes...",
    "Selecting top-rated activities...",
    "Personalizing your experience..."
];

function nextStep(step) {
    document.getElementById(`step-${currentStep}`).classList.remove('active');
    document.getElementById(`step-${step}`).classList.add('active');
    currentStep = step;
}

function selectVibe(vibe, element) {
    document.querySelectorAll('.vibe-card').forEach(c => c.classList.remove('selected'));
    element.classList.add('selected');
    document.getElementById('vibe').value = vibe;
}

async function generateItinerary() {
    const destination = document.getElementById('destination').value;
    if (!destination) {
        alert("Please enter a destination!");
        return;
    }

    document.getElementById('target-dest').innerText = destination;
    nextStep('loading');
    
    // Cycle through tips
    let tipIdx = 0;
    const tipInterval = setInterval(() => {
        tipIdx = (tipIdx + 1) % tips.length;
        document.getElementById('loading-tip').innerText = tips[tipIdx];
    }, 1500);

    const data = {
        destination: destination,
        duration: document.getElementById('duration').value,
        budget: document.getElementById('budget').value,
        vibe: document.getElementById('vibe').value
    };

    try {
        const response = await fetch('api/ai_engine.html', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: { 'Content-Type': 'application/json' }
        });
        const result = await response.json();
        
        clearInterval(tipInterval);
        
        if (result.success) {
            displayItinerary(result);
        } else {
            alert("Error: " + result.message);
            nextStep(3);
        }
    } catch (error) {
        console.error(error);
        alert("Something went wrong!");
        nextStep(3);
    }
}

let currentPlan = null;
const rates = { 'Economy': 2500, 'Balanced': 5000, 'Luxury': 12000 };

function displayItinerary(result) {
    currentPlan = result;
    document.getElementById('step-loading').classList.remove('active');
    document.getElementById('step-result').classList.add('active');
    currentStep = 'result';
    
    document.getElementById('res-title').innerText = `Your Trip to ${result.destination}`;
    document.getElementById('res-summary').innerText = result.summary;
    
    const container = document.getElementById('itinerary-container');
    container.innerHTML = '';
    
    result.itinerary.forEach(day => {
        let dayHtml = `
            <div class="itinerary-day">
                <div class="day-header">Day ${day.day}</div>
                ${day.activities.map(act => `
                    <div class="activity-card">
                        <div class="activity-time">${act.time}</div>
                        <div class="activity-name">${act.activity}</div>
                        <div class="activity-desc">${act.description}</div>
                    </div>
                `).join('')}
            </div>
        `;
        container.innerHTML += dayHtml;
    });
}

function openBookingModal() {
    if (!currentPlan || !currentPlan.plan_id) {
        alert("Please generate a plan first!");
        return;
    }
    document.getElementById('modal_vibe').innerText = currentPlan.budget;
    document.getElementById('modal_rate').innerText = '₹' + rates[currentPlan.budget].toLocaleString();
    document.getElementById('modal_duration').innerText = currentPlan.duration + ' Days';
    updateBookingTotal();
    document.getElementById('booking-modal').classList.add('active');
}

function closeBookingModal() {
    document.getElementById('booking-modal').classList.remove('active');
}

function updateBookingTotal() {
    const persons = document.getElementById('book_persons').value;
    const total = rates[currentPlan.budget] * currentPlan.duration * persons;
    document.getElementById('modal_total').innerText = '₹' + total.toLocaleString();
}

async function confirmBooking() {
    const btn = document.getElementById('final-book-btn');
    btn.innerText = 'Booking...';
    btn.disabled = true;

    const data = {
        plan_id: currentPlan.plan_id,
        persons: document.getElementById('book_persons').value,
        start_date: document.getElementById('book_start_date').value
    };

    try {
        const response = await fetch('api/book_ai_plan.html', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: { 'Content-Type': 'application/json' }
        });
        const result = await response.json();
        
        if (result.success) {
            // Create a hidden form and submit to payment.html
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'payment.html';
            
            const fields = {
                booking_id: result.booking_id,
                booking_type: 'custom',
                item_id: result.plan_id,
                price: result.total_price, // Single price for total
                total_price: result.total_price,
                start_date: data.start_date,
                persons: data.persons
            };
            
            for (const key in fields) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = fields[key];
                form.appendChild(input);
            }
            
            document.body.appendChild(form);
            form.submit();
        } else {
            alert("Error: " + result.message);
            btn.innerText = 'Confirm Booking';
            btn.disabled = false;
        }
    } catch (e) {
        alert("Something went wrong!");
        btn.innerText = 'Confirm Booking';
        btn.disabled = false;
    }
}
</script>

<?php include 'includes/footer.php'; ?>
