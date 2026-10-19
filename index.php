<?php
// 1. ตั้งค่าการเชื่อมต่อฐานข้อมูล MySQL
$servername = "localhost";
$username = "root";          // Default ของ XAMPP
$password = "";              // Default ของ XAMPP คือรหัสผ่านว่าง
$dbname = "dv-w08";          // ชื่อฐานข้อมูลตามไฟล์ SQL

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// 2. เขียนคำสั่ง SQL ดึงข้อมูลภาพยนตร์ทำรายได้เปิดตัวสูงสุด 10 อันดับแรก
$sql = "SELECT movie_name, opening, total_gross FROM topopening ORDER BY opening DESC LIMIT 10";
$result = $conn->query($sql);

$movie_names = [];
$openings = [];
$total_grosses = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $movie_names[] = $row['movie_name'];
        $openings[] = (int)$row['opening'];
        $total_grosses[] = (int)$row['total_gross'];
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top 10 Opening Movies Chart</title>
    <!-- นำเข้า Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Sarabun', Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 30px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>10 อันดับภาพยนตร์ที่ทำรายได้เปิดตัวสูงสุด (เรียกดูจากฐานข้อมูล dv-w08)</h2>
    <canvas id="movieChart"></canvas>
</div>

<script>
    const ctx = document.getElementById('movieChart').getContext('2d');

    // รับค่า Array จาก PHP แปลงเป็น JSON ให้ JavaScript ใช้งาน
    const labels = <?php echo json_encode($movie_names); ?>;
    const openingData = <?php echo json_encode($openings); ?>;
    const totalGrossData = <?php echo json_encode($total_grosses); ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'รายได้เปิดตัว (Opening Gross $)',
                    data: openingData,
                    backgroundColor: 'rgba(54, 162, 235, 0.85)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'รายได้รวมทั้งหมด (Total Gross $)',
                    data: totalGrossData,
                    backgroundColor: 'rgba(255, 99, 132, 0.85)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let val = context.raw || 0;
                            return context.dataset.label + ': $' + val.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + (value / 1000000).toFixed(0) + 'M';
                        }
                    },
                    title: {
                        display: true,
                        text: 'รายได้ (ล้านดอลลาร์สหรัฐ)'
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 25
                    }
                }
            }
        }
    });
</script>

</body>
</html>