<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
// ข้อมูลการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "thatsanai_data";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ''; // ตัวแปรสำหรับเก็บข้อความที่จะแสดง

// ตรวจสอบว่าได้รับข้อมูลจากฟอร์มหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $category = $_POST['category'];

    // ตรวจสอบว่าข้อมูลไม่ว่าง
    if (!empty($name) && !empty($category)) {
        // สร้างคำสั่ง SQL ด้วย prepared statement
        $stmt = $conn->prepare("INSERT INTO name (name, category, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $name, $category); // "ss" หมายถึง string (name, category)

        // ตรวจสอบว่าแทรกข้อมูลสำเร็จหรือไม่
        if ($stmt->execute()) {
            $message = "ข้อมูลถูกเพิ่มสำเร็จ!";
        } else {
            $message = "เกิดข้อผิดพลาดในการแทรกข้อมูล: " . $stmt->error;
        }

        // ปิดคำสั่ง prepared statement
        $stmt->close();
    } else {
        $message = "กรุณากรอกข้อมูลให้ครบถ้วน!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ป้อนข้อมูล</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f9;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .form-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
        }

        input, select {
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

        /* ปุ่มกลับหน้า Home */
        .home-button {
            display: inline-block;
            margin: 20px 20px;
            padding: 15px 30px;
            background-color: #2196F3; /* สีของปุ่ม */
            color: white;
            text-decoration: none;
            font-size: 18px;
            border-radius: 5px;
        }

        .home-button:hover {
            background-color: #1E88E5; /* สีที่เปลี่ยนเมื่อเลื่อนเมาส์ */
        }

        /* ข้อความ */
        .message {
            background-color: #f4f4f9;
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 20px;
            text-align: center;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <h1>ป้อนข้อมูล</h1>

    <!-- แสดงข้อความ (ถ้ามี) -->
    <?php if (!empty($message)) : ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <div class="form-container">
        <!-- ฟอร์มเพื่อป้อนข้อมูล -->
        <form action="form.php" method="POST" autocomplete="off">
            <label for="name">ข้อความที่ต้องการเพิ่ม: </label>
            <input type="text" id="name" name="name" required>

            <!-- ฟิลด์เลือกหมวดหมู่ -->
            <label for="category">เลือกหมวดหมู่: </label>
            <select id="category" name="category" required>
                <option value="">เลือก...</option>
                <option value="ไวไฟ">ไวไฟ</option>
                <option value="กล้องวงจรปิด">กล้องวงจรปิด</option>
            </select>

            <button type="submit">ส่งข้อมูล</button>
        </form>
    </div>

    <!-- ปุ่มกลับหน้า Home -->
    <div class="menu">
        <a href="home.php" class="home-button">กลับหน้า Home</a>
    </div>

</body>
</html>
