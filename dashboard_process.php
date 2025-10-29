<?php
// dashboard_process.php

// --- START ADVANCED DEBUGGING ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/my_debug_error.log');
error_reporting(E_ALL);
// --- END ADVANCED DEBUGGING ---

require_once 'csrf.php';
require_once 'config_mysqli.php';

$user_id_from_session = $_SESSION['user_id'] ?? null;

// --- !! START AUTHENTICATION CHECK !! ---
if (!$user_id_from_session) {
    // ถ้าไม่มี session (ยังไม่ล็อกอิน)
    // สั่งให้ browser วาร์ปไปหน้า login.php ทันที
    $_SESSION['flash'] = 'Hey! bro. why u do not login first???';
    header('Location: login.php');
    exit; 
}
// --- !! END AUTHENTICATION CHECK !! ---
$display_name = 'Guest'; // Default name

// --- เชื่อมต่อฐานข้อมูล ---
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    http_response_code(500);
    error_log('Database connection failed: ' . $mysqli->connect_error);
    // ใน process file อาจจะไม่ต้อง die() แต่คืนค่า error หรือตั้งค่า default แทน
    // die('Database connection failed.');
    // ตั้งค่าข้อมูลเป็นว่างเปล่า หรือค่า default ถ้าเชื่อมต่อไม่ได้
    $monthly = $category = $region = $topProducts = $payment = $hourly = $newReturning = $kpis = [];
    $kpi = ['sales_30d'=>0,'qty_30d'=>0,'buyers_30d'=>0];
} else {
    $mysqli->set_charset('utf8mb4');

    // --- ดึง Display Name จากตาราง users ---
    if ($user_id_from_session) {
        $sql_user = "SELECT display_name FROM users WHERE id = ?";
        $stmt = $mysqli->prepare($sql_user);
        if ($stmt) {
            $stmt->bind_param("i", $user_id_from_session);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($user_row = $result->fetch_assoc()) {
                if (!empty($user_row['display_name'])) {
                     $display_name = $user_row['display_name'];
                } else {
                     error_log("User ID {$user_id_from_session} found but display_name is empty.");
                }
            } else {
                 error_log("User ID {$user_id_from_session} not found in users table.");
            }
            $stmt->close();
        } else {
            error_log("Failed to prepare statement for user query: " . $mysqli->error);
        }
    }

    // --- ฟังก์ชัน Helper ---
    function fetch_all($mysqli, $sql) {
        $res = $mysqli->query($sql);
        if (!$res) {
             error_log("SQL Error: " . $mysqli->error . " | Query: " . $sql);
             return []; // คืนค่า array ว่าง ถ้า query ล้มเหลว
        }
        $rows = [];
        while ($row = $res->fetch_assoc()) { $rows[] = $row; }
        $res->free();
        return $rows;
    }

    // --- ดึงข้อมูลสำหรับกราฟ (จาก Views) ---
    $monthly = fetch_all($mysqli, "SELECT ym, net_sales FROM v_monthly_sales");
    $category = fetch_all($mysqli, "SELECT category, net_sales FROM v_sales_by_category");
    $region = fetch_all($mysqli, "SELECT region, net_sales FROM v_sales_by_region");
    $topProducts = fetch_all($mysqli, "SELECT product_name, qty_sold, net_sales FROM v_top_products");
    $payment = fetch_all($mysqli, "SELECT payment_method, net_sales FROM v_payment_share");
    $hourly = fetch_all($mysqli, "SELECT hour_of_day, net_sales FROM v_hourly_sales");
    $newReturning = fetch_all($mysqli, "
        SELECT date_key, new_customer_sales, returning_sales
        FROM v_new_vs_returning
        WHERE date_key >= DATE_SUB(CURDATE(), INTERVAL 89 DAY)
        ORDER BY date_key
    ");
    $kpis = fetch_all($mysqli, "SELECT sales_30d, qty_30d, buyers_30d FROM v_kpi_30d");
    $kpi = $kpis ? $kpis[0] : ['sales_30d'=>0,'qty_30d'=>0,'buyers_30d'=>0]; // ใช้ข้อมูลแรก หรือ default

    $mysqli->close();
}

// --- Helper for number format ---
// (ควรมีอยู่นอก if-else เพราะ dashboard.php ต้องเรียกใช้เสมอ)
function nf($n) { return number_format((float)$n, 2); }

?>
