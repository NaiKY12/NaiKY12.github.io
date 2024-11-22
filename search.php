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

// ค้นหาข้อมูล
if (isset($_POST['search'])) {
    $search_term = $_POST['search_term'];
    $sql = "SELECT id, name, category, A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, created_at FROM name WHERE name LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_term_like = "%" . $search_term . "%";  // ทำการค้นหาด้วย LIKE
    $stmt->bind_param("s", $search_term_like);
    $stmt->execute();
    $result = $stmt->get_result();
}

// ลบข้อมูล
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM name WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        header("Location: search.php"); // รีเฟรชหน้าเมื่อทำการลบสำเร็จ
        exit;
    } else {
        echo "เกิดข้อผิดพลาดในการลบข้อมูล: " . $stmt->error;
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ค้นหาข้อมูล</title>
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

        /* ฟอร์มค้นหาข้อมูล */
        .search-form {
            text-align: center;
            margin-bottom: 30px;
        }

        input[type="text"] {
            padding: 10px;
            width: 200px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
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

        /* ตารางแสดงข้อมูล */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        .checkbox-column {
            text-align: center;
        }

        .action-buttons {
            text-align: center;
        }

        .btn-edit, .btn-delete {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
        }

        .btn-edit {
            background-color: #4CAF50;
        }

        .btn-edit:hover {
            background-color: #45a049;
        }

        .btn-delete {
            background-color: #f44336;
        }

        .btn-delete:hover {
            background-color: #e53935;
        }

        /* สีสำหรับ ✔ และ ✘ */
        .checkmark {
            color: green; /* สีเขียว */
        }

        .crossmark {
            color: red; /* สีแดง */
        }
    </style>
</head>
<body>

    <h1>ค้นหาข้อมูล</h1>

    <!-- ฟอร์มค้นหาข้อมูล -->
    <div class="search-form">
        <form action="search.php" method="POST">
            <input type="text" name="search_term" placeholder="ค้นหาข้อมูล..." value="<?php echo isset($search_term) ? htmlspecialchars($search_term) : ''; ?>" required>
            <button type="submit" name="search">ค้นหา</button>
        </form>
    </div>

    <!-- ผลลัพธ์การค้นหา -->
    <h2>ผลลัพธ์การค้นหา:</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>ชื่อ</th>
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
            <th>เวลา</th>
            <th>ดำเนินการ</th>
        </tr>

        <?php
        if (isset($result) && $result->num_rows > 0) {
            // แสดงข้อมูล
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["name"] . "</td>";
                echo "<td>" . $row["category"] . "</td>";

                // แสดงข้อมูลช่องติก A - O
                foreach (range('A', 'O') as $letter) {
                    $value = $row[$letter] ? "<span class='checkmark'>✔</span>" : "<span class='crossmark'>✘</span>";
                    echo "<td class='checkbox-column'>$value</td>";
                }

                // แสดงเวลาที่บันทึก
                echo "<td>" . $row["created_at"] . "</td>";

                // ปุ่มแก้ไขและลบ
                echo "<td class='action-buttons'>
                        <a href='edit_data.php?id=" . $row["id"] . "' class='btn-edit'>แก้ไข</a> |
                        <a href='search.php?delete_id=" . $row["id"] . "' class='btn-delete' onclick='return confirm(\"คุณต้องการลบข้อมูลนี้หรือไม่?\")'>ลบ</a>
                    </td>";
                    
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='20' style='text-align:center;'>ไม่พบข้อมูลที่ค้นหา</td></tr>";
        }

        $conn->close();
        ?>
    </table>

    <!-- ปุ่มกลับหน้า Home -->
    <div class="menu">
        <a href="javascript:history.back()" class="home-button">ย้อนหลับ</a>
    </div>

</body>
</html>
