<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Invoice' ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: white;
            padding: 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
            position: relative;
            padding: 20px 20px 200px 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .company-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 3px solid #ff6b35;
            padding-bottom: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }

        .company-logo {
            flex: 0 0 auto;
            margin-right: 20px;
        }

        .company-logo img {
            max-height: 80px;
            max-width: 150px;
            object-fit: contain;
        }

        .company-details {
            flex: 1;
            text-align: left;
        }

        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #ff6b35;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .company-tagline {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            font-style: italic;
        }

        .company-contact {
            font-size: 12px;
            color: #555;
            line-height: 1.6;
        }

        .company-contact div {
            margin-bottom: 3px;
        }

        .invoice-header {
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 5px solid #ff6b35;
        }

        .invoice-header .row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #ff6b35;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .invoice-meta {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .invoice-meta p {
            margin-bottom: 8px;
            font-size: 13px;
        }

        .bill-to {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .bill-to h4 {
            color: #ff6b35;
            margin-bottom: 10px;
            font-size: 16px;
            border-bottom: 2px solid #ff6b35;
            padding-bottom: 5px;
        }

        .invoice-header .col {
            flex: 1;
        }

        .invoice-header .col:last-child {
            text-align: right;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .address-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .address-section>div {
            flex: 1;
            margin-right: 20px;
        }

        .address-section>div:last-child {
            margin-right: 0;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #e9ecef;
            padding: 12px;
            text-align: left;
            vertical-align: top;
        }

        .invoice-table th {
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }

        .invoice-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .invoice-table tbody tr:hover {
            background-color: #e9ecef;
        }

        .invoice-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-right {
            text-align: right;
        }

        .invoice-total {
            border-top: 2px solid #333;
            font-weight: bold;
            font-size: 14px;
        }

        .invoice-footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #666;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-top: 3px solid #ff6b35;
            padding: 20px;
            border-radius: 0 0 8px 8px;
            position: absolute;
            bottom: 0;
            left: 20px;
            right: 20px;
        }

        .invoice-footer p {
            margin-bottom: 8px;
        }

        .invoice-footer .footer-highlight {
            color: #ff6b35;
            font-weight: bold;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
            transition: all 0.3s ease;
        }

        .print-button:hover {
            background: linear-gradient(135deg, #e55a2b 0%, #ff7a32 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 107, 53, 0.4);
        }

        .invoice-total-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 5px solid #28a745;
            margin-top: 20px;
        }

        @media print {

            .print-button,
            div[style*="position: fixed"] {
                display: none !important;
            }

            body {
                padding: 0;
            }

            .invoice-container {
                padding: 20px;
                min-height: auto;
            }

            .invoice-footer {
                position: static;
                margin-top: 40px;
            }

            @page {
                margin: 0.5in;
                size: A4;
            }
        }

        h5 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        address {
            font-style: normal;
            line-height: 1.5;
        }

        p {
            margin-bottom: 5px;
        }

        strong {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <a href="<?= base_url('admin/orders/' . $order['id']) ?>" class="print-button" style="text-decoration: none; display: inline-block; margin-right: 10px; background: #6c757d;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <button class="print-button" onclick="window.print()" style="margin-right: 10px;">
            <i class="fas fa-print"></i> Print Invoice
        </button>
        <a href="<?= base_url('admin/orders/' . $order['id'] . '/pdf') ?>" class="print-button" style="text-decoration: none; display: inline-block;">
            <i class="fas fa-download"></i> Download PDF
        </a>
    </div>

    <div class="invoice-container">
        <!-- Company Header -->
        <div class="company-info">
            <?php
            $settingModel = new \App\Models\SettingModel();
            $siteName = $settingModel->getSetting('site_name', 'NANDINI HUB');
            $siteTagline = $settingModel->getSetting('site_tagline', 'Your Trusted Shopping Destination');
            $contactEmail = $settingModel->getSetting('contact_email', 'info@nandinihub.com');
            $contactPhone = $settingModel->getSetting('contact_phone', '+91 9876543210');
            $siteLogo = $settingModel->getSetting('site_logo', '');
            $businessAddress = $settingModel->getSetting('business_address', '123 Business Street, City, State - 123456');
            ?>

            <!-- Company Logo -->
            <?php if (!empty($siteLogo)): ?>
                <div class="company-logo">
                    <img src="<?= base_url($siteLogo) ?>" alt="<?= esc($siteName) ?> Logo">
                </div>
            <?php endif; ?>

            <!-- Company Details -->
            <div class="company-details">
                <!-- Company name removed from header -->
            </div>
        </div>

        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="row">
                <div class="col">
                    <div class="invoice-meta">
                        <h2 class="invoice-title">INVOICE</h2>
                        <p><strong>Invoice #:</strong> <?= esc($order['order_number']) ?></p>
                        <p><strong>Invoice Date:</strong> <?= date('F j, Y', strtotime($order['created_at'])) ?></p>
                    </div>
                </div>
                <div class="col">
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
            </div>
        </div>

        <!-- Addresses -->
        <?php if (!empty($order['shipping_address'])): ?>
            <div class="address-section">
                <div>
                    <h5>Shipping Address:</h5>
                    <address>
                        <?php
                        // Try to decode as JSON first, if that fails, treat as plain text
                        $shippingAddress = json_decode($order['shipping_address'], true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            // It's a formatted string, display as is
                            echo nl2br(esc($order['shipping_address']));
                        } else {
                            // It's JSON data, display structured
                            if (!empty($shippingAddress['full_name'])) {
                                echo esc($shippingAddress['full_name']) . '<br>';
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
                                echo 'Phone: ' . esc($shippingAddress['phone']);
                            }
                        }
                        ?>
                    </address>
                </div>
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
                <?php if (!empty($orderItems)): ?>
                    <?php foreach ($orderItems as $item): ?>
                        <tr>
                            <td>
                                <strong><?= esc($item['product_name']) ?></strong>
                            </td>
                            <td><?= esc($item['product_sku'] ?? 'N/A') ?></td>
                            <td class="text-right">$<?= number_format($item['price'], 2) ?></td>
                            <td class="text-right"><?= $item['quantity'] ?></td>
                            <td class="text-right">$<?= number_format($item['total'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                    <td class="text-right">$<?= number_format(($order['total_amount'] - ($order['shipping_amount'] ?? 0) - ($order['tax_amount'] ?? 0) + ($order['discount_amount'] ?? 0)), 2) ?></td>
                </tr>
                <?php if (!empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Discount:</strong></td>
                        <td class="text-right">-$<?= number_format($order['discount_amount'], 2) ?></td>
                    </tr>
                <?php endif; ?>
                <?php if (!empty($order['tax_amount']) && $order['tax_amount'] > 0): ?>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Tax:</strong></td>
                        <td class="text-right">$<?= number_format($order['tax_amount'], 2) ?></td>
                    </tr>
                <?php endif; ?>
                <?php if (!empty($order['shipping_amount']) && $order['shipping_amount'] > 0): ?>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Shipping:</strong></td>
                        <td class="text-right">$<?= number_format($order['shipping_amount'], 2) ?></td>
                    </tr>
                <?php endif; ?>
                <tr class="invoice-total">
                    <td colspan="4" class="text-right"><strong>TOTAL AMOUNT:</strong></td>
                    <td class="text-right"><strong>$<?= number_format($order['total_amount'], 2) ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <!-- Order Information -->
        <div style="display: flex; gap: 20px; margin-top: 30px;">
            <!-- Order Information -->
            <div style="flex: 1; background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 5px solid #17a2b8;">
                <h5 style="color: #17a2b8; margin-bottom: 10px;">📦 Order Information</h5>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div>
                        <?php if (!empty($order['shipping_method_name'])): ?>
                            <p><strong>Shipping Method:</strong><br><?= esc($order['shipping_method_name']) ?></p>
                            <p><strong>Delivery Time:</strong><br><?= esc($order['shipping_delivery_time']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            <?php if (!empty($order['notes'])): ?>
                <div style="flex: 1; background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 5px solid #6f42c1;">
                    <h5 style="color: #6f42c1; margin-bottom: 10px;">📝 Order Notes</h5>
                    <p><?= nl2br(esc($order['notes'])) ?></p>
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
                🌐 <strong>Website:</strong> www.microdosemushroom.com
            </p>
            <hr style="margin: 15px 0; border: 1px solid #ff6b35;">
            <p><strong><?= esc($siteName) ?></strong> - <em><?= esc($siteTagline) ?></em></p>
            <p style="font-size: 10px; color: #999;">Generated on <?= date('F j, Y \a\t g:i A') ?></p>
        </div>
    </div>
</body>

</html>