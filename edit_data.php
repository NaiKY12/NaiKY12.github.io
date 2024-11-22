<?php
// เริ่ม session สำหรับ CSRF token
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

// ตรวจสอบว่าได้ส่งค่า ID มาหรือไม่
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // ดึงข้อมูลของรายการที่ต้องการแก้ไขจากฐานข้อมูล
    $sql = "SELECT * FROM name WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // ถ้ามีข้อมูลที่ตรงกับ ID ที่เลือก
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        // หากไม่พบข้อมูล
        echo "ไม่พบข้อมูลที่ต้องการแก้ไข";
        exit;  // หยุดการทำงาน
    }

    $stmt->close();
} else {
    echo "ไม่พบ ID สำหรับแก้ไข";
    exit;
}

// การตรวจสอบ CSRF token
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['csrf_token'] != $_SESSION['csrf_token']) {
        die("เกิดข้อผิดพลาดในการยืนยันตัวตน");
    }

    // รับค่าจากฟอร์ม
    $id = $_POST['id'];
    $A = isset($_POST['A']) ? 1 : 0;
    $B = isset($_POST['B']) ? 1 : 0;
    $C = isset($_POST['C']) ? 1 : 0;
    $D = isset($_POST['D']) ? 1 : 0;
    $E = isset($_POST['E']) ? 1 : 0;
    $F = isset($_POST['F']) ? 1 : 0;
    $G = isset($_POST['G']) ? 1 : 0;
    $H = isset($_POST['H']) ? 1 : 0;
    $I = isset($_POST['I']) ? 1 : 0;
    $J = isset($_POST['J']) ? 1 : 0;
    $K = isset($_POST['K']) ? 1 : 0;
    $L = isset($_POST['L']) ? 1 : 0;
    $M = isset($_POST['M']) ? 1 : 0;
    $N = isset($_POST['N']) ? 1 : 0;
    $O = isset($_POST['O']) ? 1 : 0;

    // คำสั่งอัปเดตข้อมูล
    $sql_update = "UPDATE name SET A = ?, B = ?, C = ?, D = ?, E = ?, F = ?, G = ?, H = ?, I = ?, J = ?, K = ?, L = ?, M = ?, N = ?, O = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("iiiiiiiiiiiiiiii", $A, $B, $C, $D, $E, $F, $G, $H, $I, $J, $K, $L, $M, $N, $O, $id);

    if ($stmt->execute()) {
        // ถ้าบันทึกข้อมูลสำเร็จ
        header("Location: view_data.php");
        exit();  // หยุดการทำงานของสคริปต์หลังจากรีไดเรกต์
    } else {
        $message = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

// สร้าง CSRF token สำหรับป้องกันการโจมตีแบบ CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // สร้าง token
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูล</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f9fafb;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #333;
            font-size: 28px;
            margin-bottom: 20px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 60%;
            margin: 0 auto;
        }

        label {
            display: block;
            font-size: 14px;
            margin-bottom: 6px;
        }

        input[type="text"], input[type="checkbox"] {
            padding: 10px;
            margin: 6px 0;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="text"] {
            width: 100%;
            border: 1px solid #ddd;
        }

        .checkbox-group {
            margin-bottom: 20px;
        }

        .checkbox-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .checkbox-row label {
            display: inline-block;
            width: 48%;
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            font-size: 14px;
        }

        .checkbox-row input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }

        .checkbox-row label:nth-child(odd) {
            background-color: #d1e7dd;
        }

        .checkbox-row label:nth-child(even) {
            background-color: #e2f3f5;
        }

        .checkbox-row label:hover {
            border-color: #4CAF50;
            background-color: #c8e6c9;
        }

        button {
            padding: 12px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .btn-back {
            display: block;
            background-color: #007B9D;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            margin-top: 20px;
            width: 100%;
            transition: background-color 0.3s;
        }

        .btn-back:hover {
            background-color: #005f6b;
        }

        .message {
            padding: 12px;
            margin: 20px 0;
            background-color: #fff8e1;
            border-left: 5px solid #fbc02d;
            color: #333;
            text-align: center;
            font-size: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .message.error {
            background-color: #ffe0e0;
            border-left: 5px solid #e57373;
            color: #d32f2f;
        }
    </style>
</head>
<body>

    <h1>แก้ไขข้อมูล</h1>

    <!-- แสดงข้อความตอบกลับ -->
    <?php if (isset($message)): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="id" value="<?= isset($row) ? $row['id'] : '' ?>">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label for="name">ชื่อ:</label>
        <input type="text" name="name" id="name" value="<?= isset($row) ? htmlspecialchars($row['name']) : '' ?>" required>

        <label for="category">หมวดหมู่:</label>
        <input type="text" name="category" id="category" value="<?= isset($row) ? htmlspecialchars($row['category']) : '' ?>" required>

        <div class="checkbox-group">
            <div class="checkbox-row">
                <label for="A"><input type="checkbox" name="A" id="A" <?= isset($row) && $row['A'] ? 'checked' : '' ?>> A</label>
                <label for="B"><input type="checkbox" name="B" id="B" <?= isset($row) && $row['B'] ? 'checked' : '' ?>> B</label>
            </div>
            <div class="checkbox-row">
                <label for="C"><input type="checkbox" name="C" id="C" <?= isset($row) && $row['C'] ? 'checked' : '' ?>> C</label>
                <label for="D"><input type="checkbox" name="D" id="D" <?= isset($row) && $row['D'] ? 'checked' : '' ?>> D</label>
            </div>
            <div class="checkbox-row">
                <label for="E"><input type="checkbox" name="E" id="E" <?= isset($row) && $row['E'] ? 'checked' : '' ?>> E</label>
                <label for="F"><input type="checkbox" name="F" id="F" <?= isset($row) && $row['F'] ? 'checked' : '' ?>> F</label>
            </div>
            <div class="checkbox-row">
                <label for="G"><input type="checkbox" name="G" id="G" <?= isset($row) && $row['G'] ? 'checked' : '' ?>> G</label>
                <label for="H"><input type="checkbox" name="H" id="H" <?= isset($row) && $row['H'] ? 'checked' : '' ?>> H</label>
            </div>
            <div class="checkbox-row">
                <label for="I"><input type="checkbox" name="I" id="I" <?= isset($row) && $row['I'] ? 'checked' : '' ?>> I</label>
                <label for="J"><input type="checkbox" name="J" id="J" <?= isset($row) && $row['J'] ? 'checked' : '' ?>> J</label>
            </div>
            <div class="checkbox-row">
                <label for="K"><input type="checkbox" name="K" id="K" <?= isset($row) && $row['K'] ? 'checked' : '' ?>> K</label>
                <label for="L"><input type="checkbox" name="L" id="L" <?= isset($row) && $row['L'] ? 'checked' : '' ?>> L</label>
            </div>
            <div class="checkbox-row">
                <label for="M"><input type="checkbox" name="M" id="M" <?= isset($row) && $row['M'] ? 'checked' : '' ?>> M</label>
                <label for="N"><input type="checkbox" name="N" id="N" <?= isset($row) && $row['N'] ? 'checked' : '' ?>> N</label>
            </div>
            <div class="checkbox-row">
                <label for="O"><input type="checkbox" name="O" id="O" <?= isset($row) && $row['O'] ? 'checked' : '' ?>> O</label>
            </div>
        </div>

        <button type="submit">บันทึกการแก้ไข</button>
    </form>

    <a href="javascript:history.back()" class="btn-back">กลับไปหน้าก่อนหน้า</a>


</body>
</html>