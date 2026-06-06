<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <title>Document</title>
</head>

<body>
    <header>
        <div class="header">
            <div class="row" style="width: 100%;">
                <div class="col-4 ">
                    <img src="/fyp/images/icons8-menu-48.png" alt="" style="width: 31%; margin-left: 10%;">
                </div>
                <div class="col-4 " style="text-align: center;">
                    <h4>HopeSpot</h4>
                </div>
                <div class="col-4 " style="text-align: right;
                padding: 0;">
                    <img src="/fyp/images/icons8-bell-48.png" alt="" style="width: 24%;">
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid">
        <div class="brand">
            <h4 style="line-height: 249%;">Found Person </h4>
        </div>
        <div class="row" style="margin-top: 20%;">
            <div class="col-7">
                <h1>Person Detail</h1>
                <p><?php echo $result; ?></p>

                <?php if ($matched_image): ?>
                    <img src="/static/recognize/<?php echo $matched_image; ?>" alt="Matched Image">
                <?php endif; ?>

                <?php if ($additional_info): ?>
                <h2>Additional Information:</h2>
                <ul>
                    <li><strong>Name:</strong> <?php echo $additional_info[0]; ?></li>
                    <li><strong>Father's Name:</strong> <?php echo $additional_info[1]; ?></li>
                    <li><strong>Age:</strong> <?php echo $additional_info[2]; ?></li>
                    <li><strong>Phone Number:</strong> <?php echo $additional_info[3]; ?></li>
                    <li><strong>More Details:</strong> <?php echo $additional_info[4]; ?></li>
                    <!-- Add more fields as needed -->
                </ul>
                <?php else: ?>
                <p>No additional information available.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="seemore">
            <a href="/Details.html" style=" text-decoration: none;
      margin-left: 72%;">See More...</a>
        </div>
    </div>
    <footer>
        <div id="footer1">
            <div class="row" id="row" style="width:107%; ">
                <div class="col-12" style="padding: 0%;">
                    <div class="footer">
                        <div class="footer-column">
                            <a class="nav-item nav-link " href="/fyp/home.html">
                                <img src="/fyp/images/home.png" alt="">
                            </a>
                        </div>
                        <div class="footer-column">
                            <a class="nav-item nav-link " href="/fyp/newsfeed.html">
                                <img src="/fyp/images/news.png" alt="">
                            </a>
                        </div>
                        <div class="footer-column">
                            <a class="nav-item nav-link " href="/fyp/feedback.html">
                                <img src="/fyp/images/comments.png" alt="">
                            </a>
                        </div>
                        <div class="footer-column">
                            <a class="nav-item nav-link " id="nav-home-tab" data-toggle="tab" href="/fyp/profile.html"
                                role="tab" aria-controls="nav-home" aria-selected="true">
                                <img src="/fyp/images/maleuser.png" alt="">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
