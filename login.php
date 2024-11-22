<?php
// เริ่มต้น session
session_start();

// ข้อมูลการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "thatsanai_data";
// สร้างการเชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่าผู้ใช้ได้ทำการล็อกอินแล้วหรือยัง
if (isset($_SESSION['username'])) {
    // ถ้าเข้าสู่ระบบแล้วก็รีไดเร็กต์ไปหน้าอื่น (เช่นหน้า home)
    header("Location: home.php");
    exit;
}

// เช็คว่าเป็นการส่งข้อมูลผ่านฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ตรวจสอบว่าไม่มีช่องว่างในฟอร์ม
    if (empty($username) || empty($password)) {
        $error_message = "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน!";
    } else {
        // ค้นหาผู้ใช้ในฐานข้อมูล
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // ตรวจสอบว่าเจอผู้ใช้หรือไม่
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // ตรวจสอบรหัสผ่าน
            if (password_verify($password, $user['password'])) {
                // ถ้าถูกต้องเริ่ม session
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role']; // เพิ่มการตั้งค่า role ใน session

                // รีไดเร็กต์ไปหน้า home หลังจากเข้าสู่ระบบสำเร็จ
                header("Location: home.php");
                exit;
            } else {
                $error_message = "รหัสผ่านไม่ถูกต้อง!";
            }
        } else {
            $error_message = "ไม่พบชื่อผู้ใช้ในระบบ!";
        }
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f9;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 16px;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h1>เข้าสู่ระบบ</h1>
    
    <!-- แสดงข้อความข้อผิดพลาดถ้ามี -->
    <?php if (!empty($error_message)) : ?>
        <div class="error"><?= $error_message ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="username">ชื่อผู้ใช้</label>
        <input type="text" name="username" id="username" required>

        <label for="password">รหัสผ่าน</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">เข้าสู่ระบบ</button>
    </form>

    <!-- ลิงก์ไปหน้าสมัครสมาชิก -->
    <div class="register-link">
        <p>ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></p>
    </div>
</div>

</body>
</html>
