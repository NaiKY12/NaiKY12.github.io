<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// ตรวจสอบยศของผู้ใช้ใน session
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'viewer'; // สมมติว่าเก็บใน $_SESSION['role']

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

// ตรวจสอบว่าเป็นการส่งข้อมูลอัปเดตหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // สำหรับการลบข้อมูล
    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        $sql_delete = "DELETE FROM name WHERE id = ?";
        $stmt = $conn->prepare($sql_delete);
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            // ถ้าลบข้อมูลสำเร็จ, รีเฟรชหน้าเว็บใหม่เพื่อดึงข้อมูลล่าสุด
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            echo "เกิดข้อผิดพลาดในการลบข้อมูล: " . $stmt->error;
        }
        $stmt->close();
    }
}

// ดึงข้อมูลจากฐานข้อมูล เรียงลำดับจากเก่าที่สุดไปใหม่ที่สุด
$sql = "SELECT id, name, category, A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, created_at FROM name ORDER BY created_at ASC";
$result = $conn->query($sql);

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลที่มี</title>
    <style>
        /* สไตล์ต่าง ๆ */
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f9;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }
        .btn-delete {
            background-color: red;
        }
        .btn-edit {
            background-color: yellow;
            color: black;
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
        }
        .btn-back:hover {
            background-color: #007B9D;
        }
        .btn-group {
            display: flex;
            gap: 10px;
        }
        .btn-group button {
            margin: 0;
        }
        .check-letter {
            font-size: 18px;
            font-weight: bold;
        }
        /* สีของเครื่องหมายถูกและผิด */
        .correct {
            color: green;
            font-size: 18px;
            font-weight: bold;
        }
        .incorrect {
            color: red;
            font-size: 18px;
            font-weight: bold;
        }
        /* ซ่อนคอลัมน์การกระทำ */
        .action-column {
            display: none;
        }

        /* ปุ่มค้นหาติดขวาบน */
        .btn-search {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-search:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <h1>ข้อมูลที่มี</h1>

    <!-- ปุ่มไปหน้าค้นหาที่มุมขวาบน -->
    <form action="search.php" method="GET">
        <button type="submit" class="btn-search">ไปหน้าค้นหา</button>
    </form>

    <table>
        <tr>
            <th>ลำดับ</th>
            <th>ข้อความ</th>
            <th>หมวดหมู่</th>
            <th>A</th>
            <th>B</th>
            <th>C</th>
            <th>D</th>
            <th>E</th>
            <th>F</th>
            <th>G</th>
            <th>H</th>
            <th>I</th>
            <th>J</th>
            <th>K</th>
            <th>L</th>
            <th>M</th>
            <th>N</th>
            <th>O</th>
            <th>วันที่และเวลา</th>
            <?php if ($role !== 'viewer') : ?>
                <th>ดำเนินการ</th>
            <?php endif; ?>
        </tr>
        <?php if ($result->num_rows > 0) : ?>
            <?php $count = 1; ?>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['category'] ?></td>
                    <!-- แสดงเครื่องหมายถูกหรือผิดและใช้สี -->
                    <td><span class="<?= $row['A'] ? 'correct' : 'incorrect' ?>"><?= $row['A'] ? '✔' : '✘' ?></span></td>
                    <td><span class="<?= $row['B'] ? 'correct' : 'incorrect' ?>"><?= $row['B'] ? '✔' : '✘' ?></span></td>
                    <td><span class="<?= $row['C'] ? 'correct' : 'incorrect' ?>"><?= $row['C'] ? '✔' : '✘' ?></span></td>
                    <td><span class="<?= $row['D'] ? 'correct' : 'incorrect' ?>"><?= $row['D'] ? '✔' : '✘' ?></span></td>
                    <td><span class="<?= $row['E'] ? 'correct' : 'incorrect' ?>"><?= $row['E'] ? '✔' : '✘' ?></span></td>
                    <td><span class="<?= $row['F'] ? 'correct' : 'incorrect' ?>"><?= $row['F'] ? '✔' : '✘' ?></span></td>
                    <td><span class="<?= $row['G'] ? 'correct' : 'incorrect' ?>"><?= $row['G'] ? '✔' : '✘' ?></span></td>
                    <td><span class="<?= $row['H'] ? 'correct' : 'incorrect' ?>"><?= $row['H'] ? '✔' : '✘' ?></span></td>
                    <td><span class="<?= $row['I'] ? 'correct' : 'incorrect' ?>"><?= $row['I'] ? '✔' : '✘' ?></span></td>
                    <td><span class="<?= $row['J'] ? 'correct' : 'incorrect' ?>"><?= $row['J'] ? '✔' : '✘' ?></span></td>
                    <td><span class="<?= $row['K'] ? 'correct' : 'incorrect' ?>"><?= $row['K'] ? '✔' : '✘' ?></span></td>
                    <td><span class="<?= $row['L'] ? 'correct' : 'incorrect' ?>"><?= $row['L'] ? '✔' : '✘' ?></span></td>
                    <td><span class="<?= $row['M'] ? 'correct' : 'incorrect' ?>"><?= $row['M'] ? '✔' : '✘' ?></span></td>
                    <td><span class="<?= $row['N'] ? 'correct' : 'incorrect' ?>"><?= $row['N'] ? '✔' : '✘' ?></span></td>
                    <td><span class="<?= $row['O'] ? 'correct' : 'incorrect' ?>"><?= $row['O'] ? '✔' : '✘' ?></span></td>
                    <td><?= date("Y-m-d H:i:s", strtotime($row['created_at'])) ?></td>
                    <?php if ($role !== 'viewer') : ?>
                        <td>
                            <div class="btn-group">
                                <!-- ปุ่มแก้ไข -->
                                <form action="edit_data.php" method="get">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn-edit">แก้ไข</button>
                                </form>

                                <!-- ปุ่มลบ -->
                                <form method="POST">
                                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn-delete" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบข้อมูลนี้?')">ลบ</button>
                                </form>
                            </div>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr><td colspan="19">ไม่มีข้อมูล</td></tr>
        <?php endif; ?>
    </table>

    <!-- ปุ่มกลับหน้าหลัก -->
    <form action="home.php" method="GET">
        <button type="submit" class="btn-back">กลับหน้า Home</button>
    </form>

</body>
</html>
