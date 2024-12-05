<?php
require_once 'db.php';

// Fetch employees with tenure calculation
$query = '
    SELECT 
        EmployeeID,
        FirstName,
        LastName,
        Role,
        Username,
        DateJoined,
        TIMESTAMPDIFF(YEAR, DateJoined, CURDATE()) AS Years,
        TIMESTAMPDIFF(MONTH, DateJoined, CURDATE()) % 12 AS Months
    FROM Employees
    ORDER BY DateJoined DESC
';
$stmt = $db->prepare($query);
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Employees</h2>
<div class="collapsible-section">
    <button type="button" class="collapse-toggle" onclick="toggleSection('employees-table')">
        <span class="toggle-icon">▼</span> View Employees
    </button>
    <div id="employees-table" class="collapsible-content">
        <table border="1">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Role</th>
                    <th>Username</th>
                    <th>Tenure</th>
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
                            <td><?php 
                                if ($employee['Years'] > 0) {
                                    echo htmlspecialchars($employee['Years'] . ' year' . ($employee['Years'] > 1 ? 's' : ''));
                                    if ($employee['Months'] > 0) {
                                        echo ' and ' . htmlspecialchars($employee['Months'] . ' month' . ($employee['Months'] > 1 ? 's' : ''));
                                    }
                                } else {
                                    echo htmlspecialchars($employee['Months'] . ' month' . ($employee['Months'] > 1 ? 's' : ''));
                                }
                            ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No employees found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add New Employee Form -->
<h3>Add New Employee</h3>
<div class="collapsible-section">
    <button type="button" class="collapse-toggle" onclick="toggleSection('add-employee')">
        <span class="toggle-icon">▼</span> Add New Employee
    </button>
    <div id="add-employee" class="collapsible-content">
        <form method="POST" action="create_employee.php">
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" required>
            </div>
            
            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" required>
            </div>
            
            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" required onchange="toggleContactInfo(this.value)">
                    <option value="Manager">Manager</option>
                    <option value="Mover">Mover</option>
                    <option value="Dispatcher">Dispatcher</option>
                </select>
            </div>
            
            <div class="form-group" id="contactInfoGroup" style="display: none;">
                <label for="contactInfo">Contact Info:</label>
                <input type="text" id="contactInfo" name="contactInfo" placeholder="Phone number or email">
            </div>
            
            <div class="form-group" id="otherDetailsGroup" style="display: none;">
                <label for="otherDetails">Other Details:</label>
                <textarea id="otherDetails" name="otherDetails" rows="3" 
                    placeholder="Experience, specialties, certifications, etc"></textarea>
            </div>
            
            <div class="form-group">
                <label for="dateJoined">Date Joined:</label>
                <input type="date" id="dateJoined" name="dateJoined" required>
            </div>
            
            <button type="submit">Add Employee</button>
        </form>
    </div>
</div>

<script>
function toggleContactInfo(role) {
    const isMover = role === 'Mover';
    const contactInfoGroup = document.getElementById('contactInfoGroup');
    const otherDetailsGroup = document.getElementById('otherDetailsGroup');
    
    contactInfoGroup.style.display = isMover ? 'block' : 'none';
    otherDetailsGroup.style.display = isMover ? 'block' : 'none';
    
    document.getElementById('contactInfo').required = isMover;
}

function toggleSection(id) {
    const section = document.getElementById(id).parentElement;
    section.classList.toggle('collapsed');
}

// Initialize sections as expanded
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.collapsible-section');
    sections.forEach(section => {
        section.classList.add('collapsed');
    });
});
</script>

