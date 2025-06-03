<?php
include "includes/db_conn.php";
include "includes/header.php";


$clinic_name = 'ANICARE Veterinary Clinic';
$clinic_address = '123 Pet Street, Petville';
$clinic_phone = '(123) 456-7890';
$clinic_email = 'info@anicare.com';
$clinic_hours = '9:00 AM - 6:00 PM';


if (isset($_POST['save_settings'])) {
   
    $clinic_name = $_POST['clinic_name'];
    $clinic_address = $_POST['clinic_address'];
    $clinic_phone = $_POST['clinic_phone'];
    $clinic_email = $_POST['clinic_email'];
    $clinic_hours = $_POST['clinic_hours'];
    
    $success_message = 'Settings updated successfully!';
}
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-cog"></i> Settings</h1>
            <p class="text-muted">Configure system preferences and clinic information</p>
        </div>
    </div>

    <?php if(isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
       
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="#clinic" class="list-group-item list-group-item-action active" data-bs-toggle="list">Clinic Information</a>
                        <a href="#users" class="list-group-item list-group-item-action" data-bs-toggle="list">User Management</a>
                        <a href="#email" class="list-group-item list-group-item-action" data-bs-toggle="list">Email Templates</a>
                        <a href="#backup" class="list-group-item list-group-item-action" data-bs-toggle="list">Backup & Restore</a>
                        <a href="#system" class="list-group-item list-group-item-action" data-bs-toggle="list">System Settings</a>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="col-md-9">
            <div class="tab-content">
                
                <div class="tab-pane fade show active" id="clinic">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Clinic Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="post" action="">
                                <div class="mb-3">
                                    <label for="clinic_name" class="form-label">Clinic Name</label>
                                    <input type="text" class="form-control" id="clinic_name" name="clinic_name" value="<?php echo $clinic_name; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="clinic_address" class="form-label">Address</label>
                                    <textarea class="form-control" id="clinic_address" name="clinic_address" rows="2" required><?php echo $clinic_address; ?></textarea>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="clinic_phone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" id="clinic_phone" name="clinic_phone" value="<?php echo $clinic_phone; ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="clinic_email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="clinic_email" name="clinic_email" value="<?php echo $clinic_email; ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="clinic_hours" class="form-label">Operating Hours</label>
                                    <input type="text" class="form-control" id="clinic_hours" name="clinic_hours" value="<?php echo $clinic_hours; ?>" required>
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" name="save_settings" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                
                <div class="tab-pane fade" id="users">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">User Management</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Manage system users and permissions.</p>
                            
                            <div class="alert alert-info">User management feature is under development.</div>
                        </div>
                    </div>
                </div>
                
                <div class="tab-pane fade" id="email">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Email Templates</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Configure email templates for appointment reminders, etc.</p>
                            
                            <div class="alert alert-info">Email template feature is under development.</div>
                        </div>
                    </div>
                </div>
                
                <div class="tab-pane fade" id="backup">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Backup & Restore</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Backup your data or restore from a previous backup.</p>
                            
                            <div class="alert alert-info">Backup feature is under development.</div>
                        </div>
                    </div>
                </div>
                
                <div class="tab-pane fade" id="system">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">System Settings</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Configure system-wide settings and preferences.</p>
                           
                            <div class="alert alert-info">System settings feature is under development.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
       
        var triggerTabList = [].slice.call(document.querySelectorAll('.list-group-item'))
        triggerTabList.forEach(function (triggerEl) {
            triggerEl.addEventListener('click', function (e) {
                e.preventDefault()
                triggerTabList.forEach(function(el) {
                    el.classList.remove('active');
                });
                this.classList.add('active');
            })
        })
    });
</script>

<?php include "includes/footer.php"; ?>