<?php
// Include database connection if needed in future
include 'config/db.php';

// Include the unified header
include 'header.php';
?>
    <style>
        /* --- Custom Styles for Rules & Policies Page --- */
        :root {
            --policy-bg: #ffffff;
            --icon-bg: #f1f3f5;
            --accent-red: #e74c3c;
        }

        [data-theme="dark"] {
            --policy-bg: #1e1e1e;
            --icon-bg: #2a2a2a;
            --accent-red: #ff6b6b;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .policy-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .policy-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .policy-header h1 {
            font-size: 2.5rem;
            color: var(--text-color);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .policy-header p {
            font-size: 1.1rem;
            opacity: 0.8;
        }

        /* Rules Card Layout */
        .policy-card {
            background-color: var(--policy-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            margin-bottom: 30px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .policy-section-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .policy-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .policy-list li {
            position: relative;
            padding-left: 30px;
            margin-bottom: 15px;
            font-size: 1.05rem;
            line-height: 1.6;
            opacity: 0.9;
        }

        /* Custom stylish bullet icons */
        .policy-list li::before {
            content: "✓";
            position: absolute;
            left: 0;
            top: 2px;
            width: 20px;
            height: 20px;
            background-color: var(--icon-bg);
            color: #2ed573;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
        }

        /* Strict/Strictly Prohibited Rules Styling */
        .policy-list li.prohibited::before {
            content: "✕";
            color: var(--accent-red);
            background-color: rgba(231, 76, 60, 0.1);
        }

        .policy-list li strong {
            color: var(--text-color);
        }

        .note-box {
            background-color: rgba(241, 196, 15, 0.1);
            border-left: 4px solid #f1c40f;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
            font-size: 0.95rem;
            font-style: italic;
        }
    </style>

<div class="policy-container">
    <div class="policy-header">
        <h1>House Rules & Policies</h1>
        <p>Please read our guidelines carefully to ensure a comfortable and safe stay for everyone.</p>
    </div>

    <div class="policy-card">
        <div class="policy-section-title" style="color: var(--accent-red);">
            🚫 Strictly Prohibited (सख्त मनाही है)
        </div>
        <ul class="policy-list">
            <li class="prohibited"><strong>No Alcohol & Smoking:</strong> Consumption of alcohol, smoking, or any kind of drug/narcotic substance is strictly prohibited inside the rooms and premises.</li>
            <li class="prohibited"><strong>Illegal Activities:</strong> Any form of illegal or anti-social activity will report immediately to the local authorities.</li>
            <li class="prohibited"><strong>No Loud Music:</strong> Playing loud music or causing disturbance to neighbors after 10:00 PM is not allowed.</li>
            <li class="prohibited"><strong>Unregistered Guests:</strong> Strangers or unregistered guests are not permitted to stay overnight without prior admin approval.</li>
        </ul>
    </div>

    <div class="policy-card">
        <div class="policy-section-title">
            🏡 General Guest Guidelines
        </div>
        <ul class="policy-list">
            <li><strong>Valid ID Proof:</strong> All guests must produce a valid government-approved ID card (Aadhaar, Passport, etc.) at the time of check-in.</li>
            <li><strong>Check-in & Check-out:</strong> Standard Check-in time is 12:00 PM and Check-out time is 11:00 AM.</li>
            <li><strong>Property Care:</strong> Please take care of the homestay property and electronics. Any intentional damage will be charged to the guest.</li>
            <li><strong>Save Energy:</strong> Kindly switch off the lights, fans, fridge, and water taps when not in use or while leaving the room.</li>
        </ul>
    </div>

    <div class="note-box">
        <strong>Important Note:</strong> Management reserves the right to ask any guest to leave the premises immediately if found violating any of the above strict policies. Thank you for your cooperation!
    </div>
</div>

<footer>
    <p>Contact Us: <a href="mailto:contact@alraahathomestay.com">contact@alraahathomestay.com</a></p>
    <p>© 2026 Al Raahat Homestay. All Rights Reserved.</p>
</footer>

<script src="js/main.js"></script>
</body>
</html>