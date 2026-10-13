<?php
// Include the database connection to get specific room details dynamically
include 'config/db.php';

// Catch the room number from the URL parameter (Passed from rooms.php)
$room_number = isset($_GET['room']) ? intval($_GET['room']) : 1; 

// Fetch the rate of the selected room from the database
$room_query = "SELECT rate FROM rooms WHERE room_number = ?";
$stmt = $conn->prepare($room_query);
$stmt->bind_param("i", $room_number);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();
$room_rate = $room ? $room['rate'] : 2000; 

// Fetch live meal rates from the database table 'meal_rates'
$meals_live = [];
$meal_query = "SELECT * FROM meal_rates";
$meal_res = mysqli_query($conn, $meal_query);
if ($meal_res && mysqli_num_rows($meal_res) > 0) {
    while ($m_row = mysqli_fetch_assoc($meal_res)) {
        $meals_live[$m_row['meal_name']] = $m_row['price'];
    }
}

// Map database prices with standard fallback rates if database is empty
$meal_rates = [
    'breakfast_veg'     => isset($meals_live['Breakfast Veg']) ? $meals_live['Breakfast Veg'] : 100,
    'breakfast_non_veg' => isset($meals_live['Breakfast Non-Veg']) ? $meals_live['Breakfast Non-Veg'] : 150,
    'dinner_veg'        => isset($meals_live['Dinner Veg']) ? $meals_live['Dinner Veg'] : 200,
    'dinner_non_veg'    => isset($meals_live['Dinner Non-Veg']) ? $meals_live['Dinner Non-Veg'] : 250
];

// Include the unified header
include 'header.php';
?>
    <style>
        /* --- Custom Styles for the Booking Page Integrated with Theme Variables --- */
        :root {
            --form-bg: #ffffff;
            --input-bg: #fafafa;
            --input-border: #cccccc;
            --label-color: #444444;
            --bill-bg: #fff9db;
            --bill-border: #fab005;
            --bill-text: #444444;
            --bill-total-text: #000000;
        }

        [data-theme="dark"] {
            --form-bg: #1e1e1e;
            --input-bg: #2a2a2a;
            --input-border: #444444;
            --label-color: #dddddd;
            --bill-bg: #2d2812;
            --bill-border: #e6a100;
            --bill-text: #cccccc;
            --bill-total-text: #ffffff;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            padding: 40px 20px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .booking-container {
            max-width: 550px;
            margin: 0 auto;
            background: var(--form-bg);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-color);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #ff4d4d;
            text-decoration: none;
            font-weight: 600;
        }

        h1 {
            font-size: 1.8rem;
            color: var(--text-color);
            margin-bottom: 5px;
            transition: color 0.3s ease;
        }

        .room-meta {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin-bottom: 25px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 12px;
            transition: color 0.3s ease, border-color 0.3s ease;
        }

        .form-section-title {
            font-size: 1.1rem;
            color: var(--text-color);
            margin: 25px 0 15px 0;
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 5px;
            transition: color 0.3s ease, border-color 0.3s ease;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            color: var(--label-color);
            margin-bottom: 6px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--input-border);
            border-radius: 6px;
            font-size: 0.95rem;
            background-color: var(--input-bg);
            color: var(--text-color);
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        .date-row {
            margin-bottom: 15px;
        }

        /* Meal Option Styles using Checkbox */
        .meal-box {
            background-color: var(--input-bg);
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--input-border);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .meal-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 0.95rem;
            color: var(--text-color);
            transition: color 0.3s ease;
        }

        .meal-item:last-child {
            margin-bottom: 0;
        }

        .meal-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .meal-price {
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .meal-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #fab005; 
            cursor: pointer;
        }

        /* Bill Summary Box Styles */
        .bill-summary-box {
            background-color: var(--bill-bg);
            border: 2px dashed var(--bill-border);
            border-radius: 8px;
            padding: 20px;
            margin-top: 25px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .bill-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95rem;
            color: var(--bill-text);
            transition: color 0.3s ease;
        }

        .bill-row.total-row {
            border-top: 1px solid var(--bill-border);
            padding-top: 10px;
            margin-top: 10px;
            font-weight: bold;
            font-size: 1.1rem;
            color: var(--bill-total-text);
            transition: border-color 0.3s ease, color 0.3s ease;
        }

    
        .checkbox-verify-container {
            margin-top: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.95rem;
            color: var(--text-color);
        }

        .checkbox-verify-container input {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            accent-color: #fab005;
            cursor: pointer;
        }

        .btn-verify {
            display: block;
            width: 100%;
            background-color: #fab005;
            color: #ffffff;
            padding: 14px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 25px;
            transition: background 0.2s ease;
            text-align: center;
        }

        .btn-verify:hover {
            background-color: #e6a100;
        }
    </style>

    <div class="booking-container">
        <a href="rooms.php" class="back-link">&larr; Back to Rooms</a>
        
        <h1>Book Room <?php echo $room_number; ?></h1>
        <p class="room-meta">Capacity: 2-4 guests max | Base rate: ₹<?php echo number_format($room_rate); ?> / night</p>
        
        <form id="bookingForm" action="js/book.php?room=<?php echo $room_number; ?>" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="room_number" value="<?php echo $room_number; ?>">

            <div class="form-group">
                <label for="customer_name">Customer Name (As on ID Proof):</label>
                <input type="text" id="customer_name" name="customer_name" required placeholder="Enter full name">
            </div>
            
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="tel" id="phone_number" name="phone_number" value="+91 " required placeholder="+91 XXXXXXXXXX">
            </div>

            <div class="form-group">
                <label for="id_proof">Upload ID Proof (Image Name must match Customer Name):</label>
                <input type="file" id="id_proof" name="id_proof" accept="image/*,.pdf" required>
            </div>

            <div class="form-section-title">Select Dates</div>
            
            <div class="date-row">
                <div class="form-group">
                    <label for="check_in_date">Check-in Date:</label>
                    <input type="date" id="check_in_date" name="check_in_date" required>
                </div>
            </div>

            <div class="date-row">
                <div class="form-group">
                    <label for="check_out_date">Check-out Date:</label>
                    <input type="date" id="check_out_date" name="check_out_date" required>
                </div>
            </div>

            <div class="form-section-title">Arrival & Departure Times</div>
            <div class="date-row">
                <div class="form-group">
                    <label for="arrival_time">Check-in Time (Arrival):</label>
                    <input type="time" id="arrival_time" name="arrival_time" value="14:00">
                </div>
            </div>
            <div class="date-row">
                <div class="form-group">
                    <label for="departure_time">Check-out Time (Departure):</label>
                    <input type="time" id="departure_time" name="departure_time" value="11:00">
                </div>
            </div>

            <div class="form-section-title">Meal Options</div>
            <div class="meal-box">
                <label class="meal-item">
                    <div class="meal-left">
                        <input type="checkbox" name="meal_option[]" value="Breakfast Veg" data-price="<?php echo $meal_rates['breakfast_veg']; ?>">
                        <span>Breakfast Veg</span>
                    </div>
                    <span class="meal-price">₹<?php echo $meal_rates['breakfast_veg']; ?></span>
                </label>
                <label class="meal-item">
                    <div class="meal-left">
                        <input type="checkbox" name="meal_option[]" value="Breakfast Non-Veg" data-price="<?php echo $meal_rates['breakfast_non_veg']; ?>">
                        <span>Breakfast Non-Veg</span>
                    </div>
                    <span class="meal-price">₹<?php echo $meal_rates['breakfast_non_veg']; ?></span>
                </label>
                <label class="meal-item">
                    <div class="meal-left">
                        <input type="checkbox" name="meal_option[]" value="Dinner Veg" data-price="<?php echo $meal_rates['dinner_veg']; ?>">
                        <span>Dinner Veg</span>
                    </div>
                    <span class="meal-price">₹<?php echo $meal_rates['dinner_veg']; ?></span>
                </label>
                <label class="meal-item">
                    <div class="meal-left">
                        <input type="checkbox" name="meal_option[]" value="Dinner Non-Veg" data-price="<?php echo $meal_rates['dinner_non_veg']; ?>">
                        <span>Dinner Non-Veg</span>
                    </div>
                    <span class="meal-price">₹<?php echo $meal_rates['dinner_non_veg']; ?></span>
                </label>
            </div>

            <div class="form-section-title">Bill Summary</div>
            <div class="bill-summary-box">
                <div class="bill-row">
                    <span>Total Nights:</span>
                    <span id="summary_nights">0</span>
                </div>
                <div class="bill-row">
                    <span>Room Total (₹<?php echo number_format($room_rate); ?> / night):</span>
                    <span>₹<span id="summary_room_total">0</span></span>
                </div>
                <div class="bill-row">
                    <span>Meals Total (Per Day):</span>
                    <span>₹<span id="summary_meals_total">0</span></span>
                </div>
                <div class="bill-row">
                    <span>GST (12%):</span>
                    <span>₹<span id="summary_gst">0</span></span>
                </div>
                <div class="bill-row total-row">
                    <span>Total Amount:</span>
                    <span>₹<span id="summary_grand_total">0</span></span>
                </div>
            </div>

            <div class="checkbox-verify-container">
                <input type="checkbox" id="name_declaration" required>
                <label for="name_declaration">I hereby declare that the <strong>Customer Name</strong> entered matches exactly with the attached <strong>ID Proof</strong>.</label>
            </div>

            <button type="submit" class="btn-verify">Verify & Book Now</button>
            
        </form>
    </div>

    <script>
        const roomRate = <?php echo $room_rate; ?>;
        const checkInInput = document.getElementById('check_in_date');
        const checkOutInput = document.getElementById('check_out_date');
        const mealCheckboxes = document.querySelectorAll('input[name="meal_option[]"]');
        const bookingForm = document.getElementById('bookingForm');
        const declarationCheckbox = document.getElementById('name_declaration');
        const phoneInput = document.getElementById('phone_number');
        const idProofInput = document.getElementById('id_proof');
        const customerNameInput = document.getElementById('customer_name');

        // --- Feature 1: Force Phone format (+91 ) ---
        phoneInput.addEventListener('input', function() {
            if (!this.value.startsWith('+91 ')) {
                this.value = '+91 ';
            }
            // Clear out non-digits from the phone number part
            let numPart = this.value.substring(4).replace(/\D/g, '');
            if (numPart.length > 10) {
                numPart = numPart.substring(0, 10);
            }
            this.value = '+91 ' + numPart;
        });

        phoneInput.addEventListener('keydown', function(e) {
            if (this.selectionStart < 4 && (e.key === 'Backspace' || e.key === 'Delete')) {
                e.preventDefault();
            }
        });

        function calculateBill() {
            const checkInDate = new Date(checkInInput.value);
            const checkOutDate = new Date(checkOutInput.value);
            
            let nights = 0;
            
            if (checkInInput.value && checkOutInput.value && checkOutDate > checkInDate) {
                const diffTime = Math.abs(checkOutDate - checkInDate);
                nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            }

            const roomTotal = nights * roomRate;

            let mealsPerDayCost = 0;
            mealCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    mealsPerDayCost += parseFloat(checkbox.getAttribute('data-price'));
                }
            });
            
            const totalMealsCost = mealsPerDayCost * (nights > 0 ? nights : 1); 

            const subtotal = roomTotal + totalMealsCost;
            const gst = subtotal * 0.12; 
            const grandTotal = subtotal + gst;

            document.getElementById('summary_nights').innerText = nights;
            document.getElementById('summary_room_total').innerText = roomTotal.toLocaleString('en-IN');
            document.getElementById('summary_meals_total').innerText = totalMealsCost.toLocaleString('en-IN');
            document.getElementById('summary_gst').innerText = gst.toLocaleString('en-IN', {maximumFractionDigits: 2});
            document.getElementById('summary_grand_total').innerText = grandTotal.toLocaleString('en-IN', {maximumFractionDigits: 2});
        }

        checkInInput.addEventListener('change', calculateBill);
        checkOutInput.addEventListener('change', calculateBill);
        mealCheckboxes.forEach(checkbox => checkbox.addEventListener('change', calculateBill));

        // --- Feature 2: Strictly match Name and File Name ---
        bookingForm.addEventListener('submit', function(e) {
            // Check declaration checkbox
            if (!declarationCheckbox.checked) {
                e.preventDefault();
                alert("Please confirm that your Name matches your ID Proof by ticking the checkbox!");
                return false;
            }

            // Check Indian Phone Number digits length
            const rawPhone = phoneInput.value.replace(/\s+/g, ''); // "+91XXXXXXXXXX"
            if (rawPhone.length !== 13) {
                e.preventDefault();
                alert("Please enter a valid 10-digit Indian phone number after +91.");
                return false;
            }

            // Validate File Name against Input Name
           /* if (idProofInput.files.length > 0) {
                const fullFileName = idProofInput.files[0].name; // e.g., "Basit Amin.png"
                const fileNameWithoutExt = fullFileName.substring(0, fullFileName.lastIndexOf('.')).trim().toLowerCase();
                const enteredName = customerNameInput.value.trim().toLowerCase();

                if (fileNameWithoutExt !== enteredName) {
                    e.preventDefault(); // Payment ya process par aage badhne se rokega
                    alert("Error: The uploaded ID Proof file name does not match the Customer Name. Please rename the file to your exact name and upload again!");
                    return false;
                }
            }*/
        });
    </script>

</body>
</html>