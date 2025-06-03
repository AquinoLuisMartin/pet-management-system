<?php
include "includes/db_conn.php";
include "includes/header.php";

// Get payment data using stored procedure
$stmt = $conn->prepare("CALL GetAllPayments()");
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Get total revenue using stored procedure
$stmt = $conn->prepare("CALL GetTotalRevenue()");
$stmt->execute();
$revenue_result = $stmt->get_result();
$total_revenue = mysqli_fetch_assoc($revenue_result)['TotalRevenue'] ?? 0;
$stmt->close();

// Get pending revenue using stored procedure
$stmt = $conn->prepare("CALL GetPendingRevenue()");
$stmt->execute();
$pending_result = $stmt->get_result();
$pending_revenue = mysqli_fetch_assoc($pending_result)['PendingRevenue'] ?? 0;
$stmt->close();

// Get today's payments using stored procedure
$today = date('Y-m-d');
$stmt = $conn->prepare("CALL GetTodayPayments(?)");
$stmt->bind_param("s", $today);
$stmt->execute();
$today_result = $stmt->get_result();
$today_data = mysqli_fetch_assoc($today_result);
$today_count = $today_data['count'] ?? 0;
$today_total = $today_data['total'] ?? 0;
$stmt->close();
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-credit-card"></i> Payments</h1>
            <p class="text-muted">Manage payment records and billing</p>
        </div>
        <div class="col-auto">
            <button type="button" id="newPaymentBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                <i class="fas fa-plus"></i> New Payment
            </button>
        </div>
    </div>

    <!-- Payment Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Revenue</h6>
                            <h2 class="mb-0">$<?php echo number_format($total_revenue, 2); ?></h2>
                        </div>
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Pending Payments</h6>
                            <h2 class="mb-0">$<?php echo number_format($pending_revenue, 2); ?></h2>
                        </div>
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Today's Payments</h6>
                            <h2 class="mb-0">$<?php echo number_format($today_total, 2); ?></h2>
                            <small><?php echo $today_count; ?> payments today</small>
                        </div>
                        <i class="fas fa-calendar-day fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter/Search Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3">
                <div class="col-md-3">
                    <label for="dateFilter" class="form-label">Payment Date</label>
                    <input type="date" class="form-control" id="dateFilter">
                </div>
                <div class="col-md-3">
                    <label for="methodFilter" class="form-label">Payment Method</label>
                    <select class="form-control" id="methodFilter">
                        <option value="">All Methods</option>
                        <option value="Cash">Cash</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Debit Card">Debit Card</option>
                        <option value="Insurance">Insurance</option>
                        <option value="Mobile Payment">Mobile Payment</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-control" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                        <option value="Refunded">Refunded</option>
                        <option value="Failed">Failed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="searchFilter" class="form-label">Search</label>
                    <input type="text" class="form-control" id="searchFilter" placeholder="Search owner or pet...">
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Records Table -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Invoice #</th>
                            <th>Pet</th>
                            <th>Owner</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$row['PaymentID']}</td>";
                                echo "<td>INV-" . str_pad($row['PaymentID'], 6, '0', STR_PAD_LEFT) . "</td>";
                                echo "<td>{$row['PetName']}</td>";
                                echo "<td>{$row['OwnerName']}</td>";
                                echo "<td>" . date('M d, Y', strtotime($row['PaymentDate'])) . "</td>";
                                echo "<td>$" . number_format($row['Amount'], 2) . "</td>";
                                echo "<td>{$row['PaymentMethod']}</td>";
                                echo "<td><span class='badge " . 
                                      ($row['Status'] == 'Paid' ? 'bg-success' : 
                                       ($row['Status'] == 'Pending' ? 'bg-warning' : 
                                       ($row['Status'] == 'Refunded' ? 'bg-info' : 'bg-danger'))) . 
                                      "'>{$row['Status']}</span></td>";
                                echo "<td>
                                        <button type='button' class='btn btn-sm btn-outline-primary receipt-btn' data-id='{$row['PaymentID']}' title='Generate Receipt'><i class='fas fa-file-invoice'></i></button>
                                        <button type='button' class='btn btn-sm btn-outline-secondary edit-btn' data-id='{$row['PaymentID']}' title='Edit'><i class='fas fa-edit'></i></button>
                                        <button type='button' class='btn btn-sm btn-outline-danger delete-btn' data-id='{$row['PaymentID']}' title='Delete'><i class='fas fa-trash'></i></button>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9' class='text-center'>No payment records found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Payment Modal -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPaymentModalLabel">Add New Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Payment form fields -->
                    <form id="paymentForm" action="payment_process.php" method="post">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="form-group mb-3">
                            <select class="form-select" id="appointmentID" name="appointmentID" required>
                                <option value="">-- Select a pet --</option>
                                <?php
                                // Fetch pets for dropdown
                                $stmt = $conn->prepare("CALL GetPetsWithOwners()");
                                $stmt->execute();
                                $result = $stmt->get_result();
                                
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . $row['PetID'] . '">' . $row['PetName'] . ' - Owner: ' . $row['OwnerName'] . '</option>';
                                }
                                $stmt->close();
                                $conn->next_result(); // Clear result set
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <input type="date" class="form-control" id="paymentDate" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            
                            <select class="form-select" id="paymentMethod" name="paymentMethod" required>
                                <option value="">-- Select method --</option>
                                <option value="Cash">Cash</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Debit Card">Debit Card</option>
                                <option value="Mobile Payment">Mobile Payment</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">

                            <select class="form-select" id="status" name="status" required>
                                <option value="">-- Select status --</option>
                                <option value="Paid">Paid</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional Notes"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="paymentForm" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Search and filter functionality
        $("#searchFilter").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("table tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        
        // Method filter
        $("#methodFilter").on("change", function() {
            var value = $(this).val().toLowerCase();
            if (value === "") {
                $("table tbody tr").show();
            } else {
                $("table tbody tr").filter(function() {
                    $(this).toggle($(this).find("td:nth-child(7)").text().toLowerCase().indexOf(value) > -1)
                });
            }
        });
        
        // Status filter
        $("#statusFilter").on("change", function() {
            var value = $(this).val().toLowerCase();
            if (value === "") {
                $("table tbody tr").show();
            } else {
                $("table tbody tr").filter(function() {
                    $(this).toggle($(this).find("td:nth-child(8)").text().toLowerCase().indexOf(value) > -1)
                });
            }
        });
        
        // Date filter
        $("#dateFilter").on("change", function() {
            var value = $(this).val();
            if (value === "") {
                $("table tbody tr").show();
            } else {
                // Format the selected date for comparison
                var selectedDate = new Date(value).toLocaleDateString('en-US', {
                    month: 'short', 
                    day: '2-digit',
                    year: 'numeric'
                });
                
                $("table tbody tr").filter(function() {
                    var rowDate = $(this).find("td:nth-child(5)").text().trim();
                    return $(this).toggle(rowDate === selectedDate);
                });
            }
        });
        
        // Edit payment button click
        $(".edit-btn").on("click", function() {
            var paymentId = $(this).data("id");
            $("#addPaymentModalLabel").text("Edit Payment");
            
            // Reset the form first
            $("#paymentForm")[0].reset();
            
            // Add hidden payment ID field and change form action
            if(!$("#payment_id").length) {
                $("#paymentForm").append('<input type="hidden" id="payment_id" name="payment_id">');
            }
            $("#payment_id").val(paymentId);
            $("#paymentForm input[name='action']").val("edit");
            
            // Fetch payment data via AJAX
            $.ajax({
                url: "payment_process.php",
                type: "GET",
                data: {
                    action: "get_payment",
                    id: paymentId
                },
                dataType: "json",
                success: function(data) {
                    if(data) {
                        // Populate form fields with existing data
                        $("#appointmentID").val(data.AppointmentID);
                        $("#amount").val(data.Amount);
                        $("#paymentDate").val(data.PaymentDate);
                        $("#paymentMethod").val(data.PaymentMethod);
                        $("#status").val(data.Status);
                        $("#notes").val(data.Notes);
                        
                        var modal = new bootstrap.Modal(document.getElementById('addPaymentModal'));
                        modal.show();
                    } else {
                        alert("Failed to load payment data.");
                    }
                },
                error: function() {
                    alert("An error occurred while fetching payment data.");
                }
            });
        });
        
        // Delete payment button click
        $(".delete-btn").on("click", function() {
            var paymentId = $(this).data("id");
            if(confirm("Are you sure you want to delete this payment record?")) {
                // You can implement AJAX to delete the payment
                // Example: window.location.href = "payment_process.php?action=delete&id=" + paymentId;
            }
        });
        
        // Generate receipt button click
        $(".receipt-btn").on("click", function() {
            var paymentId = $(this).data("id");
            window.open("generate_receipt.php?id=" + paymentId, "_blank");
        });
    });
    </script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make sure "New Payment" button works correctly
    document.getElementById('newPaymentBtn')?.addEventListener('click', function() {
        document.getElementById('addPaymentModalLabel').textContent = 'Add New Payment';
    });
});
</script>

<?php include "includes/footer.php"; ?>