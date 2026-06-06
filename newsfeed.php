<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_result = mysqli_query($conn, "SELECT * FROM user WHERE id = $user_id");
$current_user = mysqli_fetch_assoc($user_result);

$missing_result = mysqli_query($conn, "
    SELECT m.*, u.name as poster_name, u.image as poster_image
    FROM missing m 
    LEFT JOIN user u ON m.user_id = u.id 
    ORDER BY m.created_at DESC
");

if (!$missing_result) {
    $missing_result = mysqli_query($conn, "SELECT * FROM missing ORDER BY created_at DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsfeed - HopeSpot</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="/FYP/js/script.js"></script>
</head>
<style>
    * { box-sizing: border-box; }
    body {
        background-color: #F1F7FE;
        margin: 0; padding: 0;
        padding-bottom: 80px;
        font-family: sans-serif;
    }
    .brand {
        text-align: center;
        color: #1e319d;
        padding: 14px 0 8px 0;
        font-weight: 700;
        font-size: 17px;
    }
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
    .post-card {
        background: white;
        border-radius: 14px;
        box-shadow: 0px 2px 8px #00000018;
        margin: 10px 12px;
        overflow: hidden;
    }
    .post-header {
        display: flex;
        align-items: center;
        padding: 12px 14px 8px 14px;
        gap: 10px;
    }
    .post-header img.avatar {
        width: 42px; height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #1e319d;
        flex-shrink: 0;
    }
    .avatar-initials {
        width: 42px; height: 42px;
        border-radius: 50%;
        background: #1e319d;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 17px;
        flex-shrink: 0;
    }
    .poster-name {
        font-weight: 600;
        color: #1e319d;
        font-size: 14px;
        margin: 0;
    }
    .post-time {
        font-size: 11px;
        color: #aaa;
        margin: 0;
    }
    .missing-badge {
        background: #ff4757;
        color: white;
        font-size: 10px;
        padding: 3px 9px;
        border-radius: 20px;
        margin-left: auto;
        font-weight: 700;
        flex-shrink: 0;
    }
    .post-image {
        width: 100%;
        max-height: 260px;
        object-fit: cover;
        display: block;
    }
    .post-details {
        padding: 12px 14px;
    }
    .post-details h5 {
        color: #1e319d;
        font-weight: 700;
        margin-bottom: 6px;
        font-size: 15px;
    }
    .detail-row {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        margin-top: 4px;
    }
    .detail-item {
        font-size: 12px;
        color: #666;
    }
    .detail-item span { font-weight: 600; color: #333; }
    .more-detail {
        font-size: 13px;
        color: #666;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #f0f0f0;
    }
    .no-posts {
        text-align: center;
        padding: 40px 20px;
        color: #aaa;
        font-size: 14px;
    }
</style>
<body>
    <div id="header-container"></div>
    <div class="brand">Missing Persons Feed</div>

    <div class="container-fluid" style="padding: 0;">
        <?php
        $count = mysqli_num_rows($missing_result);
        if ($count == 0): ?>
            <div class="no-posts">Abhi koi missing person report nahi hai.</div>
        <?php else:
            while ($row = mysqli_fetch_assoc($missing_result)):
                $poster_name = !empty($row['poster_name']) ? $row['poster_name'] : 'Unknown User';
                $poster_image = $row['poster_image'] ?? null;

                // Profile image logic
                if ($poster_image && file_exists(__DIR__ . '/uploaded_img/' . $poster_image)) {
                    $avatar_src = '/FYP/uploaded_img/' . $poster_image;
                    $show_initials = false;
                } else {
                    $show_initials = true;
                    $initial = strtoupper(substr($poster_name, 0, 1));
                }

                $missing_img = $row['filename'] ? '/FYP/static/recognize/' . $row['filename'] : null;
                $time_str = isset($row['created_at']) ? date('d M Y, h:i A', strtotime($row['created_at'])) : '';
        ?>
        <div class="post-card">
            <div class="post-header">
                <?php if ($show_initials): ?>
                    <div class="avatar-initials"><?= htmlspecialchars($initial) ?></div>
                <?php else: ?>
                    <img class="avatar" src="<?= htmlspecialchars($avatar_src) ?>"
                         onerror="this.style.display='none'" alt="Profile">
                <?php endif; ?>
                <div>
                    <p class="poster-name"><?= htmlspecialchars($poster_name) ?></p>
                    <p class="post-time"><?= $time_str ?></p>
                </div>
                <span class="missing-badge">MISSING</span>
            </div>

            <?php if ($missing_img): ?>
            <img class="post-image"
                 src="<?= htmlspecialchars($missing_img) ?>"
                 onerror="this.style.display='none'"
                 alt="Missing Person">
            <?php endif; ?>

            <div class="post-details">
                <h5><?= htmlspecialchars($row['name']) ?> s/o <?= htmlspecialchars($row['fname']) ?></h5>
                <div class="detail-row">
                    <?php if ($row['age']): ?>
                    <div class="detail-item">Umar: <span><?= htmlspecialchars($row['age']) ?> saal</span></div>
                    <?php endif; ?>
                    <?php if ($row['phonenumber']): ?>
                    <div class="detail-item">Contact: <span><?= htmlspecialchars($row['phonenumber']) ?></span></div>
                    <?php endif; ?>
                </div>
                <?php if ($row['moredetail']): ?>
                <div class="more-detail"><?= htmlspecialchars($row['moredetail']) ?></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; endif; ?>
    </div>

    <footer>
        <div id="footer1">
            <div class="row" style="width:107%;">
                <div class="col-12" style="padding:0%;">
                    <div class="footer">
                        <div class="footer-column">
                            <a class="nav-item nav-link" href="/FYP/home.php"><img src="/FYP/images/home.png" alt=""></a>
                        </div>
                        <div class="footer-column">
                            <a class="nav-item nav-link" href="/FYP/newsfeed.php"><img src="/FYP/images/news.png" alt=""></a>
                        </div>
                        <div class="footer-column">
                            <a class="nav-item nav-link" href="/FYP/feedback.php"><img src="/FYP/images/comments.png" alt=""></a>
                        </div>
                        <div class="footer-column">
                            <a class="nav-item nav-link" href="/FYP/update_profile.php"><img src="/FYP/images/maleuser.png" alt=""></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>