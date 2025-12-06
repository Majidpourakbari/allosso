<?php require_once 'controlls/db/functions.php' ?>

<?php if(isset($my_profile_id)){header('location:dashboard');}?>

<!DOCTYPE html>
<html lang="fa">
<head>
  <title><?php echo $setting_site_name ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
  <link rel="stylesheet" href="views/assets/style.css">
  <script src="views/assets/script.js"></script>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body>



<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ajax-unobtrusive/3.2.6/jquery.unobtrusive-ajax.min.js"></script>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(-45deg, #1a237e, #0d47a1, #01579b, #0277bd);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(4px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: transform 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
        }

        .login-title {
            color: #1a237e;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
            font-size: 28px;
        }

        .login-title i {
            margin-right: 10px;
            color: #0d47a1;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 12px 12px 45px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.15);
            border-color: #1a237e;
        }

        .form-label {
            color: #424242;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .btn-login {
            background: linear-gradient(45deg, #1a237e, #0d47a1);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: linear-gradient(45deg, #0d47a1, #1a237e);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 71, 161, 0.3);
        }

        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            display: none;
        }

        .form-floating {
            margin-bottom: 20px;
            position: relative;
        }

        .form-floating > .form-control {
            height: calc(3.5rem + 2px);
            padding: 1rem 1rem 1rem 3rem;
        }

        .form-floating > label {
            padding: 1rem 1rem 1rem 3rem;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            z-index: 10;
        }

        .input-group-text {
            background: transparent;
            border: 1px solid #e0e0e0;
            border-right: none;
        }

        .password-toggle {
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }

        .password-toggle:hover {
            color: #1a237e;
        }

        .social-login {
            margin-top: 20px;
            text-align: center;
        }

        .social-login p {
            color: #666;
            margin-bottom: 15px;
            position: relative;
        }

        .social-login p::before,
        .social-login p::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background: #e0e0e0;
        }

        .social-login p::before {
            left: 0;
        }

        .social-login p::after {
            right: 0;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-icons a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            transform: translateY(-3px);
        }

        .google {
            background: #DB4437;
        }

        .facebook {
            background: #4267B2;
        }

        .twitter {
            background: #1DA1F2;
        }
    </style>