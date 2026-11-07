<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password string details.";
            }
        } catch (PDOException $e) {
            $error = "System connectivity failure: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all empty fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Secure AuthSystem</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>

<div class="card login-card shadow-sm p-4 bg-white">
    <div class="text-center mb-4">
        <h3 class="fw-bold text-dark">ACCOUNT LOGIN</h3>
        <p class="text-muted small">Sign in to access</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small" role="alert">
            <strong>Login Failed:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="index.php" method="POST">
        <div class="mb-3">
            <label Lothar for="username" class="form-label text-muted small fw-bold">Username</label>
            <input type="text" class="form-control" id="username" name="username" required autocomplete="off">
        </div>

        <div class="mb-4">
            <label for="password" class="form-label text-muted small fw-bold">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mb-3">
            Log In
        </button>
        
        <div class="text-center">
            <p class="small text-muted mb-0">Don't have an account? <a href="register.php" class="text-decoration-none fw-bold">Register here</a></p>
        </div>
    </form>
</div>

</body>
</html>
