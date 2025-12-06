<?php require_once 'controlls/db/functions.php' ?>
<!DOCTYPE html>
<html lang="fa">
<head>
  <title><?php echo $setting_site_name ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
  <link rel="stylesheet" href="views/assets/style.css">
  <script src="views/assets/script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ajax-unobtrusive/3.2.6/jquery.unobtrusive-ajax.min.js"></script>
</head>
<body>




<?php if(!isset($my_profile_id)){header('location:index');}?>

<style>
    :root {
        --primary-dark: #232e3c;
        --light-bg: #f4f7fb;
        --accent-blue: #3b7cdd;
        --white: #fff;
    }

    body {
        background-color: var(--light-bg);
        font-family: Arial, Helvetica, sans-serif;
    }

    .dashboard-container {
        display: flex;
        min-height: 100vh;
        position: relative;
    }

    /* Sidebar Styles */
    .sidebar {
        width: 250px;
        background-color: var(--primary-dark);
        color: var(--white);
        padding: 20px 0;
        position: fixed;
        height: 100vh;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .sidebar-header {
        padding: 0 20px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .sidebar-header h3 {
        margin: 0;
        font-size: 1.5rem;
        color: var(--white);
    }

    .nav-menu {
        list-style: none;
        padding: 0;
        margin: 20px 0;
    }

    .nav-item {
        padding: 12px 20px;
        transition: all 0.3s ease;
    }

    .nav-item:hover {
        background-color: rgba(255,255,255,0.1);
    }

    .nav-link {
        color: var(--white);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .nav-link i {
        width: 20px;
    }

    /* Mobile Menu Toggle */
    .menu-toggle {
        display: none;
        position: fixed;
        top: 20px;
        left: 20px;
        z-index: 1001;
        background: var(--primary-dark);
        color: var(--white);
        border: none;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    /* Main Content Styles */
    .main-content {
        flex: 1;
        margin-left: 250px;
        padding: 20px;
        transition: all 0.3s ease;
    }

    /* Top Bar Styles */
    .top-bar {
        background-color: var(--white);
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .datetime-container {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .region-selector {
        position: relative;
    }

    .region-selector select {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: var(--white);
        cursor: pointer;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .search-box {
        position: relative;
    }

    .search-box input {
        padding: 8px 35px 8px 15px;
        border: 1px solid #ddd;
        border-radius: 20px;
        width: 200px;
        transition: all 0.3s ease;
    }

    .search-box input:focus {
        width: 250px;
        outline: none;
        border-color: var(--accent-blue);
    }

    .search-box i {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
    }

    .notification-icon, .messages-icon {
        position: relative;
        cursor: pointer;
    }

    .notification-icon i, .messages-icon i {
        font-size: 1.2rem;
        color: var(--primary-dark);
    }

    .notification-badge, .message-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: #ff4444;
        color: white;
        border-radius: 50%;
        min-width: 20px;
        height: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        line-height: 1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        border: 2px solid white;
    }

    .profile-dropdown {
        position: relative;
    }

    .profile-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        object-fit: cover;
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: var(--white);
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        min-width: 200px;
        display: none;
        z-index: 1000;
    }

    .dropdown-menu.active {
        display: block;
    }

    .dropdown-item {
        padding: 12px 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--primary-dark);
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .dropdown-item:hover {
        background-color: var(--light-bg);
    }

    .dropdown-item i {
        width: 20px;
        color: var(--accent-blue);
    }

    .dropdown-divider {
        height: 1px;
        background-color: #eee;
        margin: 5px 0;
    }

    /* Add toggle switch styles */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .toggle-slider {
        background-color: #4CAF50;
    }

    input:checked + .toggle-slider:before {
        transform: translateX(26px);
    }

    .status-toggle-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }

    .status-text {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Dashboard Cards */
    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .card {
        background-color: var(--white);
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .card-title {
        margin: 0;
        color: var(--primary-dark);
        font-size: 1.1rem;
    }

    .card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background-color: var(--accent-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
    }

    .card-value {
        font-size: 1.8rem;
        font-weight: bold;
        color: var(--primary-dark);
        margin: 10px 0;
    }

    .card-description {
        color: #666;
        font-size: 0.9rem;
    }

    /* Recent Activity Section */
    .recent-activity {
        background-color: var(--white);
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .activity-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .activity-item {
        padding: 15px 0;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--light-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent-blue);
    }

    .activity-details {
        flex: 1;
    }

    .activity-title {
        margin: 0;
        color: var(--primary-dark);
        font-size: 1rem;
    }

    .activity-time {
        color: #666;
        font-size: 0.9rem;
    }

    /* Responsive Styles */
    @media screen and (max-width: 768px) {
        .menu-toggle {
            display: block;
        }

        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
            padding: 60px 15px 20px;
        }

        .dashboard-cards {
            grid-template-columns: 1fr;
        }

        .top-bar {
            padding: 15px;
            flex-direction: column;
            gap: 15px;
        }

        .header-left {
            width: 100%;
        }

        .datetime-container {
            width: 100%;
            justify-content: center;
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
        }

        .header-right {
            width: 100%;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }

        .search-box {
            width: 100%;
            order: -1;
        }

        .search-box input {
            width: 100%;
        }

        .search-box input:focus {
            width: 100%;
        }

        .messages-icon, .notification-icon, .profile-dropdown {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .activity-item {
            flex-direction: column;
            text-align: center;
        }

        .activity-icon {
            margin: 0 auto;
        }
    }

    @media screen and (min-width: 769px) and (max-width: 1024px) {
        .dashboard-cards {
            grid-template-columns: repeat(2, 1fr);
        }

        .main-content {
            padding: 20px 15px;
        }
    }
</style>

