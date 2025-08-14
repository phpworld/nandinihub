<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= esc($order['order_number']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #333;
            background: white;
            margin: 0;
            padding: 0;
        }

        .invoice-container {
            max-width: 100%;
            margin: 0;
            background: white;
            padding: 15px;
        }

        .company-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            border-bottom: 2px solid #ff6b35;
            padding-bottom: 10px;
            padding: 10px;
        }

        .company-logo {
            flex: 0 0 auto;
            margin-right: 20px;
        }

        .company-logo img {
            max-height: 40px;
            max-width: 80px;
            object-fit: contain;
        }

        .company-details {
            flex: 1;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #ff6b35;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border-left: 3px solid #ff6b35;
            background: #f8f9fa;
        }

        .invoice-meta {
            display: table-cell;
            background: #fff;
            padding: 8px;
            border: 1px solid #e9ecef;
            width: 50%;
            vertical-align: top;
        }

        .bill-to {
            display: table-cell;
            background: #fff;
            padding: 8px;
            border: 1px solid #e9ecef;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #ff6b35;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .address-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .address-section > div {
            flex: 1;
            margin-right: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .address-section > div:last-child {
            margin-right: 0;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            border: 1px solid #e9ecef;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #e9ecef;
            padding: 5px;
            text-align: left;
            vertical-align: top;
            font-size: 9px;
        }

        .invoice-table th {
            background: #ff6b35;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.5px;
        }

        .invoice-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .invoice-total {
            border-top: 2px solid #ff6b35;
            font-weight: bold;
            font-size: 10px;
            background-color: #fff3cd;
        }

        .invoice-footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 2px solid #ff6b35;
            padding: 10px;
        }

        .invoice-footer p {
            margin-bottom: 2px;
        }

        .footer-highlight {
            color: #ff6b35;
            font-weight: bold;
        }

        .payment-info-section {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .payment-info-box {
            flex: 1;
            background: #f8f9fa;
            padding: 8px;
            border-radius: 8px;
            border-left: 5px solid #17a2b8;
        }

        .order-notes-box {
            flex: 1;
            background: #f8f9fa;
            padding: 8px;
            border-left: 3px solid #6f42c1;
        }

        h4, h5 {
            margin-bottom: 5px;
            color: #333;
            font-size: 10px;
        }

        address {
            font-style: normal;
            line-height: 1.3;
        }

        p {
            margin-bottom: 3px;
        }

        strong {
            font-weight: bold;
        }

        .invoice-title {
            font-size: 16px;
            color: #ff6b35;
            margin: 0 0 8px 0;
            font-weight: bold;
        }

        .bill-to h4 {
            font-size: 12px;
            color: #ff6b35;
            margin: 0 0 8px 0;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <!-- Company Header -->
        <div class="company-info">
            <?php if (!empty($siteLogo)): ?>
                <div class="company-logo">
                    <img src="<?= base_url($siteLogo) ?>" alt="<?= esc($siteName) ?> Logo">
                </div>
            <?php endif; ?>
            
            <!-- Company Details -->
            <div class="company-details">
                <div class="company-name"><?= esc($siteName) ?></div>
            </div>
        </div>

        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="invoice-meta">
                <h2 class="invoice-title">INVOICE</h2>
                <p><strong>Invoice #:</strong> <?= esc($order['order_number']) ?></p>
                <p><strong>Invoice Date:</strong> <?= date('F j, Y', strtotime($order['created_at'])) ?></p>
            </div>
            <div class="bill-to">
                <h4>Bill To:</h4>
                <p>
                    <strong><?= esc($order['first_name'] . ' ' . $order['last_name']) ?></strong><br>
                    📧 <?= esc($order['email']) ?><br>
                    <?php if (!empty($order['phone'])): ?>
                        📞 <?= esc($order['phone']) ?><br>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Shipping Address -->
        <?php if (!empty($order['shipping_address'])): ?>
            <div class="address-section" style="margin-bottom: 10px; padding: 8px; background: #f8f9fa; border-left: 3px solid #ff6b35;">
                <h5 style="margin: 0 0 5px 0; font-size: 12px; color: #ff6b35;">📦 Shipping Address:</h5>
                <address style="margin: 0; font-style: normal; font-size: 9px; line-height: 1.3;">
                    <?php
                    // Try to decode as JSON first, if that fails, treat as plain text
                    $shippingAddress = json_decode($order['shipping_address'], true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        // It's a formatted string, display as is
                        echo nl2br(esc($order['shipping_address']));
                    } else {
                        // It's JSON data, display structured
                        if (!empty($shippingAddress['full_name'])) {
                            echo '<strong>' . esc($shippingAddress['full_name']) . '</strong><br>';
                        }
                        if (!empty($shippingAddress['address_line1'])) {
                            echo esc($shippingAddress['address_line1']) . '<br>';
                        }
                        if (!empty($shippingAddress['address_line2'])) {
                            echo esc($shippingAddress['address_line2']) . '<br>';
                        }
                        if (!empty($shippingAddress['landmark'])) {
                            echo 'Near ' . esc($shippingAddress['landmark']) . '<br>';
                        }
                        if (!empty($shippingAddress['city']) || !empty($shippingAddress['state']) || !empty($shippingAddress['pincode'])) {
                            echo esc($shippingAddress['city'] ?? '');
                            if (!empty($shippingAddress['city']) && (!empty($shippingAddress['state']) || !empty($shippingAddress['pincode']))) echo ', ';
                            echo esc($shippingAddress['state'] ?? '');
                            if (!empty($shippingAddress['state']) && !empty($shippingAddress['pincode'])) echo ' ';
                            echo esc($shippingAddress['pincode'] ?? '') . '<br>';
                        }
                        if (!empty($shippingAddress['country']) && $shippingAddress['country'] !== 'India') {
                            echo esc($shippingAddress['country']) . '<br>';
                        }
                        if (!empty($shippingAddress['phone'])) {
                            echo '📞 ' . esc($shippingAddress['phone']);
                        }
                    }
                    ?>
                </address>
            </div>
        <?php endif; ?>

        <!-- Invoice Items Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Product Description</th>
                    <th style="width: 15%;">SKU</th>
                    <th style="width: 10%;" class="text-right">Price</th>
                    <th style="width: 10%;" class="text-right">Qty</th>
                    <th style="width: 15%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td>
                            <strong><?= esc($item['product_name']) ?></strong>
                            <?php if (!empty($item['product_description'])): ?>
                                <br><small style="color: #666;"><?= esc($item['product_description']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($item['product_sku'] ?? 'N/A') ?></td>
                        <td class="text-right">₹<?= number_format($item['price'], 2) ?></td>
                        <td class="text-right"><?= $item['quantity'] ?></td>
                        <td class="text-right">₹<?= number_format($item['total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                    <td class="text-right">₹<?= number_format($order['subtotal_amount'] ?? 0, 2) ?></td>
                </tr>
                <?php if (!empty($order['tax_amount']) && $order['tax_amount'] > 0): ?>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Tax:</strong></td>
                        <td class="text-right">₹<?= number_format($order['tax_amount'] ?? 0, 2) ?></td>
                    </tr>
                <?php endif; ?>
                <?php if (!empty($order['shipping_amount']) && $order['shipping_amount'] > 0): ?>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Shipping:</strong></td>
                        <td class="text-right">₹<?= number_format($order['shipping_amount'] ?? 0, 2) ?></td>
                    </tr>
                <?php endif; ?>
                <?php if (!empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Discount:</strong></td>
                        <td class="text-right">-₹<?= number_format($order['discount_amount'] ?? 0, 2) ?></td>
                    </tr>
                <?php endif; ?>
                <tr class="invoice-total">
                    <td colspan="4" class="text-right"><strong>TOTAL AMOUNT:</strong></td>
                    <td class="text-right"><strong>₹<?= number_format($order['total_amount'] ?? 0, 2) ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <!-- Order Information -->
        <div class="payment-info-section">
            <!-- Order Information -->
            <div class="payment-info-box">
                <h5 style="color: #17a2b8; margin-bottom: 3px; font-size: 9px;">📦 Order Information</h5>
                <?php if (!empty($order['shipping_method_name'])): ?>
                    <p style="font-size: 8px; margin-bottom: 1px;"><strong>Shipping:</strong> <?= esc($order['shipping_method_name']) ?></p>
                    <p style="font-size: 8px; margin-bottom: 1px;"><strong>Delivery:</strong> <?= esc($order['shipping_delivery_time']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Order Notes -->
            <?php if (!empty($order['notes'])): ?>
                <div class="order-notes-box">
                    <h5 style="color: #6f42c1; margin-bottom: 3px; font-size: 9px;">📝 Order Notes</h5>
                    <p style="font-size: 8px;"><?= nl2br(esc($order['notes'])) ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Invoice Footer -->
        <div class="invoice-footer">
            <p class="footer-highlight">🙏 Thank you for choosing <?= esc($siteName) ?>!</p>
            <p>This is a computer-generated invoice and does not require a physical signature.</p>
            <p>📞 For any queries or support, please contact us:</p>
            <p>
                📧 <strong>Email:</strong> <?= esc($contactEmail) ?> | 
                📞 <strong>Phone:</strong> <?= esc($contactPhone) ?> | 
                🌐 <strong>Website:</strong> www.nandinihub.com
            </p>
            <hr style="margin: 15px 0; border: 1px solid #ff6b35;">
            <p><strong><?= esc($siteName) ?></strong> - <em><?= esc($siteTagline) ?></em></p>
            <p style="font-size: 9px; color: #999;">Generated on <?= date('F j, Y \a\t g:i A') ?></p>
        </div>
    </div>
</body>

</html>
