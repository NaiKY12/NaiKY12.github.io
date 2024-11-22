<?php
// เริ่มต้น session
session_start();

// สมมุติว่าผู้ใช้ยังไม่ได้เข้าสู่ระบบหรือไม่ได้กำหนดค่า session นี้
if (!isset($_SESSION['role'])) {
    // ถ้าไม่มีการกำหนด role ให้กำหนดค่าเริ่มต้น
    $_SESSION['role'] = 'user'; // หรือค่าอื่นๆ ตามที่คุณต้องการ
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f9;
            position: relative; /* ให้สามารถจัดตำแหน่งปุ่มได้ */
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .menu {
            text-align: center;
            margin-top: 50px;
        }

        .menu a {
            display: inline-block;
            margin: 10px 20px;
            padding: 15px 30px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            font-size: 18px;
            border-radius: 5px;
        }

        .menu a:hover {
            background-color: #45a049;
        }

        /* ปุ่มออกจากระบบ */
        .logout-btn, .login-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 10px 20px;  /* ขนาดปุ่มเท่ากัน */
            background-color: #4CAF50; /* สีเขียว */
            color: white;
            text-decoration: none;
            font-size: 18px;
            border-radius: 5px;
        }

        .logout-btn:hover, .login-btn:hover {
            background-color: #45a049;
        }

        /* ปรับให้ logout-btn อยู่ด้านล่างของ login-btn */
        .logout-btn {
            top: 50px;  /* ย้ายออกจากระบบลงมาข้างล่าง */
            background-color: #f44336;
        }

        .logout-btn:hover {
            background-color: #e53935;
        }

        .user-info {
            text-align: right;
            margin-bottom: 20px;
        }

        /* ปุ่มจัดการผู้ใช้งานสำหรับแอดมิน */
        .admin-btn {
            position: fixed; /* ทำให้ปุ่มคงที่เมื่อเลื่อนหน้า */
            top: 50%; /* ตั้งปุ่มให้อยู่กลางในแนวตั้ง */
            right: 20px; /* ตั้งให้ปุ่มอยู่ที่ขวาของหน้าจอ */
            transform: translateY(-50%); /* ย้ายปุ่มให้อยู่กลางแนวตั้ง */
            padding: 10px 20px;
            background-color: #2196F3; /* สีน้ำเงิน */
            color: white;
            text-decoration: none;
            font-size: 18px;
            border-radius: 5px;
        }

        .admin-btn:hover {
            background-color: #1976D2;
        }
    </style>
</head>
<body>

    <h1>ยินดีต้อนรับสู่หน้าหลัก</h1>

    <!-- ถ้าผู้ใช้เข้าสู่ระบบแล้ว -->
    <?php if (isset($_SESSION['username'])): ?>
        <div class="user-info">
            <span>สวัสดี, <?= $_SESSION['username'] ?></span>
            <!-- ปุ่มออกจากระบบ -->
            <a href="logout.php" class="logout-btn">ออกจากระบบ</a>
        </div>

        <!-- ถ้าผู้ใช้เป็นแอดมิน -->
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <a href="admin.php" class="admin-btn">จัดการผู้ใช้งาน</a>
        <?php endif; ?>

        <!-- เมนูสำหรับผู้ใช้ที่ไม่ใช่ viewer -->
        <?php if ($_SESSION['role'] !== 'viewer'): ?>
            <div class="menu">
                <a href="form.php">เพิ่มข้อมูล</a>
            </div>
        <?php endif; ?>

        <div class="menu">
            <a href="view_data.php">ข้อมูลที่มี</a>
            <a href="search.php">ค้นหาข้อมูล</a>
        </div>

    <?php else: ?>
        <!-- ถ้าผู้ใช้ยังไม่เข้าสู่ระบบ -->
        <div class="menu">
            <a href="login.php" class="login-btn">เข้าสู่ระบบ</a>
        </div>
    <?php endif; ?>

</body>
</html>
