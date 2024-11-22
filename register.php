<?php
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

$message = ''; // ข้อความสำหรับแสดงผล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ตรวจสอบว่าไม่มีช่องว่างในฟอร์ม
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $message = "กรุณากรอกข้อมูลให้ครบถ้วน!";
    } elseif ($password !== $confirm_password) {
        // ตรวจสอบว่ารหัสผ่านตรงกันหรือไม่
        $message = "รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน!";
    } else {
        // ตรวจสอบว่ามีชื่อผู้ใช้นี้ในฐานข้อมูลหรือไม่
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // ชื่อผู้ใช้มีอยู่แล้ว
            $message = "ชื่อผู้ใช้ $username นี้มีอยู่แล้ว กรุณาเลือกชื่อใหม่!";
        } else {
            // เข้ารหัสรหัสผ่านก่อนเก็บ
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // กำหนด role เป็น 'viewer' (แสดงว่าเป็นผู้ใช้ทั่วไป)
            $role = 'viewer';

            // เพิ่มข้อมูลผู้ใช้ใหม่
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $message = "ลงทะเบียนสำเร็จ! คุณสามารถเข้าสู่ระบบได้เลย";
            } else {
                $message = "เกิดข้อผิดพลาด: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียนผู้ใช้</title>
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

        .message {
            color: green;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .error {
            color: red;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .login-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
        }

        .login-btn:hover {
            background-color: #1976D2;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h1>ลงทะเบียนผู้ใช้</h1>

    <!-- แสดงข้อความข้อผิดพลาดหรือข้อความสำเร็จ -->
    <?php if (!empty($message)) : ?>
        <div class="<?= strpos($message, "สำเร็จ") !== false ? 'message' : 'error' ?>"><?= $message ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <label for="username">ชื่อผู้ใช้</label>
        <input type="text" name="username" id="username" required>

        <label for="password">รหัสผ่าน</label>
        <input type="password" name="password" id="password" required>

        <label for="confirm_password">ยืนยันรหัสผ่าน</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <button type="submit">ลงทะเบียน</button>
    </form>

    <!-- ปุ่มไปหน้า login จะแสดงตลอดเวลา -->
    <a href="login.php" class="login-btn">ไปที่หน้าเข้าสู่ระบบ</a>
</div>

</body>
</html>
