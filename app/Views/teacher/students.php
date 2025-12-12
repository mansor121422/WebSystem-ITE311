<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="mb-4">
                <h2>
                    <i class="fas fa-users"></i> Search Students
                </h2>
            </div>

            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Search Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="searchInput" class="form-label">
                                <i class="fas fa-search"></i> Search Students
                            </label>
                            <input 
                                type="text" 
                                class="form-control form-control-lg" 
                                id="searchInput" 
                                name="q" 
                                placeholder="Search by name or email..." 
                                value="<?= esc($search_term ?? '') ?>"
                                autocomplete="off"
                            >
                            <small class="form-text text-muted">Type to search students in real-time</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students List -->
            <div class="card" id="studentsCard" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-graduate"></i> 
                        <span id="studentCount">Search Results (0)</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-id-card"></i> ID</th>
                                    <th><i class="fas fa-user"></i> Name</th>
                                    <th><i class="fas fa-envelope"></i> Email</th>
                                    <th><i class="fas fa-book"></i> Total Enrollments</th>
                                    <th><i class="fas fa-check-circle"></i> Active Enrollments</th>
                                    <th><i class="fas fa-calendar"></i> Registered</th>
                                    <th class="text-center"><i class="fas fa-cog"></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentsTableBody">
                                <?php foreach ($students as $student): ?>
                                    <tr class="student-row" 
                                        data-student-name="<?= strtolower(esc($student['name'])) ?>" 
                                        data-student-email="<?= strtolower(esc($student['email'])) ?>"
                                        data-student-id="<?= esc($student['id']) ?>">
                                        <td><?= esc($student['id']) ?></td>
                                        <td>
                                            <strong><?= esc($student['name']) ?></strong>
                                        </td>
                                        <td><?= esc($student['email']) ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= $student['enrollment_count'] ?? 0 ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                <?= $student['active_enrollments'] ?? 0 ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if(!empty($student['created_at'])): ?>
                                                <?= date('M d, Y', strtotime($student['created_at'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('teacher/enroll-student?student_id=' . $student['id']) ?>" 
                                               class="btn btn-sm btn-primary" 
                                               title="Enroll in Course">
                                                <i class="fas fa-user-plus"></i> Enroll
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Empty state for filtered results (hidden by default) -->
                    <div class="text-center py-5" id="noResultsMessage" style="display: none;">
                        <i class="fas fa-search fa-4x text-muted mb-3"></i>
                        <p class="text-muted">No students found matching your search.</p>
                    </div>
                </div>
            </div>
            
            <!-- Initial message when no search -->
            <div class="card" id="initialMessage">
                <div class="card-body text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <p class="text-muted">Start typing to search for students by name or email.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 0.9rem;
        padding: 0.4em 0.6em;
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.9rem;
        }
        
        .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    }
</style>

<script>
// Real-time search filtering
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const studentRows = document.querySelectorAll('.student-row');
    const studentCount = document.getElementById('studentCount');
    const noResultsMessage = document.getElementById('noResultsMessage');
    const studentsCard = document.getElementById('studentsCard');
    const initialMessage = document.getElementById('initialMessage');
    const tableBody = document.getElementById('studentsTableBody');
    const tableContainer = tableBody ? tableBody.closest('.table-responsive') : null;
    
    if (!searchInput) return;
    
    // Real-time filtering as user types
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleCount = 0;
        
        // Show/hide initial message and students card
        if (searchTerm === '') {
            // No search term - hide results, show initial message
            if (studentsCard) studentsCard.style.display = 'none';
            if (initialMessage) initialMessage.style.display = 'block';
            return;
        } else {
            // Has search term - show results card, hide initial message
            if (studentsCard) studentsCard.style.display = 'block';
            if (initialMessage) initialMessage.style.display = 'none';
        }
        
        // Filter table rows
        studentRows.forEach(function(row) {
            const studentName = row.getAttribute('data-student-name') || '';
            const studentEmail = row.getAttribute('data-student-email') || '';
            
            // Check if search term matches name or email
            const matches = studentName.includes(searchTerm) || 
                           studentEmail.includes(searchTerm);
            
            if (matches) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update student count
        if (studentCount) {
            studentCount.textContent = `Search Results (${visibleCount} found)`;
        }
        
        // Show/hide empty state messages
        if (visibleCount === 0) {
            // Show "no results" message if no matches
            if (noResultsMessage) noResultsMessage.style.display = 'block';
            if (tableContainer) tableContainer.style.display = 'none';
        } else {
            // Hide "no results" message and show table
            if (noResultsMessage) noResultsMessage.style.display = 'none';
            if (tableContainer) tableContainer.style.display = '';
        }
    });
    
    // Initialize on page load if there's a search term
    if (searchInput.value) {
        searchInput.dispatchEvent(new Event('input'));
    }
});
</script>

<?= $this->endSection() ?>

