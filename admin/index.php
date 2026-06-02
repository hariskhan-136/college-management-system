<?php
require_once 'layout_header.php';

// Fetch quick stats
$stats = [
    'students' => 0,
    'teachers' => 0,
    'courses' => 0,
    'departments' => 0
];

try {
    $stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    while ($row = $stmt->fetch()) {
        if ($row['role'] == 'student') $stats['students'] = $row['count'];
        if ($row['role'] == 'teacher') $stats['teachers'] = $row['count'];
    }
    
    $stats['courses'] = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    $stats['departments'] = $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();
    
} catch (PDOException $e) {
    // Handle error quietly for dashboard
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Admin Dashboard</h2>
    <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i> <?= date('F j, Y') ?></span>
</div>

<!-- Stats Widgets -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card glass-card border-0 h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-user-graduate fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Students</h6>
                    <h3 class="fw-bold mb-0"><?= number_format($stats['students']) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card glass-card border-0 h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-chalkboard-teacher fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Teachers</h6>
                    <h3 class="fw-bold mb-0"><?= number_format($stats['teachers']) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card glass-card border-0 h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-book fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Courses</h6>
                    <h3 class="fw-bold mb-0"><?= number_format($stats['courses']) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card glass-card border-0 h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                    <i class="fas fa-building fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Departments</h6>
                    <h3 class="fw-bold mb-0"><?= number_format($stats['departments']) ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Chart Section -->
    <div class="col-lg-8">
        <div class="card glass-card border-0">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h5 class="fw-bold">System Overview</h5>
            </div>
            <div class="card-body">
                <canvas id="overviewChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Recent Notices -->
    <div class="col-lg-4">
        <div class="card glass-card border-0 h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Recent Notices</h5>
                <a href="notices.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php
                try {
                    $stmt = $pdo->query("SELECT title, created_at FROM notices ORDER BY id DESC LIMIT 4");
                    $notices = $stmt->fetchAll();
                    
                    if (count($notices) > 0) {
                        echo '<ul class="list-group list-group-flush">';
                        foreach ($notices as $notice) {
                            $date = date('M j, Y', strtotime($notice['created_at']));
                            echo "<li class='list-group-item bg-transparent px-0 border-bottom border-secondary-subtle'>
                                    <div class='fw-medium'>".htmlspecialchars($notice['title'])."</div>
                                    <small class='text-muted'><i class='far fa-clock me-1'></i> $date</small>
                                  </li>";
                        }
                        echo '</ul>';
                    } else {
                        echo '<p class="text-muted mb-0">No notices posted yet.</p>';
                    }
                } catch (PDOException $e) {}
                ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('overviewChart').getContext('2d');
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#e0e0e0' : '#333';
    const gridColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.05)';

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Students', 'Teachers', 'Courses', 'Departments'],
            datasets: [{
                label: 'Count',
                data: [<?= $stats['students'] ?>, <?= $stats['teachers'] ?>, <?= $stats['courses'] ?>, <?= $stats['departments'] ?>],
                backgroundColor: [
                    'rgba(74, 144, 226, 0.7)',
                    'rgba(46, 204, 113, 0.7)',
                    'rgba(243, 156, 18, 0.7)',
                    'rgba(23, 162, 184, 0.7)'
                ],
                borderColor: [
                    '#4a90e2',
                    '#2ecc71',
                    '#f39c12',
                    '#17a2b8'
                ],
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                },
                x: {
                    ticks: { color: textColor },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>

<?php require_once 'layout_footer.php'; ?>
