<?php
// Include the database connection to fetch available rooms
include 'config/db.php';

// Query to get all available rooms from the database ordered by room number sequence
$room_query = "SELECT * FROM rooms WHERE status = 'Available' ORDER BY room_number ASC";
$room_result = $conn->query($room_query);

// Include the unified header
include 'header.php';
?>
    <style>
        /* --- Custom Styles for the Rooms Page --- */
        :root {
            --card-bg: #ffffff;
            --heading-color: #130101f1;
            --subtext-color: #666666;
        }

        [data-theme="dark"] {
            --card-bg: #1e1e1e;
            --heading-color: #f5f5f5;
            --subtext-color: #b0b0b0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--bg-color); 
            color: var(--text-color);
            padding-bottom: 60px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Top Title Navigation bar styling with centered titles */
        .rooms-header {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center; /* Ensures the H1 stays exactly in the middle */
            padding: 40px 20px;
            background-color: #2306c5;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 40px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
            color: #ffffff;
        }

        .rooms-header h1 {
            font-size: 2.5rem;
            color: #ffffff; /* Contrast check for header background */
            margin: 0;
            text-align: center;
            transition: color 0.3s ease;
        }

        /* Back to home only Arrow styling fixed to the far left */
        .back-home-container {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        .back-home-link {
            display: inline-block;
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.8rem;
            transition: color 0.2s ease;
        }
        
        .back-home-link:hover {
            color: #fcc419;
        }

        /* List layout container instead of standard grid */
        .rooms-container {
            max-width: 900px; /* Reduced width for a neat list look */
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        /* Individual Room Card Box with Flex Row layout */
        .room-card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: row; /* Aligns photo on left, info on right */
            gap: 25px;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
        }

        .room-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        /* Left Side Room Image Handling */
        .room-image-wrapper {
            flex: 0 0 250px; /* Exact width for room picture */
            height: 160px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .room-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Right Side Content Management */
        .room-content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 160px; /* Matches layout structure */
            position: relative;
        }

        .room-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .room-card h3 {
            font-size: 1.5rem;
            color: var(--heading-color);
            margin: 0 0 10px 0;
            transition: color 0.3s ease;
        }

        /* Status Badge */
        .status-badge {
            background-color: #fff4e6;
            color: #2306c5;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* Guests and Price text layout */
        .room-details {
            font-size: 0.95rem;
            color: var(--subtext-color);
            margin-bottom: 10px;
            transition: color 0.3s ease;
        }

        .room-details p {
            margin: 0 0 6px 0;
        }

        .room-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--heading-color);
            margin: 5px 0 0 0;
            transition: color 0.3s ease;
        }

        /* Action Footer within the card content block */
        .room-action-footer {
            display: flex;
            justify-content: flex-end;
        }

        /* Golden Yellow Book Now Button */
        .btn-book-now {
            display: inline-block;
            text-align: center;
            background-color: #3a05fa; 
            color: #ffffff;
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: background 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-book-now:hover {
            background-color: #fcc419;
            color: #130101;
        }
        
        .no-rooms-msg {
            text-align: center;
            font-size: 1.2rem;
            color: var(--subtext-color);
            padding: 40px 0;
            transition: color 0.3s ease;
        }

        /* Responsive Layout adjustments for small screens */
        @media (max-width: 650px) {
            .room-card {
                flex-direction: column;
                align-items: stretch;
            }
            .room-image-wrapper {
                flex: none;
                width: 100%;
                height: 180px;
            }
            .room-content-wrapper {
                height: auto;
                gap: 15px;
            }
        }
    </style>

    <header class="rooms-header">
        <div class="back-home-container">
            <a href="index.php" class="back-home-link" title="Back to Home">&larr;</a>
        </div>
        <h1>Available Rooms</h1>
    </header>
    
    <main class="rooms-container">
        <?php if ($room_result->num_rows > 0): ?>
            <?php while($room = $room_result->fetch_assoc()): ?>
                <div class="room-card">
                    
                    <div class="room-image-wrapper">
                        <?php 
                        // Agar database mein image dynamic ho toh woh aayegi, nahi toh default upload image use hogi
                        $image_name = !empty($room['image']) ? $room['image'] : 'image1.jpeg'; 
                        ?>
                        <img src="images/<?php echo $image_name; ?>" alt="Room Image">
                    </div>

                    <div class="room-content-wrapper">
                        <div>
                            <div class="room-card-header">
                                <h3>Room <?php echo $room['room_number']; ?></h3>
                                <span class="status-badge">Available</span>
                            </div>
                            <div class="room-details">
                                <p>👥 2-4 Guests max</p> 
                                <p class="room-price">₹<?php echo $room['rate']; ?> <span style="font-size: 0.9rem; font-weight: 400; color: var(--subtext-color);">/ night</span></p>
                            </div>
                        </div>

                        <div class="room-action-footer">
                            <a href="booking.php?room=<?php echo $room['room_number']; ?>" class="btn-book-now">Book Now</a>
                        </div>
                    </div>

                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-rooms-msg">
                <p>Sorry, no rooms are currently available at the moment.</p>
            </div>
        <?php endif; ?>
    </main>
    <script src="js/main.js"></script>
</body>
</html>