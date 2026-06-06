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

$success = '';
$error = '';

if (isset($_POST['submit'])) {
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $user_name = $current_user['name'];

    if ($rating == 0) {
        $error = "Kripya rating select karein!";
    } elseif (empty($comment)) {
        $error = "Comment khali nahi hona chahiye!";
    } else {
        $sql = "INSERT INTO feedback (user_name, email, rating, comment, user_id) 
                VALUES ('$user_name', '$email', '$rating', '$comment', '$user_id')";

        if (mysqli_query($conn, $sql)) {
            $to = "thesarmaddev@gmail.com";
            $subject = "HopeSpot - Naya Feedback!";
            $body = "Naam: $user_name\nEmail: $email\nRating: $rating/10\nComment: $comment";
            $headers = "From: noreply@hopespot.com\r\nReply-To: $email\r\n";
            @mail($to, $subject, $body, $headers);
            $success = "Shukriya! Aapka feedback submit ho gaya.";
        } else {
            // user_id column nahi hai to bina us ke try karo
            $sql2 = "INSERT INTO feedback (user_name, email, rating, comment) 
                     VALUES ('$user_name', '$email', '$rating', '$comment')";
            if (mysqli_query($conn, $sql2)) {
                $success = "Shukriya! Aapka feedback submit ho gaya.";
            } else {
                $error = "Database error: " . mysqli_error($conn);
            }
        }
    }
}

// user_id column add karo feedback table mein agar nahi hai
mysqli_query($conn, "ALTER TABLE feedback ADD COLUMN IF NOT EXISTS user_id INT DEFAULT NULL");

// Feedbacks fetch with user image
$feedbacks = mysqli_query($conn, "
    SELECT f.*, u.image as user_image 
    FROM feedback f
    LEFT JOIN user u ON f.user_id = u.id
    ORDER BY f.created_at DESC
");
if (!$feedbacks) {
    $feedbacks = mysqli_query($conn, "SELECT * FROM feedback ORDER BY created_at DESC");
}

// Current user profile image
$my_image = $current_user['image'] ?? null;
$my_avatar_src = ($my_image && file_exists(__DIR__ . '/uploaded_img/' . $my_image))
    ? '/FYP/uploaded_img/' . $my_image
    : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - HopeSpot</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="/FYP/js/script.js"></script>
</head>
<style>
    * { box-sizing: border-box; }
    body {
        background-color: #F1F7FE;
        margin: 0; padding: 0;
        font-family: sans-serif;
        height: 100vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .fixed-top-section {
        flex-shrink: 0;
    }
    .comments-scroll-area {
        flex: 1;
        overflow-y: auto;
        padding-bottom: 70px;
    }
    .brand {
        text-align: center;
        color: #1e319d;
        font-size: 17px;
        font-weight: 700;
        padding: 14px 0 8px 0;
    }

    /* Form card */
    .form-card {
        background: white;
        border-radius: 14px;
        box-shadow: 0px 2px 8px #00000015;
        margin: 10px 12px;
        padding: 16px;
    }
    .form-card .card-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e319d;
        margin-bottom: 14px;
    }

    /* User row inside form */
    .form-user-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }
    .form-avatar {
        width: 40px; height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #1e319d;
        flex-shrink: 0;
    }
    .form-avatar-initials {
        width: 40px; height: 40px;
        border-radius: 50%;
        background: #1e319d;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        flex-shrink: 0;
    }
    .form-user-name {
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }

    /* Stars */
    .stars {
        display: flex;
        gap: 4px;
        font-size: 28px;
        cursor: pointer;
        justify-content: center;
        margin-bottom: 6px;
    }
    .star { color: #ddd; transition: color 0.15s; user-select: none; }
    .star.active { color: #FFD700; }
    .rating-label {
        text-align: center;
        font-size: 12px;
        color: #aaa;
        margin-bottom: 10px;
    }

    /* Inputs */
    .form-card input,
    .form-card textarea {
        width: 100%;
        border: none;
        background: #f5f7ff;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 14px;
        margin-bottom: 12px;
        outline: none;
        box-shadow: 0 1px 4px #00000010;
    }
    .form-card textarea { resize: none; height: 90px; }
    .submit-btn {
        width: 100%;
        background: #1e319d;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 12px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
    }

    /* Alert */
    .alert-box {
        border-radius: 10px;
        padding: 10px 14px;
        margin: 0 12px 10px 12px;
        font-size: 13px;
        font-weight: 500;
    }
    .alert-box.success { background: #d4edda; color: #155724; }
    .alert-box.error { background: #f8d7da; color: #721c24; }

    /* Section title */
    .section-title {
        color: #1e319d;
        font-weight: 700;
        font-size: 15px;
        margin: 4px 12px 8px 12px;
    }

    /* Feedback cards */
    .fb-card {
        background: white;
        border-radius: 14px;
        box-shadow: 0px 2px 8px #00000015;
        margin: 10px 12px;
        padding: 14px;
    }
    .fb-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    .fb-avatar {
        width: 40px; height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #1e319d;
        flex-shrink: 0;
    }
    .fb-initials {
        width: 40px; height: 40px;
        border-radius: 50%;
        background: #1e319d;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        flex-shrink: 0;
    }
    .fb-name { font-weight: 600; color: #1e319d; font-size: 14px; margin: 0; }
    .fb-time { font-size: 11px; color: #aaa; margin: 0; }
    .fb-stars { font-size: 16px; margin-bottom: 6px; }
    .fb-stars .s-on { color: #FFD700; }
    .fb-stars .s-off { color: #ddd; }
    .fb-comment { font-size: 13px; color: #444; line-height: 1.5; margin: 0; }
    .no-fb { text-align: center; color: #aaa; padding: 20px; font-size: 14px; }

    /* Footer */
    #footer1 { bottom: 0; position: fixed; width: 100%; background: #F1F7FE; }
    .footer { display: flex; box-shadow: 0px 0px 4px 0px; justify-content: space-around; padding: 10px 0; }
</style>
<body>
    <div class="fixed-top-section">
    <div id="header-container"></div>
    <div class="brand">Feedback</div>

    <?php if ($success): ?>
    <div class="alert-box success">✅ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert-box error">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Feedback Form -->
    <form method="POST" action="feedback.php">
    <div class="form-card">
        <!-- Current user profile row -->
        <div class="form-user-row">
            <?php if ($my_avatar_src): ?>
                <img class="form-avatar" src="<?= htmlspecialchars($my_avatar_src) ?>"
                     onerror="this.style.display='none'" alt="">
            <?php else: ?>
                <div class="form-avatar-initials">
                    <?= strtoupper(substr($current_user['name'], 0, 1)) ?>
                </div>
            <?php endif; ?>
            <span class="form-user-name"><?= htmlspecialchars($current_user['name']) ?></span>
        </div>

        <div class="card-title">App ko rate karein</div>

        <!-- Stars -->
        <div class="stars" id="stars">
            <?php for ($i = 1; $i <= 10; $i++): ?>
            <span class="star" data-value="<?= $i ?>">★</span>
            <?php endfor; ?>
        </div>
        <p class="rating-label" id="rating-label">Koi rating nahi di</p>
        <input type="hidden" name="rating" id="rating-input" value="0">

        <!-- Email -->
        <input type="email" name="email"
               placeholder="Email"
               value="<?= htmlspecialchars($current_user['email'] ?? '') ?>">

        <!-- Comment -->
        <textarea name="comment" placeholder="Apna tajurba likhein..."></textarea>

        <button type="submit" name="submit" class="submit-btn">Submit Feedback</button>
    </div>
    </form>

    <!-- All Feedbacks -->
    </div><!-- end fixed-top-section -->
    <div class="comments-scroll-area">
    <div class="section-title">Logon ki Raye</div>

    <?php
    $count = mysqli_num_rows($feedbacks);
    if ($count == 0): ?>
        <div class="no-fb">Abhi koi feedback nahi hai.</div>
    <?php else:
        $labels = ['','Bura','Bura','Theek','Theek','Acha','Acha','Bahut Acha','Bahut Acha','Zabardast','Zabardast'];
        while ($fb = mysqli_fetch_assoc($feedbacks)):
            $fb_image = $fb['user_image'] ?? null;
            $fb_name = $fb['user_name'] ?? 'Anonymous';

            if ($fb_image && file_exists(__DIR__ . '/uploaded_img/' . $fb_image)) {
                $fb_avatar_src = '/FYP/uploaded_img/' . $fb_image;
                $fb_show_img = true;
            } else {
                $fb_show_img = false;
                $fb_initial = strtoupper(substr($fb_name, 0, 1));
            }

            $rating = intval($fb['rating']);
            $time_str = date('d M Y', strtotime($fb['created_at']));
    ?>
    <div class="fb-card">
        <div class="fb-header">
            <?php if ($fb_show_img): ?>
                <img class="fb-avatar" src="<?= htmlspecialchars($fb_avatar_src) ?>"
                     onerror="this.style.display='none'" alt="">
            <?php else: ?>
                <div class="fb-initials"><?= htmlspecialchars($fb_initial) ?></div>
            <?php endif; ?>
            <div>
                <p class="fb-name"><?= htmlspecialchars($fb_name) ?></p>
                <p class="fb-time"><?= $time_str ?></p>
            </div>
        </div>
        <div class="fb-stars">
            <?php for ($i = 1; $i <= 10; $i++): ?>
                <span class="<?= $i <= $rating ? 's-on' : 's-off' ?>">★</span>
            <?php endfor; ?>
            <span style="font-size:11px;color:#888;margin-left:4px;">
                <?= $rating ?>/10 — <?= $labels[$rating] ?>
            </span>
        </div>
        <p class="fb-comment"><?= htmlspecialchars($fb['comment']) ?></p>
    </div>
    <?php endwhile; endif; ?>

    </div><!-- end comments-scroll-area -->
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

<script>
const stars = document.querySelectorAll('.star');
const ratingInput = document.getElementById('rating-input');
const ratingLabel = document.getElementById('rating-label');
const labels = {1:'1 — Bura',2:'2 — Bura',3:'3 — Theek',4:'4 — Theek',5:'5 — Acha',
                6:'6 — Acha',7:'7 — Bahut Acha',8:'8 — Bahut Acha',9:'9 — Zabardast',10:'10 — Zabardast'};

stars.forEach(star => {
    star.addEventListener('click', function() {
        const val = parseInt(this.dataset.value);
        ratingInput.value = val;
        ratingLabel.textContent = labels[val];
        stars.forEach(s => s.classList.toggle('active', parseInt(s.dataset.value) <= val));
    });
    star.addEventListener('mouseover', function() {
        const val = parseInt(this.dataset.value);
        stars.forEach(s => s.classList.toggle('active', parseInt(s.dataset.value) <= val));
    });
    star.addEventListener('mouseout', function() {
        const selected = parseInt(ratingInput.value);
        stars.forEach(s => s.classList.toggle('active', parseInt(s.dataset.value) <= selected));
    });
});
</script>
</body>
</html>