<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}
require_once 'db.php';
$db = getDB();
$errors = $db->query("SELECT * FROM errors ORDER BY timestamp DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'templates/header.php'; ?>
<h2 class="mb-4">Shoulder Press Error Dashboard</h2>
<table class="table table-striped">
    <thead>
    <tr>
        <th>Timestamp</th>
        <th>Error Type</th>
        <th>Details</th>
        <th>Video</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
<?php foreach($errors as $err): ?>
    <tr>
        <td><?=htmlspecialchars($err['timestamp'])?></td>
        <td><?=htmlspecialchars($err['error_type'])?></td>
        <td><?=htmlspecialchars($err['details'])?></td>
        <td>
            <video width="240" controls>
                <source src="<?=htmlspecialchars($err['filename'])?>" type="video/mp4">
            </video>
        </td>
        <td>
            <?= $err['visualized'] ? "<span class='badge bg-success'>Reviewed</span>" : "<span class='badge bg-warning text-dark'>New</span>" ?>
        </td>
        <td>
            <?php if(!$err['visualized']): ?>
            <form method="post" action="mark_reviewed.php">
                <input type="hidden" name="id" value="<?=$err['id']?>">
                <button class="btn btn-sm btn-outline-success">Mark as Reviewed</button>
            </form>
            <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php include 'templates/footer.php'; ?>
