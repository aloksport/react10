<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Stock Scanner</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .navbar-custom {
      background: linear-gradient(90deg, #005c97, #363795); /* Finance theme */
    }
    .ad-box {
      background: #f1f1f1;
      border: 1px dashed #ccc;
      height: 250px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      color: #555;
    }
    .ad-mobile {
      height: 100px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">StockScanner</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMenu">
      <ul class="navbar-nav me-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Market Tools</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Top Gainers</a></li>
            <li><a class="dropdown-item" href="#">Top Losers</a></li>
            <li><a class="dropdown-item" href="#">Volume Buzzers</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="#">Screener</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Alerts</a></li>
      </ul>

      <!-- User Dropdown -->
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"><i class="bi bi-person-circle"></i> User</a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Mobile Top Ad -->
<div class="container-fluid mt-3 d-md-none">
  <div class="ad-box ad-mobile text-center rounded">Ad - Mobile Top</div>
</div>

<!-- Main Content Layout -->
<div class="container-fluid mt-3">
  <div class="row">

    <!-- Left Ad (Desktop Only) -->
    <div class="col-md-2 d-none d-md-block">
      <div class="ad-box text-center rounded">Ad - Left Sidebar</div>
    </div>

    <!-- Main Content -->
    <div class="col-12 col-md-8 mb-3">
      <div class="bg-white p-4 border rounded shadow-sm">
        <h2 class="mb-3">Live Stock Screener</h2>
        <p>This area is reserved for your market content, scanners, analysis, and more.</p>
        <hr>
        <h5>Sample Stock Scan Result</h5>
        <p>NIFTY50 | RSI < 30 | Last Close: ₹1785 | Trend: Oversold</p>
      </div>
    </div>

    <!-- Right Ad (Desktop Only) -->
    <div class="col-md-2 d-none d-md-block">
      <div class="ad-box text-center rounded">Ad - Right Sidebar</div>
    </div>

  </div>
</div>

<!-- Mobile Bottom Ad -->
<div class="container-fluid mt-3 d-md-none mb-4">
  <div class="ad-box ad-mobile text-center rounded">Ad - Mobile Bottom</div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
