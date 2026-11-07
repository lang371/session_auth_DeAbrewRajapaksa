<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($email) && !empty($password)) {
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !strpos($email, '.')) {
            $error = "Please enter a valid email address with a proper extension (e.g., user@domain.com).";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
                $stmt->execute(['username' => $username, 'email' => $email]);
                
                if ($stmt->fetch()) {
                    $error = "Username or Email is already taken.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    $insert_stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
                    $insert_stmt->execute([
                        'username' => $username,
                        'email' => $email,
                        'password_hash' => $hashed_password
                    ]);

                    $success = "Registration successful! You can now log in.";
                }
            } catch (PDOException $e) {
                $error = "System error: " . $e->getMessage();
            }
        }
    } else {
        $error = "Please fill out all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - AuthSystem</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-card {
            border: none;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>

<div class="card register-card shadow-sm p-4 bg-white">
    <div class="text-center mb-4">
        <h3 class="fw-bold mt-2 text-dark">Create Account</h3>
        <p class="text-muted small">Register to start your secure web session</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small" role="alert">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success py-2 small" role="alert">
            <strong>Success:</strong> <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="mb-3">
            <label Lothar for="username" class="form-label text-muted small fw-bold">Username</label>
            <input type="text" class="form-control" id="username" name="username" required autocomplete="off">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label text-muted small fw-bold">Email Address</label>
            <input type="text" class="form-control" id="email" name="email" required autocomplete="off">
        </div>

        <div class="mb-4">
            <label for="password" class="form-label text-muted small fw-bold">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-success w-100 py-2 fw-bold mb-3">
            Register Account
        </button>
        
        <div class="text-center">
            <p class="small text-muted mb-0">Already have an account? <a href="index.php" class="text-decoration-none fw-bold">Log in here</a></p>
        </div>
    </form>
</div>

</body>
</html>
