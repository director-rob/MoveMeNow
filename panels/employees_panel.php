<?php
require_once 'db.php';

$query = 'SELECT * FROM Employees';
$stmt = $db->prepare($query);
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Employees</h2>
<table border="1">
    <thead>
        <tr>
            <th>Employee ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Role</th>
            <th>Username</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($employees)): ?>
            <?php foreach ($employees as $employee): ?>
                <tr>
                    <td><?php echo htmlspecialchars($employee['EmployeeID']); ?></td>
                    <td><?php echo htmlspecialchars($employee['FirstName']); ?></td>
                    <td><?php echo htmlspecialchars($employee['LastName']); ?></td>
                    <td><?php echo htmlspecialchars($employee['Role']); ?></td>
                    <td><?php echo htmlspecialchars($employee['Username']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No employees found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
