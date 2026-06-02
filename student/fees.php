<?php
require_once 'layout_header.php';

$student_id = $_SESSION['user_id'];

// Fetch fee records
try {
    $stmt = $pdo->prepare("SELECT * FROM fees WHERE student_id = ? ORDER BY due_date DESC");
    $stmt->execute([$student_id]);
    $fees = $stmt->fetchAll();
    
    $totalDue = 0;
    foreach($fees as $f) {
        if ($f['status'] == 'Unpaid') $totalDue += $f['amount'];
    }
} catch (PDOException $e) {
    $fees = [];
    $totalDue = 0;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Fee Status</h2>
    <div class="glass-card px-4 py-2 text-center rounded-pill <?= ($totalDue > 0) ? 'border-danger' : 'border-success' ?>">
        <h5 class="mb-0 <?= ($totalDue > 0) ? 'text-danger' : 'text-success' ?> fw-bold">Total Due: $<?= number_format($totalDue, 2) ?></h5>
    </div>
</div>

<div class="card glass-card border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>Invoice ID</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Payment Date</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($fees) > 0): ?>
                        <?php foreach($fees as $f): ?>
                        <tr>
                            <td class="fw-medium text-muted">#INV-<?= str_pad($f['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td class="fw-bold">$<?= number_format($f['amount'], 2) ?></td>
                            <td><?= date('M j, Y', strtotime($f['due_date'])) ?></td>
                            <td><?= $f['payment_date'] ? date('M j, Y', strtotime($f['payment_date'])) : '-' ?></td>
                            <td>
                                <?php if($f['status'] == 'Paid'): ?>
                                    <span class="badge bg-success rounded-pill px-3"><i class="fas fa-check me-1"></i>Paid</span>
                                <?php else: ?>
                                    <span class="badge bg-danger rounded-pill px-3"><i class="fas fa-exclamation-circle me-1"></i>Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if($f['status'] == 'Unpaid'): ?>
                                    <button class="btn btn-sm btn-primary-custom" onclick="alert('Payment gateway integration required.')">Pay Now</button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-outline-secondary" disabled>Receipt</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted"><i class="fas fa-money-check fa-3x mb-3 opacity-50 d-block"></i> No fee records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'layout_footer.php'; ?>
