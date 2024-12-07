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
        <input type="text" class =searchBox id="searchBoxEmployees" onkeyup="searchEmployees('employeesTable')" placeholder="Search for employees..">
        <table border="1" id="employeesTable">
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
<div class="collapsible-section">
    <button type="button" class="collapse-toggle" onclick="toggleSection('add-employee')">
        <span class="toggle-icon">▼</span> Add New Employee
    </button>
    <div id="add-employee" class="collapsible-content">
        <form method="POST" action="create_employee.php" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" required onblur="capitalize(this)">
            </div>
            
            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" required onblur="capitalize(this)">
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
                <input type="text" id="contactInfo" name="contactInfo" placeholder="Phone number or email" onblur="formatPhoneNumber(this)">
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

<!-- Add to employees_panel.php after the tables but before the Add Employee section -->
<div class="button-group">
    <button type="button" class="action-button" onclick="showPasswordResetModal()">Reset Employee Password</button>
    <button type="button" class="remove-button" onclick="showRemoveEmployeeModal()">Remove Employee</button>
</div>

<!-- Add Password Reset Modal -->
<div id="passwordResetModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closePasswordResetModal()">&times;</span>
        <h3>Reset Employee Password</h3>
        <form method="POST" action="reset_password.php" id="passwordResetForm">
            <div class="form-group">
                <label for="employee_select">Select Employee:</label>
                <select id="employee_select" name="employee_id" required>
                    <option value="">Select an employee...</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?php echo htmlspecialchars($employee['EmployeeID']); ?>">
                            <?php echo htmlspecialchars($employee['FirstName'] . ' ' . $employee['LastName'] . 
                                  ' (' . $employee['Username'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="text" id="new_password" name="new_password" value="password" required>
            </div>
            <button type="submit" class="reset-button">Reset Password</button>
        </form>
    </div>
</div>

<!-- Add Remove Employee Modal -->
<div id="removeEmployeeModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeRemoveEmployeeModal()">&times;</span>
        <h3>Remove Employee</h3>
        <form method="POST" action="remove_employee.php" id="removeEmployeeForm">
            <div class="form-group">
                <label for="remove_employee_select">Select Employee:</label>
                <select id="remove_employee_select" name="employee_id" required>
                    <option value="">Select an employee...</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?php echo htmlspecialchars($employee['EmployeeID']); ?>">
                            <?php echo htmlspecialchars($employee['FirstName'] . ' ' . $employee['LastName'] . 
                                  ' (' . $employee['Username'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="removeEmployeeError" style="color: red; display: none;">Error: You cannot remove yourself.</div>
            <button type="submit" class="reset-button">Remove Employee</button>
        </form>
    </div>
</div>

<script>
function toggleContactInfo(role) {
    const isMover = role === 'Mover';
    document.getElementById('contactInfoGroup').style.display = isMover ? 'block' : 'none';
    document.getElementById('otherDetailsGroup').style.display = isMover ? 'block' : 'none';
}

function capitalize(input) {
    input.value = input.value.replace(/\b\w/g, function(char) {
        return char.toUpperCase();
    });
}

function formatPhoneNumber(input) {
    const phone = input.value.replace(/\D/g, '');
    if (phone.length === 10) {
        input.value = phone.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
    }
}

function validateForm() {
    const firstName = document.getElementById('firstName');
    const lastName = document.getElementById('lastName');
    const contactInfo = document.getElementById('contactInfo');

    capitalize(firstName);
    capitalize(lastName);
    formatPhoneNumber(contactInfo);

    return true;
}

function toggleSection(id) {
    const section = document.getElementById(id).parentElement;
    section.classList.toggle('collapsed');
}

function showPasswordResetModal() {
    document.getElementById('passwordResetModal').style.display = 'block';
}

function closePasswordResetModal() {
    document.getElementById('passwordResetModal').style.display = 'none';
}

function showRemoveEmployeeModal() {
    document.getElementById('removeEmployeeModal').style.display = 'block';
}

function closeRemoveEmployeeModal() {
    document.getElementById('removeEmployeeModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target == document.getElementById('passwordResetModal')) {
        closePasswordResetModal();
    }
}

// Handle form submission for removing employee
document.getElementById('removeEmployeeForm').addEventListener('submit', function(event) {
    const selectedEmployeeId = document.getElementById('remove_employee_select').value;
    const currentUserId = '<?php echo $_SESSION['user_id']; ?>';

    if (selectedEmployeeId === currentUserId) {
        event.preventDefault();
        document.getElementById('removeEmployeeError').style.display = 'block';
    }
});

// Initialize sections as collapsed
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.collapsible-section');
    sections.forEach(section => {
        section.classList.add('collapsed');
    });
});

function searchEmployees(tableId) {
    const input = document.getElementById('searchBoxEmployees');
    const filter = input.value.toLowerCase();
    const table = document.getElementById(tableId);
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        const tdArray = tr[i].getElementsByTagName('td');
        let found = false;
        for (let j = 0; j < tdArray.length; j++) {
            if (tdArray[j]) {
                if (tdArray[j].innerHTML.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        tr[i].style.display = found ? '' : 'none';
    }
}



</script>

