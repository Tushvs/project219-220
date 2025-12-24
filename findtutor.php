<?php
session_start(); // Start the session at the very beginning
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find a Tutor | TutorSphere</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Base Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #98bdf4;
            color: #333;
            line-height: 1.6;
        }
        
        /* Navigation */
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
            text-decoration: none;
        }
        
        .logo i {
            color: #3498db;
            margin-right: 10px;
            font-size: 1.8rem;
        }
        
        .logo span {
            color: #3498db;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
        }
        
        .nav-links li {
            margin-left: 25px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #7f8c8d;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: #3498db;
        }
        
        .nav-links a.active {
            color: #3498db;
            font-weight: 600;
        }
        
        .nav-buttons {
            display: flex;
            align-items: center;
        }
        
        .nav-buttons a {
            text-decoration: none;
            margin-left: 15px;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: 500;
        }
        
        .nav-buttons a.login {
            color: #7f8c8d;
        }
        
        .nav-buttons a.signup {
            background-color: #3498db;
            color: white;
        }
        
        .nav-buttons a.signup:hover {
            background-color: #2980b9;
        }
        
        .cart-btn {
            position: relative;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            color: #7f8c8d;
            margin-right: 15px;
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
            display: none;
        }
        
        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .search-header {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .search-header h1 {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .search-header p {
            color: #7f8c8d;
            margin-bottom: 20px;
        }
        
        .search-bar {
            position: relative;
            margin-bottom: 20px;
        }
        
        .search-bar i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
        }
        
        .search-bar input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .search-bar input:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .search-bar button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            cursor: pointer;
        }
        
        .search-bar button:hover {
            background-color: #2980b9;
        }
        
        .filter-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .filter-tags span {
            font-size: 0.9rem;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .filter-tag {
            padding: 5px 15px;
            background-color: #ecf0f1;
            border-radius: 20px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .filter-tag:hover {
            background-color: #d6eaf8;
            color: #3498db;
        }
        
        .filter-tag.active {
            background-color: #3498db;
            color: white;
        }
        
        /* Main Layout */
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        @media (min-width: 992px) {
            .main-content {
                flex-direction: row;
            }
        }
        
        /* Filters Sidebar */
        .filters-sidebar {
            width: 100%;
        }
        
        @media (min-width: 992px) {
            .filters-sidebar {
                width: 25%;
                position: sticky;
                top: 20px;
                align-self: flex-start;
            }
        }
        
        .filters-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .filters-card h3 {
            font-size: 1.2rem;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .filter-group {
            margin-bottom: 20px;
        }
        
        .filter-group h4 {
            font-size: 0.95rem;
            margin-bottom: 10px;
            color: #34495e;
        }
        
        .filter-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        
        .filter-option {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .filter-option input {
            margin-right: 8px;
        }
        
        .filter-option label {
            font-size: 0.9rem;
            color: #34495e;
        }
        
        .price-range {
            width: 100%;
            margin: 15px 0;
        }
        
        .price-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        
        .price-display {
            text-align: center;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        .apply-btn {
            width: 100%;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .apply-btn:hover {
            background-color: #2980b9;
        }
        
        /* Tutor Listings */
        .tutor-listings {
            width: 100%;
        }
        
        @media (min-width: 992px) {
            .tutor-listings {
                width: 75%;
            }
        }
        
        .sort-options {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 15px 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .results-count {
            color: #7f8c8d;
            font-size: 0.95rem;
        }
        
        .sort-by {
            display: flex;
            align-items: center;
        }
        
        .sort-by span {
            font-size: 0.95rem;
            color: #7f8c8d;
            margin-right: 10px;
        }
        
        .sort-by select {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        
        /* Tutor Cards */
        .tutor-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        .tutor-card:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            border-left-color: #3498db;
        }
        
        .tutor-card-content {
            display: flex;
            flex-direction: column;
        }
        
        @media (min-width: 768px) {
            .tutor-card-content {
                flex-direction: row;
            }
        }
        
        .tutor-photo {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        @media (min-width: 768px) {
            .tutor-photo {
                width: 25%;
                justify-content: flex-start;
                margin-bottom: 0;
            }
        }
        
        .tutor-photo img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        @media (min-width: 768px) {
            .tutor-photo img {
                width: 120px;
                height: 120px;
            }
        }
        
        .tutor-photo .status {
            position: absolute;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
            bottom: 10px;
            right: 10px;
        }
        
        .tutor-photo .status.available {
            background-color: #2ecc71;
        }
        
        .tutor-photo .status.busy {
            background-color: #f39c12;
        }
        
        .tutor-photo .status.offline {
            background-color: #95a5a6;
        }
        
        .tutor-info {
            width: 100%;
            padding-left: 0;
        }
        
        @media (min-width: 768px) {
            .tutor-info {
                width: 75%;
                padding-left: 25px;
            }
        }
        
        .tutor-header {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }
        
        @media (min-width: 768px) {
            .tutor-header {
                flex-direction: row;
                justify-content: space-between;
            }
        }
        
        .tutor-name h3 {
            font-size: 1.3rem;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .tutor-name p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        .tutor-price {
            background-color: #3498db;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 10px;
            align-self: flex-start;
        }
        
        @media (min-width: 768px) {
            .tutor-price {
                margin-bottom: 0;
            }
        }
        
        .tutor-bio {
            color: #34495e;
            margin-bottom: 15px;
        }
        
        .tutor-subjects {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .subject-tag {
            background-color: #e8f4fc;
            color: #2980b9;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .tutor-footer {
            display: flex;
            flex-direction: column;
        }
        
        @media (min-width: 768px) {
            .tutor-footer {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }
        
        .tutor-location {
            display: flex;
            align-items: center;
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        @media (min-width: 768px) {
            .tutor-location {
                margin-bottom: 0;
            }
        }
        
        .tutor-location i {
            margin-right: 5px;
        }
        
        .tutor-actions {
            display: flex;
            gap: 10px;
        }
        
        .tutor-actions button {
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .profile-btn {
            background-color: #3498db;
            color: white;
            border: none;
        }
        
        .profile-btn:hover {
            background-color: #2980b9;
        }
        
        .message-btn {
            background-color: white;
            color: #3498db;
            border: 1px solid #3498db;
        }
        
        .message-btn:hover {
            background-color: #f5f9fd;
        }
        
        .add-to-cart-btn {
            background-color: #27ae60;
            color: white;
            border: none;
        }
        
        .add-to-cart-btn:hover {
            background-color: #219653;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        
        .pagination a {
            padding: 8px 12px;
            margin: 0 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            color: #3498db;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .pagination a:hover {
            background-color: #f5f5f5;
        }
        
        .pagination a.active {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
        }
        
        /* Shopping Cart Sidebar */
        .cart-sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: 100%;
            max-width: 400px;
            height: 100%;
            background-color: white;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
        }
        
        .cart-sidebar.open {
            transform: translateX(0);
        }
        
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .cart-header h2 {
            font-size: 1.5rem;
            color: #2c3e50;
        }
        
        .close-cart {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #7f8c8d;
            cursor: pointer;
        }
        
        .cart-items {
            padding: 20px;
        }
        
        .empty-cart {
            text-align: center;
            padding: 40px 0;
            color: #7f8c8d;
        }
        
        .empty-cart i {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #bdc3c7;
        }
        
        .cart-item {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .cart-item-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
        }
        
        .cart-item-details {
            flex-grow: 1;
        }
        
        .cart-item-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .cart-item-price {
            color: #3498db;
            font-weight: 500;
        }
        
        .remove-item {
            background: none;
            border: none;
            color: #e74c3c;
            cursor: pointer;
            align-self: flex-start;
        }
        
        .cart-summary {
            padding: 20px;
            border-top: 1px solid #eee;
            display: none;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .summary-total {
            font-weight: 600;
            font-size: 1.1rem;
            margin: 15px 0;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .form-group select, 
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .checkout-btn {
            width: 100%;
            padding: 12px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .checkout-btn:hover {
            background-color: #219653;
        }
        
        /* Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }
        
        .overlay.active {
            display: block;
        }
        
        /* Checkout Button */
        .checkout-btn {
            background-color: #4f46e5;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            transition: all 0.3s;
        }
        
        .checkout-btn:hover {
            background-color: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        
        .checkout-container {
            text-align: right;
            margin-top: 30px;
        }

        /* Payment Section Styles */
        .payment-section {
            display: none;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding: 25px;
            margin-top: 30px;
        }

        .payment-header {
            margin-bottom: 20px;
        }

        .payment-header h2 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .payment-summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .payment-summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .payment-summary-total {
            font-weight: 600;
            font-size: 1.1rem;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }

        .payment-methods {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .payment-method {
            flex: 1;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-method:hover {
            border-color: #3498db;
        }

        .payment-method.selected {
            border-color: #3498db;
            background-color: #e8f4fc;
        }

        .payment-method i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #3498db;
        }

        .payment-method-content {
            display: none;
        }

        .payment-method-content.active {
            display: block;
        }

        .qr-code-container {
            text-align: center;
            margin: 20px 0;
        }

        .qr-code {
            width: 200px;
            height: 200px;
            margin: 0 auto;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: white;
        }

        .qr-code img {
            width: 100%;
            height: 100%;
        }

        .card-payment-form {
            max-width: 400px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .utr-input {
            margin: 25px 0;
        }

        .submit-payment {
            width: 100%;
            padding: 12px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .submit-payment:hover {
            background-color: #219653;
        }

        /* Payment Modal */
        .payment-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .payment-modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 400px;
            width: 90%;
            text-align: center;
        }

        .payment-modal-content i {
            font-size: 3rem;
            color: #3498db;
            margin-bottom: 20px;
        }

        .payment-modal-content h3 {
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .payment-modal-content p {
            margin-bottom: 20px;
            color: #7f8c8d;
        }

        .login-redirect-btn {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-redirect-btn:hover {
            background-color: #2980b9;
        }
        .user-email {
            color: #4f46e5;
            font-weight: 500;
            margin-right: 15px;
            font-size: 0.9rem;
        }

        .logout {
            background-color: #f43f5e;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .logout:hover {
            background-color: #e11d48;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <a href="index.html" class="logo">
            <i class="fas fa-graduation-cap"></i>
            Tutor<span>Sphere</span>
        </a>
        
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="findtutor.php" class="active">Find Tutor</a></li>
            <li><a href="login.php" class="active">Your Dashboard</a></li>
        </ul>
        
        <div class="nav-buttons">
        <button class="cart-btn">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count">0</span>
            </button>
            <?php if(isset($_SESSION['user_email'])): ?>
                <span class="user-email"><?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
                <a href="process.php?action=logout" class="logout">Logout</a>
            <?php else: ?>
                <a href="mainlogin.php" class="login">Login</a>
                <a href="mainlogin.php" class="signup">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container">
        <div class="search-header">
            <h1>Find Your Perfect Tutor</h1>
            <p>Browse thousands of qualified tutors in 50+ subjects</p>
            
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="search-input" placeholder="Search by subject, tutor name, or keyword">
                <button id="search-button">Search</button>
            </div>
            
            <div class="filter-tags">
                <span>Popular:</span>
                <span class="filter-tag" data-subject="Math">Math</span>
                <span class="filter-tag" data-subject="Science">Physics</span>
                <span class="filter-tag" data-subject="Science">chemistry</span>
                <span class="filter-tag" data-subject="English">English</span>
                <span class="filter-tag" data-subject="Programming">Computer Science</span>
                <span class="filter-tag" data-subject="Test Prep">Test Prep</span>
                <span class="filter-tag" data-subject="Music">Music</span>
            </div>
        </div>
        
        <div class="main-content">
            <!-- Filters Sidebar -->
            <div class="filters-sidebar">
                <div class="filters-card">
                    <h3>Filter Tutors</h3>
                    
                    <div class="filter-group">
                        <h4>Subjects</h4>
                        <select id="subject-filter">
                            <option value="">All Subjects</option>
                            <option>Mathematics</option>
                            <option>Physics</option>
                            <option>Chemistry</option>
                            <option>English</option>
                            <option>Computer Science</option>
                            <option>Test Preparation</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <h4>Availability</h4>
                        <div class="filter-option">
                            <input type="checkbox" id="available-now" checked>
                            <label for="available-now">Available Now</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="weekdays" checked>
                            <label for="weekdays">Weekdays</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="weekends" checked>
                            <label for="weekends">Weekends</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="evenings" checked>
                            <label for="evenings">Evenings</label>
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <h4>Price Range</h4>
                        <div class="price-labels">
                            <span>₹100</span>
                            <span>₹1000+</span>
                        </div>
                        <input type="range" class="price-range" id="price-range" min="100" max="1000" value="500">
                        <div class="price-display">Up to ₹<span id="price-value">500</span>/hr</div>
                    </div>
                    
                    <div class="filter-group">
                        <h4>Tutor Type</h4>
                        <div class="filter-option">
                            <input type="checkbox" id="professional" checked class="tutor-type" value="Professional">
                            <label for="professional">Professional Tutors</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="university" checked class="tutor-type" value="University">
                            <label for="university">University Students</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="highschool" checked class="tutor-type" value="High School">
                            <label for="highschool">High School Tutors</label>
                        </div>
                        <div class="filter-option">
                            <input type="checkbox" id="experts" checked class="tutor-type" value="Expert">
                            <label for="experts">Industry Experts</label>
                        </div>
                    </div>
                    
                    <button class="apply-btn" id="apply-filters">Apply Filters</button>
                </div>
            </div>
            
            <!-- Tutor Listings -->
            <div class="tutor-listings">
                <div class="sort-options">
                    <div class="results-count">
                        <span class="count" id="results-count">142</span> tutors match your search
                    </div>
                    <div class="sort-by">
                        <span>Sort by:</span>
                        <select id="sort-by">
                            <option value="recommended">Recommended</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                        </select>
                    </div>
                </div>
                
                <div id="tutor-listings-container">
                    <!-- Tutor cards will be dynamically inserted here -->
                </div>
                
                <!-- Pagination -->
                <div class="pagination">
                    <a href="#"><i class="fas fa-chevron-left"></i></a>
                    <a href="#" class="active">1</a>
                    <a href="#">2</a>
                    <a href="#">3</a>
                    <a href="#"><i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Shopping Cart Sidebar -->
    <div class="cart-sidebar">
        <div class="cart-header">
            <h2>Your Tutor Cart</h2>
            <button class="close-cart">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="cart-items">
            <div class="empty-cart" id="empty-cart-message">
                <i class="fas fa-shopping-cart"></i>
                <p>Your cart is empty</p>
            </div>
            
            <!-- Cart items will be added here dynamically -->
        </div>
        
        <div class="cart-summary" id="cart-summary">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span id="cart-subtotal">₹0.00</span>
            </div>
            <div class="summary-row">
                <span>Service Fee:</span>
                <span id="cart-fee">₹0.00</span>
            </div>
            <div class="summary-row summary-total">
                <span>Total:</span>
                <span id="cart-total">₹0.00</span>
            </div>
            
            <div class="form-group">
                <label for="subject-selection">Select Subject:</label>
                <select id="subject-selection">
                    <option value="">Select a subject</option>
                    <option value="Mathematics">Mathematics</option>
                    <option value="Science">physics</option>
                    <option value="Science">Chemistry</option>
                    <option value="English">English</option>
                    <option value="Programming">Computer Science</option>
                    <option value="Test Preparation">Test Preparation</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="schedule-input">Preferred Schedule:</label>
                <input type="datetime-local" id="schedule-input">
            </div>
            <div class="checkout-container">
                <button class="checkout-btn" id="proceed-to-payment">Proceed to Payment</button>
            </div>
        </div>
    </div>

    <!-- Payment Section -->
    <div class="payment-section" id="payment-section">
        <div class="payment-header">
            <h2>Complete Your Payment</h2>
            <p>Choose your preferred payment method</p>
        </div>

        <div class="payment-summary">
            <div class="payment-summary-row">
                <span>Subtotal:</span>
                <span id="payment-subtotal">₹0.00</span>
            </div>
            <div class="payment-summary-row">
                <span>Service Fee (10%):</span>
                <span id="payment-fee">₹0.00</span>
            </div>
            <div class="payment-summary-row payment-summary-total">
                <span>Total Amount:</span>
                <span id="payment-total">₹0.00</span>
            </div>
        </div>

        <div class="payment-methods">
            <div class="payment-method selected" data-method="qr">
                <i class="fas fa-qrcode"></i>
                <h3>QR Code</h3>
                <p>Scan to pay instantly</p>
            </div>
            <div class="payment-method" data-method="card">
                <i class="far fa-credit-card"></i>
                <h3>Credit/Debit Card</h3>
                <p>Secure card payment</p>
            </div>
        </div>

        <div id="qr-payment" class="payment-method-content active">
            <div class="qr-code-container">
                <div class="qr-code">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=TutorSpherePayment-REF-12345" alt="Payment QR Code">
                </div>
                <p>Scan this QR code using your mobile banking app</p>
            </div>
        </div>

        <div id="card-payment" class="payment-method-content">
            <div class="card-payment-form">
                <div class="form-group">
                    <label for="card-number">Card Number</label>
                    <input type="text" id="card-number" placeholder="1234 5678 9012 3456">
                </div>
                <div class="form-group">
                    <label for="card-name">Name on Card</label>
                    <input type="text" id="card-name" placeholder="John Doe">
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="card-expiry">Expiry Date</label>
                        <input type="text" id="card-expiry" placeholder="MM/YY">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="card-cvv">CVV</label>
                        <input type="text" id="card-cvv" placeholder="123">
                    </div>
                </div>
            </div>
        </div>

        <div class="utr-input">
            <div class="form-group">
                <label for="utr-number">UTR Number (Transaction Reference)</label>
                <input type="text" id="utr-number" placeholder="Enter your UTR number after payment">
            </div>
        </div>

        <button class="submit-payment" id="submit-payment">Submit Payment</button>
    </div>

    <!-- Payment Modal -->
    <div class="payment-modal" id="payment-modal">
        <div class="payment-modal-content">
            <i class="fas fa-clock"></i>
            <h3>Payment Verification</h3>
            <p>Please wait for 10 minutes while we verify your payment.</p>
            <p>You will receive a confirmation email once verified.</p>
            <button class="login-redirect-btn" id="login-redirect">Go to Login Page</button>
        </div>
    </div>
    
    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>
    
    <script>
        // Tutor Data
        const tutors = [
            {
                id: "101",
                name: "Sachin Jakhar",
                title: "Mathematics Professor",
                price: 650,
                image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRuCZ0T1RtUk-MP3wHkh_XOrwCXiOgZ4oiyog&s",
                status: "available",
                bio: "B.Tech in Electrical Engineering from NIT Kurukshetra. Authored 'Calculus Core Fear No More' and is celebrated for his unique teaching style that simplifies complex mathematical concepts for IIT-JEE aspirants.",
                subjects: ["Calculus", "Algebra", "SAT Math", "ACT Math", "GRE Quant"],
                location: "Kolkata, India • Online",
                type: "Professional"
            },
            {
                id: "102",
                name: "Alakh Pandey (Physics Wallah)",
                title: "Physics",
                price: 350,
                image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRRovpnuZmmm_lmgiWJ7iosUeZQoJFcrnbSCQ&s",
                status: "busy",
                bio: "Dropped out of engineering; full-time educator. Millions of students across India learn through his engaging and affordable online platform.",
                subjects: ["Physics", "JEE Preparation", "NEET Preparation"],
                location: "A-13/5, Sector 62, Noida, Uttar Pradesh, India • Online",
                type: "Professional"
            },
            {
                id: "103",
                name: "Shradha Khapra (Apna College)",
                title: "Computer Science Tutor",
                price: 450,
                image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS-zobaI4sT6-ubKpQ9v4esDE0vlSW4WARVqg&s",
                status: "available",
                bio: "Ex-Microsoft intern, engineering background. Empowers students (especially from Tier 2/3 cities) to build coding skills and confidence. Co-founder of Apna College, focusing on college-level CS and coding for beginners.",
                subjects: ["Python", "JavaScript", "Web Development", "Data Structures", "Algorithms"],
                location: "Delhi, India • Online",
                type: "Expert"
            },
            {
                id: "104",
                name: "Varun Singla (Gate Smashers)",
                title: "Computer Science Tutor",
                price: 350,
                image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRVASGPMe3KSB_lWBw8q9GQhAwuW2Wtwi6byw&s",
                status: "busy",
                bio: "M.Tech in Computer Science. Teaching Computer Science subjects for GATE/UGC-NET/college exams on YouTube. Offers clear, concise, and free content for foundational subjects like DBMS, OS, TOC, and more.",
                subjects: ["DBMS", "Operating Systems", "Theory of Computation", "Computer Networks"],
                location: "India • Online",
                type: "University"
            },
            {
                id: "105",
                name: "Dr. Ravi Teja",
                title: "Chemistry Professor",
                price: 550,
                image: "https://randomuser.me/api/portraits/men/45.jpg",
                status: "available",
                bio: "PhD in Chemistry with 10+ years of teaching experience. Specializes in organic chemistry and helps students understand complex reactions with simple analogies.",
                subjects: ["Organic Chemistry", "Inorganic Chemistry", "Physical Chemistry", "NEET Chemistry"],
                location: "Bangalore, India • Online",
                type: "Professional"
            },
            {
                id: "106",
                name: "Priya Sharma",
                title: "English Language Coach",
                price: 300,
                image: "https://randomuser.me/api/portraits/women/65.jpg",
                status: "available",
                bio: "Cambridge-certified English teacher with 8 years of experience helping students improve their communication skills for academic and professional success.",
                subjects: ["ESL", "TOEFL Prep", "Writing", "Conversation", "Business English"],
                location: "Mumbai, India • Online",
                type: "Professional"
            }
        ];

        // Shopping Cart Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const cart = [];
            const cartSidebar = document.querySelector('.cart-sidebar');
            const overlay = document.getElementById('overlay');
            const cartButton = document.querySelector('.cart-btn');
            const closeCartButton = document.querySelector('.close-cart');
            const cartItemsContainer = document.querySelector('.cart-items');
            const emptyCartMessage = document.getElementById('empty-cart-message');
            const cartSummary = document.getElementById('cart-summary');
            const cartCount = document.querySelector('.cart-count');
            const proceedToPaymentBtn = document.getElementById('proceed-to-payment');
            const paymentSection = document.getElementById('payment-section');
            const submitPaymentBtn = document.getElementById('submit-payment');
            const paymentModal = document.getElementById('payment-modal');
            const loginRedirectBtn = document.getElementById('login-redirect');
            const tutorListingsContainer = document.getElementById('tutor-listings-container');
            const applyFiltersBtn = document.getElementById('apply-filters');
            const priceRange = document.getElementById('price-range');
            const priceValue = document.getElementById('price-value');
            const searchInput = document.getElementById('search-input');
            const searchButton = document.getElementById('search-button');
            const sortBy = document.getElementById('sort-by');
            const resultsCount = document.getElementById('results-count');
            const filterTags = document.querySelectorAll('.filter-tag');
            const subjectFilter = document.getElementById('subject-filter');
            
            // Initialize with all tutors
            displayTutors(tutors);
            
            // Price range display
            priceRange.addEventListener('input', function() {
                priceValue.textContent = this.value;
            });
            
            // Filter tags
            filterTags.forEach(tag => {
                tag.addEventListener('click', function() {
                    filterTags.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    searchInput.value = this.getAttribute('data-subject');
                    filterAndSortTutors();
                });
            });
            
            // Search functionality
            searchButton.addEventListener('click', filterAndSortTutors);
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    filterAndSortTutors();
                }
            });
            
            // Sort functionality
            sortBy.addEventListener('change', filterAndSortTutors);
            
            // Apply filters
            applyFiltersBtn.addEventListener('click', filterAndSortTutors);
            
            // Filter and sort tutors
            function filterAndSortTutors() {
                const searchTerm = searchInput.value.toLowerCase();
                const maxPrice = parseInt(priceRange.value);
                const subjectFilterValue = subjectFilter.value.toLowerCase();
                const availableNow = document.getElementById('available-now').checked;
                const selectedTypes = Array.from(document.querySelectorAll('.tutor-type:checked')).map(el => el.value);
                
                let filteredTutors = tutors.filter(tutor => {
                    // Search term filter
                    const matchesSearch = 
                        tutor.name.toLowerCase().includes(searchTerm) ||
                        tutor.title.toLowerCase().includes(searchTerm) ||
                        tutor.subjects.some(subj => subj.toLowerCase().includes(searchTerm)) ||
                        tutor.bio.toLowerCase().includes(searchTerm);
                    
                    // Price filter
                    const matchesPrice = tutor.price <= maxPrice;
                    
                    // Subject filter
                    const matchesSubject = subjectFilterValue === '' || 
                        tutor.subjects.some(subj => subj.toLowerCase().includes(subjectFilterValue));
                    
                    // Availability filter
                    const matchesAvailability = !availableNow || tutor.status === 'available';
                    
                    // Tutor type filter
                    const matchesType = selectedTypes.length === 0 || selectedTypes.includes(tutor.type);
                    
                    return matchesSearch && matchesPrice && matchesSubject && 
                           matchesAvailability && matchesType;
                });
                
                // Sort tutors
                const sortValue = sortBy.value;
                switch(sortValue) {
                    case 'price-low':
                        filteredTutors.sort((a, b) => a.price - b.price);
                        break;
                    case 'price-high':
                        filteredTutors.sort((a, b) => b.price - a.price);
                        break;
                    default: // recommended
                        filteredTutors.sort((a, b) => b.price - a.price); // Default sort by price high to low
                }
                
                // Update results count
                resultsCount.textContent = filteredTutors.length;
                
                // Display filtered tutors
                displayTutors(filteredTutors);
            }
            
            // Display tutors
            function displayTutors(tutorsToDisplay) {
                tutorListingsContainer.innerHTML = '';
                
                tutorsToDisplay.forEach(tutor => {
                    const tutorCard = document.createElement('div');
                    tutorCard.className = 'tutor-card';
                    tutorCard.innerHTML = `
                        <div class="tutor-card-content">
                            <div class="tutor-photo">
                                <img src="${tutor.image}" alt="${tutor.name}">
                                <div class="status ${tutor.status}"></div>
                            </div>
                            <div class="tutor-info">
                                <div class="tutor-header">
                                    <div class="tutor-name">
                                        <h3>${tutor.name}</h3>
                                        <p>${tutor.title}</p>
                                    </div>
                                    <div class="tutor-price">₹${tutor.price}/hr</div>
                                </div>
                                
                                <p class="tutor-bio">
                                    ${tutor.bio}
                                </p>
                                
                                <div class="tutor-subjects">
                                    ${tutor.subjects.map(subj => `<span class="subject-tag">${subj}</span>`).join('')}
                                </div>
                                
                                <div class="tutor-footer">
                                    <div class="tutor-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>${tutor.location}</span>
                                    </div>
                                    <div class="tutor-actions">
                                        <button class="profile-btn">View Profile</button>
                                        <button class="message-btn">Message</button>
                                        <button class="add-to-cart-btn" 
                                                data-id="${tutor.id}" 
                                                data-name="${tutor.name}" 
                                                data-price="${tutor.price}" 
                                                data-image="${tutor.image}">
                                            <i class="fas fa-cart-plus"></i> Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    tutorListingsContainer.appendChild(tutorCard);
                });
                
                // Reattach event listeners to new add to cart buttons
                document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const tutorId = this.getAttribute('data-id');
                        const tutorName = this.getAttribute('data-name');
                        const tutorPrice = parseFloat(this.getAttribute('data-price'));
                        const tutorImage = this.getAttribute('data-image');
                        
                        // Check if tutor is already in cart
                        const existingItem = cart.find(item => item.id === tutorId);
                        
                        if (existingItem) {
                            alert('This tutor is already in your cart');
                            return;
                        }
                        
                        // Add to cart
                        cart.push({
                            id: tutorId,
                            name: tutorName,
                            price: tutorPrice,
                            image: tutorImage
                        });
                        
                        updateCart();
                        
                        // Show cart sidebar
                        cartSidebar.classList.add('open');
                        overlay.classList.add('active');
                    });
                });
            }
            
            // Open/Close Cart
            cartButton.addEventListener('click', function() {
                cartSidebar.classList.add('open');
                overlay.classList.add('active');
            });
            
            closeCartButton.addEventListener('click', function() {
                cartSidebar.classList.remove('open');
                overlay.classList.remove('active');
            });
            
            overlay.addEventListener('click', function() {
                cartSidebar.classList.remove('open');
                overlay.classList.remove('active');
            });
            
            // Remove from Cart
            function setupRemoveButtons() {
                document.querySelectorAll('.remove-item').forEach(button => {
                    button.addEventListener('click', function() {
                        const itemId = this.getAttribute('data-id');
                        const itemIndex = cart.findIndex(item => item.id === itemId);
                        
                        if (itemIndex !== -1) {
                            cart.splice(itemIndex, 1);
                            updateCart();
                        }
                    });
                });
            }
            
            // Update Cart Display
            function updateCart() {
                // Update cart count
                cartCount.textContent = cart.length;
                if (cart.length > 0) {
                    cartCount.style.display = 'flex';
                } else {
                    cartCount.style.display = 'none';
                }
                
                // Update cart items
                if (cart.length === 0) {
                    emptyCartMessage.style.display = 'block';
                    cartSummary.style.display = 'none';
                    cartItemsContainer.innerHTML = '';
                } else {
                    emptyCartMessage.style.display = 'none';
                    cartSummary.style.display = 'block';
                    
                    // Clear existing items
                    cartItemsContainer.innerHTML = '';
                    
                    // Add new items
                    cart.forEach(item => {
                        const cartItem = document.createElement('div');
                        cartItem.className = 'cart-item';
                        cartItem.innerHTML = `
                            <img src="${item.image}" alt="${item.name}" class="cart-item-img">
                            <div class="cart-item-details">
                                <div class="cart-item-name">${item.name}</div>
                                <div class="cart-item-price">₹${item.price}/hr</div>
                            </div>
                            <button class="remove-item" data-id="${item.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                        cartItemsContainer.appendChild(cartItem);
                    });
                    
                    // Calculate totals
                    const subtotal = cart.reduce((sum, item) => sum + item.price, 0);
                    const fee = subtotal * 0.1; // 10% service fee
                    const total = subtotal + fee;
                    
                    document.getElementById('cart-subtotal').textContent = `₹${subtotal.toFixed(2)}`;
                    document.getElementById('cart-fee').textContent = `₹${fee.toFixed(2)}`;
                    document.getElementById('cart-total').textContent = `₹${total.toFixed(2)}`;
                    
                    // Setup remove buttons for new items
                    setupRemoveButtons();
                }
            }
            
            // Proceed to Payment
            proceedToPaymentBtn.addEventListener('click', function() {
                const subject = document.getElementById('subject-selection').value;
                const schedule = document.getElementById('schedule-input').value;
                
                if (cart.length === 0) {
                    alert('Your cart is empty');
                    return;
                }
                
                if (!subject) {
                    alert('Please select a subject');
                    return;
                }
                
                if (!schedule) {
                    alert('Please select a preferred schedule');
                    return;
                }
                
                // Calculate totals
                const subtotal = cart.reduce((sum, item) => sum + item.price, 0);
                const fee = subtotal * 0.1; // 10% service fee
                const total = subtotal + fee;
                
                // Update payment summary
                document.getElementById('payment-subtotal').textContent = `₹${subtotal.toFixed(2)}`;
                document.getElementById('payment-fee').textContent = `₹${fee.toFixed(2)}`;
                document.getElementById('payment-total').textContent = `₹${total.toFixed(2)}`;
                
                // Show payment section
                paymentSection.style.display = 'block';
                
                // Scroll to payment section
                paymentSection.scrollIntoView({ behavior: 'smooth' });
                
                // Close cart sidebar
                cartSidebar.classList.remove('open');
                overlay.classList.remove('active');
            });

            // Payment method selection
            document.querySelectorAll('.payment-method').forEach(method => {
                method.addEventListener('click', function() {
                    document.querySelectorAll('.payment-method').forEach(m => {
                        m.classList.remove('selected');
                    });
                    this.classList.add('selected');
                    
                    const methodType = this.getAttribute('data-method');
                    document.querySelectorAll('.payment-method-content').forEach(content => {
                        content.classList.remove('active');
                    });
                    document.getElementById(`${methodType}-payment`).classList.add('active');
                });
            });

            // Submit payment
            submitPaymentBtn.addEventListener('click', function() {
                const utrNumber = document.getElementById('utr-number').value;
                
                if (!utrNumber) {
                    alert('Please enter your UTR number');
                    return;
                }
                
                // Show payment verification modal
                paymentModal.style.display = 'flex';
            });

            // Login redirect
            loginRedirectBtn.addEventListener('click', function() {
                // In a real app, this would redirect to the login page
                alert('Redirecting to login page...');
                // window.location.href = 'login.html';
                
                // Close modal
                paymentModal.style.display = 'none';
                
                // Clear cart
                cart.length = 0;
                updateCart();
            });
            
            // Initialize cart
            updateCart();
        });
    </script>
</body>
</html>