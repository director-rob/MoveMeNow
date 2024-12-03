<?php
require_once 'db.php';

$query = 'SELECT * FROM Movers';
$stmt = $db->prepare($query);
$stmt->execute();
$movers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Movers</h2>
<table border="1">
    <thead>
        <tr>
            <th>Mover ID</th>
            <th>Name</th>
            <th>Contact Info</th>
            <th>Other Details</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($movers)): ?>
            <?php foreach ($movers as $mover): ?>
                <tr>
                    <td><?php echo htmlspecialchars($mover['MoverID']); ?></td>
                    <td><?php echo htmlspecialchars($mover['Name']); ?></td>
                    <td><?php echo htmlspecialchars($mover['ContactInfo']); ?></td>
                    <td><?php echo htmlspecialchars($mover['OtherDetails']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No movers found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
