<?php
include "includes/db_conn.php";

if (!isset($_GET['id'])) {
    echo "Invalid payment ID";
    exit;
}

$payment_id = $_GET['id'];
    
$stmt = $conn->prepare("CALL GetPaymentDetailsById(?)");
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();
$payment = $result->fetch_assoc();
$stmt->close();

if (!$payment) {
    echo "Payment not found";
    exit;
}

$invoice_number = 'INV-' . str_pad($payment['PaymentID'], 6, '0', STR_PAD_LEFT);
$payment_date = date('M d, Y', strtotime($payment['PaymentDate']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #<?php echo $invoice_number; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .receipt {
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            border: 1px solid #ddd;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #28a745;
            margin-bottom: 30px;
        }
        .receipt-details {
            margin-bottom: 30px;
        }
        .customer-info {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .payment-details th, .payment-details td {
            padding: 12px 15px;
        }
        .total-row {
            background-color: #e9f7ef;
            font-weight: bold;
        }
        .notes {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .footer-text {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-style: italic;
            color: #6c757d;
        }
        .btn-action {
            margin: 5px;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                background-color: #fff;
            }
            .receipt {
                box-shadow: none;
                border: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container receipt">
        <div class="header">
            <h2><i class="fas fa-paw text-success"></i> Pet Management System</h2>
            <p>123 Pet Avenue, Manila, Philippines</p>
            <p>Email: info@petmanagementsystem.ph | Phone: +63 (2) 8888-1234</p>
            <h2 class="text-success mt-4">OFFICIAL RECEIPT</h2>
        </div>
        
        <div class="row receipt-details">
            <div class="col-md-6">
                <strong>Invoice Number:</strong> <?php echo $invoice_number; ?><br>
                <strong>Payment Status:</strong> 
                <span class="badge <?php echo ($payment['Status'] == 'Paid') ? 'bg-success' : 'bg-warning'; ?>">
                    <?php echo $payment['Status']; ?>
                </span><br>
                <strong>Payment Method:</strong> <?php echo $payment['PaymentMethod']; ?>
            </div>
            <div class="col-md-6 text-md-end">
                <strong>Date:</strong> <?php echo $payment_date; ?>
            </div>
        </div>
        
        <div class="customer-info">
            <h5><i class="fas fa-user me-2"></i>Customer Information</h5>
            <div class="row">
                <div class="col-md-6">
                    <strong>Pet Owner:</strong> <?php echo $payment['OwnerName']; ?><br>
                    <strong>Pet:</strong> <?php echo $payment['PetName']; ?>
                </div>
            </div>
        </div>
        
        <h5><i class="fas fa-file-invoice-dollar me-2"></i>Payment Details</h5>
        <table class="table table-bordered payment-details">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Veterinary Services</td>
                    <td class="text-end">₱<?php echo number_format($payment['Amount'], 2); ?></td>
                    <td class="text-end">₱<?php echo number_format($payment['Amount'], 2); ?></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td class="text-end"><strong>Subtotal:</strong></td>
                    <td class="text-end">₱<?php echo number_format($payment['Amount'], 2); ?></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td class="text-end"><strong>Tax:</strong></td>
                    <td class="text-end">₱0.00</td>
                </tr>
                <tr class="total-row">
                    <td colspan="2"></td>
                    <td class="text-end"><strong>Total:</strong></td>
                    <td class="text-end">₱<?php echo number_format($payment['Amount'], 2); ?></td>
                </tr>
            </tbody>
        </table>
        
        <?php if (!empty($payment['Notes'])): ?>
        <div class="notes">
            <h5><i class="fas fa-sticky-note me-2"></i>Notes:</h5>
            <p><?php echo nl2br(htmlspecialchars($payment['Notes'])); ?></p>
        </div>
        <?php endif; ?>
        
        <div class="text-center footer-text">
            <p>Thank you for choosing Pet Management System for your pet healthcare needs.</p>
            <p>This is a computer-generated receipt and does not require a signature.</p>
        </div>
        
        <div class="text-center mt-4 mb-3 no-print">
            <button onclick="window.print()" class="btn btn-primary btn-action">
                <i class="fas fa-print me-2"></i>Print Receipt
            </button>
            <button onclick="window.close()" class="btn btn-secondary btn-action">
                <i class="fas fa-times me-2"></i>Close
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>