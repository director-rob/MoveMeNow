<?php
require_once 'db.php';

$query = 'SELECT * FROM Movers';
$stmt = $db->prepare($query);
$stmt->execute();
$movers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Movers</h2>
<div class="collapsible-section">
    <button type="button" class="collapse-toggle" onclick="toggleSection('movers-table')">
        <span class="toggle-icon">▼</span> View Movers
    </button>
    <div id="movers-table" class="collapsible-content">
        <input type="text" class="searchBox" id="searchBox" onkeyup="searchMovers()" placeholder="Search for movers..">
        <table border="1" id="moversTable">
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
    </div>
</div>

<script>
function toggleSection(id) {
    const section = document.getElementById(id).parentElement;
    section.classList.toggle('collapsed');
}

// Initialize sections as collapsed
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.collapsible-section');
    sections.forEach(section => {
        section.classList.add('collapsed');
    });
});

function searchMovers() {
    const input = document.getElementById('searchBox');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('moversTable');
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