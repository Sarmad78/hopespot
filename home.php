<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Stats fetch karo
$total_missing = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM missing"))[0];
$total_users = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM user"))[0];
$today = date('Y-m-d');
$today_reports = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM missing WHERE DATE(created_at) = '$today'"))[0];

// Latest 4 missing persons
$latest = mysqli_query($conn, "SELECT name, fname, age, filename FROM missing ORDER BY id DESC LIMIT 4");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home - HopeSpot</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <script src="/FYP/js/script.js"></script>
</head>
<style>
  * { box-sizing: border-box; }
  body {
    background-color: #F1F7FE;
    padding: 0; margin: 0;
    padding-bottom: 80px;
    font-family: sans-serif;
  }

  /* Search */
  .search-bar {
    background: white;
    border-radius: 10px;
    box-shadow: 0px 2px 8px #00000020;
    display: flex;
    align-items: center;
    padding: 10px 14px;
    margin: 12px;
  }
  .search-bar img { width: 20px; margin-right: 10px; opacity: 0.5; }
  .search-bar input {
    border: none; outline: none;
    font-size: 15px; width: 100%;
    background: transparent; color: #888;
  }

  /* Quick Action Buttons */
  .action-row {
    display: flex;
    gap: 12px;
    margin: 0 12px 16px 12px;
  }
  .action-btn {
    flex: 1;
    background: white;
    border-radius: 12px;
    box-shadow: 0px 2px 8px #00000018;
    text-align: center;
    padding: 16px 8px;
    text-decoration: none;
    color: black;
    font-weight: 600;
    font-size: 13px;
    transition: transform 0.1s;
  }
  .action-btn:active { transform: scale(0.97); }
  .action-btn img { width: 44px; margin-bottom: 8px; display: block; margin-left: auto; margin-right: auto; }

  /* Slider */
  .slider-wrap {
    margin: 0 12px 16px 12px;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    box-shadow: 0px 2px 10px #00000025;
  }
  .slides { display: flex; transition: transform 0.4s ease; }
  .slide { min-width: 100%; position: relative; }
  .slide img { width: 100%; height: 180px; object-fit: cover; display: block; }
  .slide-caption {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    color: white;
    padding: 20px 14px 10px 14px;
    font-size: 14px;
    font-weight: 600;
  }
  .slider-dots {
    position: absolute;
    bottom: 10px; right: 12px;
    display: flex; gap: 5px;
  }
  .sdot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    cursor: pointer;
    transition: background 0.3s;
  }
  .sdot.active { background: white; }

  /* Stats Cards */
  .stats-row {
    display: flex;
    gap: 10px;
    margin: 0 12px 16px 12px;
  }
  .stat-card {
    flex: 1;
    background: white;
    border-radius: 12px;
    box-shadow: 0px 2px 8px #00000015;
    padding: 14px 10px;
    text-align: center;
  }
  .stat-card .stat-num {
    font-size: 26px;
    font-weight: 800;
    color: #1e319d;
    line-height: 1;
  }
  .stat-card .stat-label {
    font-size: 11px;
    color: #888;
    margin-top: 4px;
  }
  .stat-card.red .stat-num { color: #ff4757; }
  .stat-card.green .stat-num { color: #2ed573; }

  /* Section header */
  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0 12px 10px 12px;
  }
  .section-header h6 {
    font-weight: 700;
    color: #1e319d;
    margin: 0;
    font-size: 15px;
  }
  .section-header a {
    font-size: 13px;
    color: #1e319d;
    text-decoration: none;
    font-weight: 600;
  }

  /* Missing person grid */
  .missing-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin: 0 12px 16px 12px;
  }
  .mp-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0px 2px 8px #00000015;
    overflow: hidden;
    text-decoration: none;
    color: black;
  }
  .mp-card img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    display: block;
  }
  .mp-card .mp-info {
    padding: 8px 10px;
  }
  .mp-card .mp-name {
    font-weight: 700;
    font-size: 14px;
    color: #1e319d;
    margin: 0;
  }
  .mp-card .mp-detail {
    font-size: 12px;
    color: #888;
    margin: 2px 0 0 0;
  }
  .mp-card .missing-tag {
    display: inline-block;
    background: #ff4757;
    color: white;
    font-size: 10px;
    padding: 2px 7px;
    border-radius: 10px;
    margin-top: 4px;
    font-weight: 600;
  }

  /* Awareness banner */
  .awareness {
    margin: 0 12px 16px 12px;
    background: linear-gradient(135deg, #1e319d, #3a56d4);
    border-radius: 12px;
    padding: 16px;
    color: white;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .awareness .aw-icon { font-size: 32px; }
  .awareness h6 { margin: 0; font-weight: 700; font-size: 14px; }
  .awareness p { margin: 4px 0 0 0; font-size: 12px; opacity: 0.85; }

  /* Footer */
  #footer1 {
    bottom: 0; position: fixed;
    width: 100%; background: #F1F7FE;
  }
  .footer {
    display: flex;
    box-shadow: 0px 0px 4px 0px;
    justify-content: space-around;
    padding: 10px 0;
  }
</style>
<body>

<div id="header-container"></div>

<div class="container-fluid" style="padding:0;">

  <!-- Search Bar -->
  <div class="search-bar">
    <img src="/FYP/images/search.png" alt="">
    <input type="search" placeholder="Search missing persons...">
  </div>

  <!-- Quick Action Buttons -->
  <div class="action-row">
    <a href="/FYP/missingperson.html" class="action-btn">
      <img src="/FYP/images/group9.png" alt="">
      Missing Person
    </a>
    <a href="http://localhost:5000/foundperson" class="action-btn">
      <img src="/FYP/images/clientmanagement.png" alt="">
      Found Person
    </a>
  </div>

  <!-- Auto Slider -->
  <div class="slider-wrap">
    <div class="slides" id="sliderTrack">
      <div class="slide">
        <img src="/FYP/images/Slider Imagge.png" alt="Slide 1">
        <div class="slide-caption">Help us find missing persons</div>
      </div>
      <div class="slide">
        <img src="/FYP/images/Slider Imagge.png" alt="Slide 2">
        <div class="slide-caption">Report a found person today</div>
      </div>
      <div class="slide">
        <img src="/FYP/images/Slider Imagge.png" alt="Slide 3">
        <div class="slide-caption">Together we can reunite families</div>
      </div>
    </div>
    <div class="slider-dots" id="sliderDots">
      <div class="sdot active" onclick="goSlide(0)"></div>
      <div class="sdot" onclick="goSlide(1)"></div>
      <div class="sdot" onclick="goSlide(2)"></div>
    </div>
  </div>

  <!-- Stats -->
  <div class="stats-row">
    <div class="stat-card red">
      <div class="stat-num"><?= $total_missing ?></div>
      <div class="stat-label">Total Missing</div>
    </div>
    <div class="stat-card green">
      <div class="stat-num"><?= $total_users ?></div>
      <div class="stat-label">Members</div>
    </div>
    <div class="stat-card">
      <div class="stat-num"><?= $today_reports ?></div>
      <div class="stat-label">Today's Reports</div>
    </div>
  </div>

  <!-- Awareness Banner -->
  <div class="awareness">
    <div class="aw-icon">🔔</div>
    <div>
      <h6>Kisi ko dekha?</h6>
      <p>Agar koi missing person mile toh foran report karein</p>
    </div>
  </div>

  <!-- Latest Missing Persons -->
  <div class="section-header">
    <h6>Latest Missing Persons</h6>
    <a href="/FYP/newsfeed.php">See All →</a>
  </div>

  <div class="missing-grid">
    <?php
    if (mysqli_num_rows($latest) > 0):
      while ($row = mysqli_fetch_assoc($latest)):
        $img = '/FYP/static/recognize/' . basename($row['filename']);
    ?>
    <div class="mp-card">
      <img src="<?= htmlspecialchars($img) ?>"
           onerror="this.src='/FYP/images/maleuser.png'" alt="">
      <div class="mp-info">
        <p class="mp-name"><?= htmlspecialchars($row['name']) ?></p>
        <p class="mp-detail">S/O <?= htmlspecialchars($row['fname']) ?></p>
        <p class="mp-detail">Age: <?= htmlspecialchars($row['age']) ?></p>
        <span class="missing-tag">MISSING</span>
      </div>
    </div>
    <?php endwhile;
    else: ?>
    <p style="color:#aaa; font-size:13px; grid-column: span 2; text-align:center; padding:20px;">
      Abhi koi report nahi hai
    </p>
    <?php endif; ?>
  </div>

</div>

<!-- Footer -->
<footer>
  <div id="footer1">
    <div class="row" style="width:107%;">
      <div class="col-12" style="padding:0%;">
        <div class="footer">
          <div class="footer-column">
            <a class="nav-item nav-link" href="/FYP/home.php">
              <img src="/FYP/images/home.png" alt="">
            </a>
          </div>
          <div class="footer-column">
            <a class="nav-item nav-link" href="/FYP/newsfeed.php">
              <img src="/FYP/images/news.png" alt="">
            </a>
          </div>
          <div class="footer-column">
            <a class="nav-item nav-link" href="/FYP/feedback.php">
              <img src="/FYP/images/comments.png" alt="">
            </a>
          </div>
          <div class="footer-column">
            <a class="nav-item nav-link" href="/FYP/update_profile.php">
              <img src="/FYP/images/maleuser.png" alt="">
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>

<script>
// Auto Slider
let current = 0;
const track = document.getElementById('sliderTrack');
const dots = document.querySelectorAll('.sdot');
const total = 3;

function goSlide(n) {
  current = n;
  track.style.transform = `translateX(-${current * 100}%)`;
  dots.forEach((d, i) => d.classList.toggle('active', i === current));
}

function nextSlide() {
  goSlide((current + 1) % total);
}

// Auto play every 3 seconds
setInterval(nextSlide, 3000);
</script>

</body>
</html>
