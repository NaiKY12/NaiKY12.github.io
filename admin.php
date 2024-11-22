<?php
session_start();

// ตรวจสอบว่าเป็นแอดมินหรือไม่
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "thatsanai_data";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    // อัปเดต role ของผู้ใช้
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $user_id);
    
    if ($stmt->execute()) {
        $message = "เปลี่ยนยศของผู้ใช้สำเร็จ!";
    } else {
        $message = "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
}

// ดึงข้อมูลผู้ใช้ทั้งหมด
$result = $conn->query("SELECT * FROM users");

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ใช้</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            padding: 20px;
            background-color: #4CAF50;
            color: white;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        select, button {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        select {
            width: 200px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        .message {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #f0f8ff;
            border: 1px solid #b0e0e6;
            border-radius: 5px;
        }

        .btn-back {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: #008CBA;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-back:hover {
            background-color: #007B9D;
        }
    </style>
</head>
<body>

    <h1>จัดการผู้ใช้</h1>

    <!-- แสดงข้อความผลลัพธ์ -->
    <?php if (!empty($message)) : ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ชื่อผู้ใช้</th>
                <th>ยศ</th>
                <th>แก้ไขยศ</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= $user['username'] ?></td>
                    <td><?= $user['role'] ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <select name="role">
                                <option value="viewer" <?= $user['role'] == 'viewer' ? 'selected' : '' ?>>ผู้ดูข้อมูล</option>
                                <option value="editor" <?= $user['role'] == 'editor' ? 'selected' : '' ?>>ผู้กรอกข้อมูล</option>
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>แอดมิน</option>
                            </select>
                            <button type="submit" name="change_role">เปลี่ยนยศ</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- ปุ่มกลับหน้า Home -->
    <a href="home.php" class="btn-back">กลับหน้า Home</a>

</body>
</html>
