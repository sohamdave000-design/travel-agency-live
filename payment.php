<?php
require_once 'config/database.php';
include 'includes/header.php';

if (!isLoggedIn()) {
    redirect('login.html');
}

// Handle payment processing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_payment'])) {
    $booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : null;
    $is_existing_booking = (bool)$booking_id;
    $booking_type = sanitize($_POST['booking_type']);
    $item_id = (int)$_POST['item_id'];
    $price = (float)$_POST['price'];
    $total_price = (float)($_POST['total_price'] ?? 0);
    $payment_method = sanitize($_POST['payment_method']);

    // Server-side calculation fallback to ensure price is never 0
    if ($total_price <= 0 && !$booking_id) {
        if ($booking_type == 'package') {
            $stmt = $pdo->prepare("SELECT price FROM packages WHERE id = ?");
            $stmt->execute([$item_id]);
            $base = $stmt->fetchColumn();
            $total_price = (float)$base * (int)($_POST['persons'] ?? 1);
        } elseif ($booking_type == 'bus') {
            $stmt = $pdo->prepare("SELECT price FROM buses WHERE id = ?");
            $stmt->execute([$item_id]);
            $base = $stmt->fetchColumn();
            $total_price = (float)$base * (int)($_POST['persons'] ?? 1);
        } elseif ($booking_type == 'hotel') {
            $stmt = $pdo->prepare("SELECT price_per_night FROM hotels WHERE id = ?");
            $stmt->execute([$item_id]);
            $base = $stmt->fetchColumn();
            $mult = (float)($_POST['room_type'] ?? 1);
            $s = new DateTime($_POST['start_date']);
            $e = new DateTime($_POST['end_date']);
            $nights = $s->diff($e)->days ?: 1;
            $total_price = (float)$base * $mult * $nights;
        }
    }
    
    $card_last4 = null;
    $payer_email = $_SESSION['user_email'] ?? $_SESSION['user_name'] . '@example.com'; 
    if ($payment_method == 'card' && isset($_POST['card_number'])) {
        $card_last4 = substr(preg_replace('/\D/', '', $_POST['card_number']), -4);
    }
    
    try {
        $pdo->beginTransaction();

        if ($booking_id) {
            // Update existing pending booking
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ? AND user_id = ?");
            $stmt->execute([$booking_id, $_SESSION['user_id']]);
        } else {
            // Create new booking (for other flows)
            $start_date = sanitize($_POST['start_date']);
            $end_date = !empty($_POST['end_date']) ? sanitize($_POST['end_date']) : null;
            $extra_details = $_POST['extra_details'] ?? null;

            $stmt = $pdo->prepare("INSERT INTO bookings (user_id, booking_type, item_id, start_date, end_date, total_price, extra_details, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed')");
            $stmt->execute([$_SESSION['user_id'], $booking_type, $item_id, $start_date, $end_date, $total_price, $extra_details]);
            $booking_id = $pdo->lastInsertId();
        }

        // 2. Insert Payment
        $transaction_id = 'TXN' . strtoupper(substr(md5(time() . $booking_id), 0, 10));
        $stmt2 = $pdo->prepare("INSERT INTO payments (booking_id, transaction_id, user_id, amount, payment_method, card_last4, payer_email, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'completed')");
        $stmt2->execute([$booking_id, $transaction_id, $_SESSION['user_id'], $total_price, $payment_method, $card_last4, $payer_email]);
        
        // 3. Update Availability for NEW bookings
        if (!$is_existing_booking) { 
            if ($booking_type == 'bus') {
                $persons = isset($_POST['persons']) ? (int)$_POST['persons'] : 1;
                $pdo->prepare("UPDATE buses SET available_seats = available_seats - ? WHERE id = ? AND available_seats >= ?")->execute([$persons, $item_id, $persons]);
            }
            if ($booking_type == 'rental') {
                $pdo->prepare("UPDATE rentals SET available = 0 WHERE id = ?")->execute([$item_id]);
            }
        }

        $pdo->commit();
        redirect("payment.html?success=1&booking_id=$booking_id&txn_id=$transaction_id");
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Payment failed: " . $e->getMessage();
    }
}

$success = isset($_GET['success']);
$booking_details = ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['process_payment'])) ? $_POST : null;

// If we land here without POST or success, go home
if (!$success && !$booking_details) {
    echo "<div class='container text-center' style='padding: 5rem;'><p>No active booking found.</p><a href='index.html' class='btn-primary'>Return Home</a></div>";
    include 'includes/footer.php';
    exit;
}
?>

<style>
    :root {
        --checkout-primary: #2563eb;
        --checkout-bg: #fdfdfd;
        --checkout-border: #e2e8f0;
    }

    .payment-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1rem;
        animation: fadeIn 0.4s ease-out;
    }

    .checkout-layout {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 2rem;
    }

    /* Tabs & Selection */
    .method-selector {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--checkout-border);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .method-tab {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 1.25rem 1.5rem;
        border: none;
        background: none;
        cursor: pointer;
        text-align: left;
        border-bottom: 1px solid var(--checkout-border);
        transition: all 0.2s;
        gap: 0.75rem;
    }

    .method-tab:last-child { border-bottom: none; }

    .method-tab.active {
        background: #f8fafc;
        border-left: 4px solid var(--checkout-primary);
        padding-left: calc(1.5rem - 4px);
    }

    .method-tab .icon-wrap {
        width: 40px;
        height: 40px;
        background: #f1f5f9;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .method-tab.active .icon-wrap { background: #dbeafe; color: var(--checkout-primary); }

    .method-tab .label-wrap { flex: 1; }
    .method-tab .label-wrap span { display: block; font-weight: 700; color: #1e293b; }
    .method-tab .label-wrap small { color: #64748b; font-size: 0.8rem; }

    /* Forms */
    .payment-body {
        background: white;
        border-radius: 12px;
        border: 1px solid var(--checkout-border);
        padding: 2rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }

    .method-content { display: none; }
    .method-content.active { display: block; animation: slideUp 0.3s ease-out; }

    /* Card Layout */
    .card-logos { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; align-items: center; }
    .card-logos img { height: 20px; filter: grayscale(0.4); opacity: 0.8; }

    .credit-card-ui {
        background: linear-gradient(135deg, #1e293b, #0f172a);
        padding: 1.5rem;
        border-radius: 12px;
        color: white;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .credit-card-ui::after {
        content: ''; position: absolute; top: -50%; right: -20%; width: 100%; height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%);
    }

    /* UPI Styles */
    .upi-qr-frame {
        background: white;
        border: 2px solid #f1f5f9;
        padding: 1.5rem;
        border-radius: 16px;
        text-align: center;
        max-width: 280px;
        margin: 0 auto 1.5rem;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
    }

    .upi-apps { display: flex; justify-content: center; gap: 1rem; margin-top: 1rem; }
    .upi-apps img { height: 32px; width: 32px; object-fit: contain; }

    /* Netbanking Grid */
    .bank-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    .bank-item {
        border: 1px solid var(--checkout-border);
        padding: 0.75rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .bank-item:hover { border-color: var(--checkout-primary); background: #eff6ff; }
    .bank-item img { width: 24px; height: 24px; object-fit: contain; }
    .bank-item span { font-size: 0.9rem; font-weight: 500; }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { transform: translateY(10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    /* Success Theme */
    .success-card {
        background: white;
        border-radius: 24px;
        padding: 4rem 2rem;
        text-align: center;
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }

    .checkmark-circle {
        width: 100px; height: 100px;
        background: #10b981;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 3rem; margin: 0 auto 2rem;
        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes popIn { 0% { transform: scale(0); } 100% { transform: scale(1); } }
</style>

<div class="payment-container">

    <?php if($success): ?>
        <div class="success-card">
            <div class="checkmark-circle">✓</div>
            <h1 style="font-size: 2.5rem; color: #0f172a; margin-bottom: 0.5rem;">Payment Received</h1>
            <p style="color: #64748b; margin-bottom: 2.5rem; font-size: 1.1rem;">
                Booking Code: <strong style="color: #2563eb;">#TA-<?php echo str_pad($_GET['booking_id'], 5, '0', STR_PAD_LEFT); ?></strong><br>
                Reference: <span style="font-family: monospace;"><?php echo htmlspecialchars($_GET['txn_id']); ?></span>
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <a href="invoice.html?id=<?php echo $_GET['booking_id']; ?>" class="btn-primary" style="padding: 1rem 2.5rem; border-radius: 12px;">📄 View Invoice</a>
                <a href="dashboard.html" class="btn-secondary" style="padding: 1rem 2.5rem; border-radius: 12px; background: #f1f5f9; color: #1e293b !important;">My Account</a>
            </div>
        </div>

    <?php else: ?>
        <?php
            $type = $booking_details['booking_type'];
            $price = (float)$booking_details['price'];
            $start = $booking_details['start_date'];
            $end = $booking_details['end_date'] ?? null;
            $persons = (int)($booking_details['persons'] ?? 1);
            
            $total = isset($booking_details['total_price']) ? (float)$booking_details['total_price'] : ($price * $persons);
            $qty_label = "$persons traveler(s)";
            if (!isset($booking_details['total_price']) && in_array($type, ['hotel', 'rental']) && $end) {
                $days = (new DateTime($start))->diff(new DateTime($end))->days;
                if ($days == 0) $days = 1;
                $total = $price * $days;
                $qty_label = "$days night(s)/day(s)";
            }
            $extra = isset($booking_details['extra_details']) ? json_decode($booking_details['extra_details'], true) : null;
        ?>

        <div class="checkout-layout">
            
            <div>
                <!-- Method Selection -->
                <div class="method-selector">
                    <button class="method-tab active" onclick="switchMethod('card')">
                        <div class="icon-wrap"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg></div>
                        <div class="label-wrap"><span>Card Payment</span><small>Visa, Mastercard, RuPay</small></div>
                    </button>
                    <button class="method-tab" onclick="switchMethod('upi')">
                        <div class="icon-wrap"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M5 5l14 14M19 5L5 14"/></svg></div>
                        <div class="label-wrap"><span>UPI QR Scanner</span><small>Google Pay, PhonePe, Paytm</small></div>
                    </button>
                    <button class="method-tab" onclick="switchMethod('net')">
                        <div class="icon-wrap"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M11 10v11M15 10v11M20 10v11"/></svg></div>
                        <div class="label-wrap"><span>Net Banking</span><small>All Indian Banks</small></div>
                    </button>
                </div>

                <!-- Payment Body -->
                <div class="payment-body">
                    <form id="mainPaymentForm" method="POST" onsubmit="return simulatePayment(event)">
                        <input type="hidden" name="process_payment" value="1">
                        <?php foreach($booking_details as $k => $v): ?>
                            <input type="hidden" name="<?php echo htmlspecialchars($k); ?>" value="<?php echo htmlspecialchars($v); ?>">
                        <?php endforeach; ?>
                        <input type="hidden" name="payment_method" id="selected_method" value="card">

                        <!-- Card Form -->
                        <div id="method-card" class="method-content active">
                            <div class="card-logos">
                                <img src="https://raw.githubusercontent.com/aaronfagan/svg-credit-card-payment-icons/master/flat/visa.svg" alt="Visa">
                                <img src="https://raw.githubusercontent.com/aaronfagan/svg-credit-card-payment-icons/master/flat/mastercard.svg" alt="MasterCard">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/c/cb/Rupay-Logo.svg" alt="RuPay">
                            </div>
                            
                            <div class="credit-card-ui">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 2rem;">
                                    <div style="width: 40px; height: 28px; background: #f1f5f9; border-radius: 4px; opacity: 0.3;"></div>
                                    <div id="cardBrandLogo" style="font-weight: 700; font-style: italic; opacity: 0.7;">CARD</div>
                                </div>
                                <div id="cardNumberDisp" style="font-size: 1.25rem; letter-spacing: 2px; margin-bottom: 1rem; font-family: 'Courier New', monospace;">•••• •••• •••• ••••</div>
                                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; opacity: 0.8;">
                                    <div id="cardNameDisp">CARDHOLDER NAME</div>
                                    <div id="cardExpiryDisp">MM/YY</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Cardholder Name</label>
                                <input type="text" name="card_name" class="form-control" placeholder="Full Name" oninput="updateCardUI('name', this.value)">
                            </div>
                            <div class="form-group">
                                <label>Card Number</label>
                                <input type="text" name="card_number" class="form-control" id="cardNumberInput" placeholder="0000 0000 0000 0000" maxlength="19">
                            </div>
                            <div class="grid-2">
                                <div class="form-group">
                                    <label>Expiry Date</label>
                                    <input type="text" name="card_expiry" class="form-control" placeholder="MM/YY" maxlength="5" oninput="updateCardUI('expiry', this.value)">
                                </div>
                                <div class="form-group">
                                    <label>CVV</label>
                                    <input type="password" name="card_cvv" class="form-control" placeholder="•••" maxlength="3">
                                </div>
                            </div>
                        </div>

                        <!-- UPI Form -->
                        <div id="method-upi" class="method-content">
                            <div class="upi-qr-frame">
                                <div style="font-weight: 700; margin-bottom: 1rem; color: #1e293b;">Scan to Pay</div>
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=upi://pay?pa=travelagency@axis&pn=TravelAgency&am=<?php echo $total; ?>&cu=INR" alt="UPI QR" style="display: block; margin: 0 auto;">
                                <div class="upi-apps">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/c/c7/Google_Pay_Logo.svg" alt="GPay">
                                    <img src="https://www.vectorlogo.zone/logos/phonepe/phonepe-ar21.svg" alt="PhonePe">
                                    <img src="https://www.vectorlogo.zone/logos/paytm/paytm-ar21.svg" alt="Paytm">
                                </div>
                            </div>
                            <div style="text-align: center; color: #64748b; font-size: 0.9rem;">
                                Or enter your UPI ID
                                <div class="mt-4">
                                    <input type="text" class="form-control" placeholder="your-id@upi" style="text-align: center; max-width: 250px; margin: 0 auto;">
                                </div>
                            </div>
                        </div>

                        <!-- Netbanking Form -->
                        <div id="method-net" class="method-content">
                            <label style="display: block; margin-bottom: 1rem; font-weight: 600;">Popular Banks</label>
                            <div class="bank-grid">
                                <div class="bank-item"><img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/SBI-Logo.svg" alt="SBI"> <span>State Bank</span></div>
                                <div class="bank-item"><img src="https://www.vectorlogo.zone/logos/hdfcbank/hdfcbank-icon.svg" alt="HDFC"> <span>HDFC Bank</span></div>
                                <div class="bank-item"><img src="https://upload.wikimedia.org/wikipedia/commons/1/12/ICICI_Bank_Logo.svg" alt="ICICI"> <span>ICICI Bank</span></div>
                                <div class="bank-item"><img src="https://www.axisbank.com/assets/images/logo.png" alt="Axis" style="background:#811234; padding:2px; border-radius:2px;"> <span>Axis Bank</span></div>
                            </div>
                            <div class="form-group mt-4">
                                <label>Other Banks</label>
                                <select class="form-control">
                                    <option>Select Bank</option>
                                    <option>Bank of Baroda</option>
                                    <option>Canara Bank</option>
                                    <option>PNB</option>
                                    <option>Yes Bank</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" id="payBtn" class="btn-primary" style="width: 100%; padding: 1.25rem; font-size: 1.1rem; border-radius: 12px; margin-top: 2rem; background: var(--checkout-primary);">
                            Proceed to Secure Payment
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Summary -->
            <aside>
                <div style="background: white; border-radius: 12px; padding: 1.5rem; border: 1px solid var(--checkout-border); position: sticky; top: 100px;">
                    <h3 style="margin-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">Summary</h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem;">
                        <div class="flex justify-between">
                            <span style="color: #64748b;">Booking For</span>
                            <strong style="text-transform: capitalize; color: #1e293b;"><?php echo $type; ?></strong>
                        </div>
                        <div class="flex justify-between">
                            <span style="color: #64748b;">Travel Date</span>
                            <strong style="color: #1e293b;"><?php echo date('D, d M Y', strtotime($start)); ?></strong>
                        </div>
                        <div class="flex justify-between">
                            <span style="color: #64748b;">Travelers</span>
                            <strong style="color: #1e293b;"><?php echo $qty_label; ?></strong>
                        </div>

                        <?php if ($extra): ?>
                            <hr style="border: none; border-top: 1px dashed #e2e8f0; margin: 0.5rem 0;">
                            <?php if (isset($extra['vehicle_name'])): ?>
                                <div class="flex justify-between">
                                    <span style="color: #64748b;">Vehicle</span>
                                    <strong style="color: #1e293b;"><?php echo htmlspecialchars($extra['vehicle_name']); ?></strong>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($extra['driver'])): ?>
                                <div class="flex justify-between">
                                    <span style="color: #64748b;">Operator</span>
                                    <strong style="color: #1e293b;"><?php echo $extra['driver'] === 'with_driver' ? 'Full Service' : 'Self-Drive'; ?></strong>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div style="background: #f8fafc; margin: 1rem -1.5rem; padding: 1.5rem;">
                            <div class="flex justify-between" style="font-size: 1.25rem; font-weight: 800;">
                                <span>Grand Total</span>
                                <span style="color: #2563eb;">₹<?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.5rem; color: #059669; font-size: 0.8rem; font-weight: 600;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Secure 256-bit SSL Encryption
                    </div>
                </div>
            </aside>

        </div>
    <?php endif; ?>
</div>

<script>
function switchMethod(method) {
    document.querySelectorAll('.method-tab').forEach(b => b.classList.remove('active'));
    document.querySelector(`.method-tab[onclick*="${method}"]`).classList.add('active');
    
    document.querySelectorAll('.method-content').forEach(s => s.classList.remove('active'));
    document.getElementById(`method-${method}`).classList.add('active');
    
    document.getElementById('selected_method').value = method;
}

function updateCardUI(field, val) {
    if (field === 'name') document.getElementById('cardNameDisp').textContent = val.toUpperCase() || 'CARDHOLDER NAME';
    if (field === 'expiry') document.getElementById('cardExpiryDisp').textContent = val || 'MM/YY';
}

document.getElementById('cardNumberInput').addEventListener('input', function(e) {
    let v = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    let parts = [];
    for (let i=0; i<v.length; i+=4) parts.push(v.substring(i, i+4));
    e.target.value = parts.join(' ');
    document.getElementById('cardNumberDisp').textContent = e.target.value || '•••• •••• •••• ••••';
    
    // Brand detection simple
    const brand = document.getElementById('cardBrandLogo');
    if (v.startsWith('4')) brand.textContent = 'VISA';
    else if (v.startsWith('5')) brand.textContent = 'MASTERCARD';
    else if (v.startsWith('6')) brand.textContent = 'RUPAY';
    else brand.textContent = 'CARD';
});

function simulatePayment(e) {
    e.preventDefault();
    const btn = document.getElementById('payBtn');
    const oldText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<div style="display:flex; align-items:center; justify-content:center; gap:10px;">
        <div style="width:20px; height:20px; border:3px solid white; border-top-color:transparent; border-radius:50%; animation:spin 0.8s linear infinite;"></div>
        Authorizing...</div>`;

    if(!document.getElementById('spin-style')) {
        const style = document.createElement('style');
        style.id = 'spin-style';
        style.innerHTML = "@keyframes spin { to { transform: rotate(360deg); } }";
        document.head.appendChild(style);
    }

    setTimeout(() => {
        document.getElementById('mainPaymentForm').submit();
    }, 2500);
}
</script>

<?php include 'includes/footer.php'; ?>
