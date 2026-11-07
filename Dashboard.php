<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'db.php';

try {
    $stmt = $pdo->prepare("SELECT username, email, created_at FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_destroy();
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error retrieving secure user metrics: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SystemAuthSystem - Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f6f8fa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .main-navbar {
            background-color: #0f172a;
            border-bottom: 1px solid #1e293b;
            padding: 0.75rem 1.5rem;
        }
        .nav-btn {
            background-color: #1e293b;
            color: #cbd5e1 !important;
            border: 1px solid #334155;
            padding: 0.45rem 1.2rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
            transition: all 0.2s ease;
        }
        .nav-btn:hover {
            background-color: #334155;
            color: #ffffff !important;
            border-color: #475569;
        }
        .logout-btn {
            background-color: #dc2626;
            color: #ffffff;
            border: none;
            padding: 0.45rem 1.2rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }
        .logout-btn:hover {
            background-color: #b91c1c;
            color: #ffffff;
        }
        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background-color: #ffffff;
        }
        .welcome-title {
            font-size: 3.2rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.025em;
            line-height: 1.1;
        }
        .btn-explore {
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            border-radius: 6px;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
            transition: background-color 0.2s ease;
        }
        .btn-explore:hover {
            background-color: #1d4ed8;
            color: #ffffff;
        }
        .pro-card {
            border: 1px solid #e2e8f0;
            border-left: 5px solid #cbd5e1;
            border-radius: 10px;
            background: #ffffff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .pro-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        }
        .pro-card.status-card { border-left-color: #10b981; }
        .pro-card.details-card { border-left-color: #3b82f6; }
        .pro-card.session-card { border-left-color: #f59e0b; }
        
        .metric-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.075em;
            color: #64748b;
            font-weight: 700;
        }
        .session-code {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-family: var(--bs-font-monospace);
            padding: 0.6rem;
            border-radius: 6px;
            font-size: 0.8rem;
            word-break: break-all;
        }
        .detail-item {
            border-bottom: 1px dashed #f1f5f9;
            padding-bottom: 0.4rem;
        }
        .detail-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
    </style>
</head>
<body>

    <nav class="navbar main-navbar shadow-sm">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2">
                <a href="#" class="nav-btn">SystemAuthSystem</a>
                <a href="#" class="nav-btn">Dashboard</a>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="#" class="nav-btn">Profile: <?php echo htmlspecialchars($user['username']); ?></a>
                <a href="logout.php" class="logout-btn">Log Out</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-xl-10">
                
                <div class="card card-custom shadow-sm p-5 mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-10 text-start">
                            <h1 class="welcome-title mb-2">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
                            <p class="text-secondary fs-5 mb-4">This page is protected and only visible to logged-in users.</p>
                            <a href="#" class="btn btn-explore shadow-sm">
                                Explore Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-12 col-md-4">
                        <div class="card pro-card status-card shadow-sm p-4 h-100">
                            <span class="metric-label d-block mb-3">Account Status</span>
                            <div class="d-flex align-items-center py-2">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold fs-6">
                                    Verified Active
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="card pro-card details-card shadow-sm p-4 h-100">
                            <span class="metric-label d-block mb-3">User Details</span>
                            <div class="small">
                                <div class="detail-item mb-2">
                                    <span class="text-secondary">Username:</span> 
                                    <span class="text-dark fw-bold float-end"><?php echo htmlspecialchars($user['username']); ?></span>
                                </div>
                                <div class="detail-item mb-2">
                                    <span class="text-secondary">Email:</span> 
                                    <span class="text-dark fw-bold float-end"><?php echo htmlspecialchars($user['email']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="text-secondary">Joined:</span> 
                                    <span class="text-dark fw-bold float-end"><?php echo date("M d, Y", strtotime($user['created_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="card pro-card session-card shadow-sm p-4 h-100">
                            <span class="metric-label d-block mb-3">Active Session ID</span>
                            <div class="session-code text-center mt-1">
                                <?php echo session_id(); ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
