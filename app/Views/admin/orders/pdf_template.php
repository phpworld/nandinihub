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
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .invoice-container {
            max-width: 100%;
            margin: 0;
            background: white;
            padding: 20px;
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
            max-height: 60px;
            max-width: 120px;
            object-fit: contain;
        }

        .company-details {
            flex: 1;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #ff6b35;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 20px;
            border-radius: 8px;
            border-left: 5px solid #ff6b35;
        }

        .invoice-meta {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .bill-to {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
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
            margin: 20px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #e9ecef;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }

        .invoice-table th {
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
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
            font-size: 14px;
            background-color: #fff3cd;
        }

        .invoice-footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-top: 3px solid #ff6b35;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }

        .invoice-footer p {
            margin-bottom: 5px;
        }

        .footer-highlight {
            color: #ff6b35;
            font-weight: bold;
        }

        .payment-info-section {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }

        .payment-info-box {
            flex: 1;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 5px solid #17a2b8;
        }

        .order-notes-box {
            flex: 1;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 5px solid #6f42c1;
        }

        h4, h5 {
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

        <!-- Addresses -->
        <?php if (!empty($order['shipping_address'])): ?>
            <div class="address-section">
                <div>
                    <h5>Shipping Address:</h5>
                    <address>
                        <?php
                        $shippingAddress = json_decode($order['shipping_address'], true);
                        if ($shippingAddress):
                        ?>
                            <?= esc($shippingAddress['full_name']) ?><br>
                            <?= esc($shippingAddress['address_line_1']) ?><br>
                            <?php if (!empty($shippingAddress['address_line_2'])): ?>
                                <?= esc($shippingAddress['address_line_2']) ?><br>
                            <?php endif; ?>
                            <?= esc($shippingAddress['city']) ?>, <?= esc($shippingAddress['state']) ?> <?= esc($shippingAddress['postal_code']) ?><br>
                            <?= esc($shippingAddress['country']) ?><br>
                            <?php if (!empty($shippingAddress['phone'])): ?>
                                Phone: <?= esc($shippingAddress['phone']) ?>
                            <?php endif; ?>
                        <?php endif; ?>
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
                        <td class="text-right">₹<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                    <td class="text-right">₹<?= number_format($order['subtotal'], 2) ?></td>
                </tr>
                <?php if (!empty($order['tax_amount']) && $order['tax_amount'] > 0): ?>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Tax:</strong></td>
                        <td class="text-right">₹<?= number_format($order['tax_amount'], 2) ?></td>
                    </tr>
                <?php endif; ?>
                <?php if (!empty($order['shipping_amount']) && $order['shipping_amount'] > 0): ?>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Shipping:</strong></td>
                        <td class="text-right">₹<?= number_format($order['shipping_amount'], 2) ?></td>
                    </tr>
                <?php endif; ?>
                <tr class="invoice-total">
                    <td colspan="4" class="text-right"><strong>TOTAL AMOUNT:</strong></td>
                    <td class="text-right"><strong>₹<?= number_format($order['total_amount'], 2) ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <!-- Payment & Order Information -->
        <div class="payment-info-section">
            <!-- Payment Information -->
            <div class="payment-info-box">
                <h5 style="color: #17a2b8; margin-bottom: 10px;">💳 Payment Information</h5>
                <p><strong>Payment Method:</strong> <?= ucfirst($order['payment_method']) ?></p>
                <p><strong>Payment Status:</strong> 
                    <span style="color: <?= $order['payment_status'] === 'paid' ? '#28a745' : '#ffc107' ?>; font-weight: bold;">
                        <?= ucfirst($order['payment_status']) ?>
                    </span>
                </p>
                <?php if (!empty($order['shipping_method_name'])): ?>
                    <p><strong>Shipping Method:</strong> <?= esc($order['shipping_method_name']) ?></p>
                    <p><strong>Delivery Time:</strong> <?= esc($order['shipping_delivery_time']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Order Notes -->
            <?php if (!empty($order['notes'])): ?>
                <div class="order-notes-box">
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
                🌐 <strong>Website:</strong> www.nandinihub.com
            </p>
            <hr style="margin: 15px 0; border: 1px solid #ff6b35;">
            <p><strong><?= esc($siteName) ?></strong> - <em><?= esc($siteTagline) ?></em></p>
            <p style="font-size: 9px; color: #999;">Generated on <?= date('F j, Y \a\t g:i A') ?></p>
        </div>
    </div>
</body>

</html>
