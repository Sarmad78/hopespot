<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$msg_type = '';

// Current user data fetch karo
$select = mysqli_query($conn, "SELECT * FROM user WHERE id='$user_id'");
$fetch = mysqli_fetch_assoc($select);

// ---- Profile Image Upload ----
if (isset($_FILES['update_image']) && $_FILES['update_image']['error'] == 0) {
    $file_name = time() . '_' . basename($_FILES['update_image']['name']);
    $file_tmp = $_FILES['update_image']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($file_ext, $allowed)) {
        $message = "Sirf JPG, PNG ya GIF allowed hai!";
        $msg_type = 'error';
    } else {
        $upload_dir = __DIR__ . '/uploaded_img/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
            // Purani image delete karo
            if ($fetch['image'] && file_exists($upload_dir . $fetch['image'])) {
                unlink($upload_dir . $fetch['image']);
            }
            mysqli_query($conn, "UPDATE user SET image='$file_name' WHERE id='$user_id'");
            $_SESSION['profile_image'] = $file_name;
            $fetch['image'] = $file_name;
            $message = "Profile photo update ho gayi!";
            $msg_type = 'success';
        } else {
            $message = "Image upload nahi hui. Dobara try karein.";
            $msg_type = 'error';
        }
    }
}

// ---- Profile Info Update ----
if (isset($_POST['update_profile'])) {
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);

    mysqli_query($conn, "UPDATE user SET name='$update_name', email='$update_email' WHERE id='$user_id'");
    $fetch['name'] = $update_name;
    $fetch['email'] = $update_email;

    // Password update
    $old_pass = md5($_POST['old_pass']);
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    if (!empty($new_pass)) {
        if ($old_pass != $fetch['password']) {
            $message = "Purana password galat hai!";
            $msg_type = 'error';
        } elseif ($new_pass != $confirm_pass) {
            $message = "Naya password aur confirm password match nahi karte!";
            $msg_type = 'error';
        } else {
            $hashed = md5($new_pass);
            mysqli_query($conn, "UPDATE user SET password='$hashed' WHERE id='$user_id'");
            $message = "Password successfully update ho gaya!";
            $msg_type = 'success';
        }
    } else {
        if (empty($message)) {
            $message = "Profile successfully update ho gaya!";
            $msg_type = 'success';
        }
    }
}

// Refresh user data
$select = mysqli_query($conn, "SELECT * FROM user WHERE id='$user_id'");
$fetch = mysqli_fetch_assoc($select);

$profile_img = ($fetch['image'] && file_exists(__DIR__ . '/uploaded_img/' . $fetch['image']))
    ? '/FYP/uploaded_img/' . $fetch['image']
    : '/FYP/images/maleuser.png';

$initials = strtoupper(substr($fetch['name'] ?? 'U', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - HopeSpot</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="/FYP/js/script.js"></script>
</head>
<style>
    * { box-sizing: border-box; }
    body {
        background-color: #F1F7FE;
        margin: 0; padding: 0;
        padding-bottom: 90px;
        font-family: sans-serif;
    }

    /* Profile Header */
    .profile-header {
        background: linear-gradient(135deg, #1e319d, #3a56d4);
        padding: 30px 20px 50px 20px;
        text-align: center;
        position: relative;
    }
    .profile-header h5 {
        color: white;
        font-weight: 700;
        font-size: 18px;
        margin: 0 0 20px 0;
    }
    .avatar-wrap {
        position: relative;
        display: inline-block;
        margin-bottom: 10px;
    }
    .avatar-wrap img {
        width: 90px; height: 90px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    .avatar-edit-btn {
        position: absolute;
        bottom: 2px; right: 2px;
        background: #ff4757;
        border: none;
        border-radius: 50%;
        width: 26px; height: 26px;
        color: white;
        font-size: 13px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }
    .profile-header .user-name {
        color: white;
        font-size: 18px;
        font-weight: 700;
        margin: 8px 0 2px 0;
    }
    .profile-header .user-email {
        color: rgba(255,255,255,0.8);
        font-size: 13px;
        margin: 0;
    }

    /* Cards */
    .profile-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 10px #00000015;
        margin: -20px 12px 14px 12px;
        padding: 18px;
        position: relative;
    }
    .card-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e319d;
        margin-bottom: 14px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f0f4ff;
    }
    .form-field {
        margin-bottom: 14px;
    }
    .form-field label {
        font-size: 12px;
        font-weight: 600;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        display: block;
    }
    .form-field input {
        width: 100%;
        border: none;
        background: #f5f7ff;
        border-radius: 8px;
        padding: 11px 14px;
        font-size: 14px;
        outline: none;
        box-shadow: 0 1px 4px #00000010;
        color: #333;
    }
    .form-field input:focus {
        box-shadow: 0 0 0 2px #1e319d40;
    }
    .save-btn {
        width: 100%;
        background: #1e319d;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 13px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 4px;
    }
    .save-btn:active { background: #162580; }

    /* Alert */
    .alert-box {
        border-radius: 10px;
        padding: 11px 15px;
        margin: 0 12px 14px 12px;
        font-size: 14px;
        font-weight: 500;
    }
    .alert-box.success { background: #d4edda; color: #155724; }
    .alert-box.error { background: #f8d7da; color: #721c24; }

    /* Logout */
    .logout-btn {
        display: block;
        margin: 0 12px 14px 12px;
        background: white;
        border: 2px solid #ff4757;
        color: #ff4757;
        border-radius: 10px;
        padding: 12px;
        text-align: center;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        cursor: pointer;
    }

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

<!-- Profile Header with photo -->
<div class="profile-header">
    <h5>My Profile</h5>
    <form method="POST" enctype="multipart/form-data" id="imageForm">
        <div class="avatar-wrap">
            <img src="<?= htmlspecialchars($profile_img) ?>"
                 onerror="this.src='/FYP/images/maleuser.png'"
                 id="preview" alt="Profile">
            <button type="button" class="avatar-edit-btn" onclick="document.getElementById('pic').click()">✎</button>
        </div>
        <input type="file" id="pic" name="update_image" accept="image/jpg,image/jpeg,image/png"
               style="display:none" onchange="previewAndUpload(event)">
    </form>
    <p class="user-name"><?= htmlspecialchars($fetch['name']) ?></p>
    <p class="user-email"><?= htmlspecialchars($fetch['email']) ?></p>
</div>

<?php if ($message): ?>
<div class="alert-box <?= $msg_type ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<!-- Profile Info Form -->
<form method="POST" enctype="multipart/form-data">
    <div class="profile-card">
        <div class="card-title">📋 Personal Info</div>
        <div class="form-field">
            <label>Full Name</label>
            <input type="text" name="update_name" value="<?= htmlspecialchars($fetch['name']) ?>" placeholder="Apna naam likhein" required>
        </div>
        <div class="form-field">
            <label>Email</label>
            <input type="email" name="update_email" value="<?= htmlspecialchars($fetch['email']) ?>" placeholder="Email" required>
        </div>
        <button type="submit" name="update_profile" class="save-btn">Save Changes</button>
    </div>

    <!-- Password Section -->
    <div class="profile-card" style="margin-top: 0;">
        <div class="card-title">🔒 Password Change</div>
        <div class="form-field">
            <label>Purana Password</label>
            <input type="password" name="old_pass" placeholder="Purana password">
        </div>
        <div class="form-field">
            <label>Naya Password</label>
            <input type="password" name="new_pass" placeholder="Naya password">
        </div>
        <div class="form-field">
            <label>Confirm Password</label>
            <input type="password" name="confirm_pass" placeholder="Confirm karein">
        </div>
        <button type="submit" name="update_profile" class="save-btn">Update Password</button>
    </div>
</form>

<!-- Logout -->
<a href="logout.php" class="logout-btn">🚪 Logout</a>

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
function previewAndUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Preview
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('preview').src = e.target.result;
    };
    reader.readAsDataURL(file);

    // Auto upload
    const formData = new FormData();
    formData.append('update_image', file);

    fetch('update_profile.php', {
        method: 'POST',
        body: formData
    }).then(response => {
        if (response.ok) {
            showToast('Profile photo update ho gayi! ✅');
        }
    }).catch(() => {
        showToast('Upload mein masla hua ❌');
    });
}

function showToast(msg) {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; bottom: 90px; left: 50%; transform: translateX(-50%);
        background: #1e319d; color: white; padding: 10px 20px;
        border-radius: 20px; font-size: 13px; z-index: 9999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    `;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>

</body>
</html>
