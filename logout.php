<?php
// เริ่มต้น session
session_start();

// ลบข้อมูล session ทั้งหมด
session_unset();  // ลบค่าทุกตัวแปรใน session

// ทำลาย session
session_destroy();

// ลบ session cookie (ถ้ามี)
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/'); // หมดอายุทันที
}

// รีไดเร็กต์ไปยังหน้า login
header("Location: login.php");
exit;
?>
