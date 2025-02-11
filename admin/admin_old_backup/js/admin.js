// Modal handling
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Form submissions
async function submitForm(formId, action) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);

    try {
        const response = await fetch(action, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert(result.message || 'An error occurred');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while processing your request');
    }
}

// Edit functions
function editTeam(id, name, logo) {
    document.getElementById('edit_team_id').value = id;
    document.getElementById('edit_team_name').value = name;
    document.getElementById('edit_team_logo').value = logo || '';
    openModal('editTeamModal');
}

function editLeague(id, name, flag) {
    document.getElementById('edit_league_id').value = id;
    document.getElementById('edit_league_name').value = name;
    document.getElementById('edit_league_flag').value = flag || '';
    openModal('editLeagueModal');
}

function editMatch(id, homeTeam, awayTeam, league, time, stream) {
    document.getElementById('edit_match_id').value = id;
    document.getElementById('edit_home_team').value = homeTeam;
    document.getElementById('edit_away_team').value = awayTeam;
    document.getElementById('edit_league').value = league;
    document.getElementById('edit_match_time').value = time;
    document.getElementById('edit_stream_url').value = stream;
    openModal('editMatchModal');
}

// Delete functions
async function deleteItem(id, type) {
    if (!confirm(`Are you sure you want to delete this ${type}?`)) {
        return;
    }

    try {
        const response = await fetch(`actions/delete_${type}.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}`
        });

        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert(result.message || 'An error occurred');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while deleting');
    }
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            const table = document.querySelector('.data-table table');
            const rows = table.getElementsByTagName('tr');

            Array.from(rows).forEach((row, index) => {
                if (index === 0) return; // Skip header row
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });
        });
    }
});
