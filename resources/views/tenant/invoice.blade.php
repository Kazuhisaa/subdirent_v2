<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $payment->invoice_no }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            color: #333;
            font-size: 13px;
        }
        .container {
            width: 95%;
            margin: 0 auto;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .header-left {
            display: table-cell;
            width: 60%;
        }
        .header-right {
            display: table-cell;
            width: 40%;
            text-align: right;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #000;
        }
        .header p {
            margin: 0;
            font-size: 14px;
        }
        /* * MAHALAGA: Ang public_path() ay kailangan para ma-load ng DOMPDF
         * ang image mula sa server path, hindi sa URL.
         * Siguraduhin na ang logo mo ay nasa /public/images/logo.png
        */
        .logo {
            max-width: 150px;
            max-height: 70px;
        }
        .details {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .details-left {
            display: table-cell;
            width: 50%;
        }
        .details-right {
            display: table-cell;
            width: 50%;
        }
        .details-box {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .details-box p {
            margin: 0;
            line-height: 1.6;
        }
        .details-box strong {
            display: inline-block;
            width: 130px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .items-table th {
            background: #f0f0f0;
            color: #333;
            font-weight: bold;
        }
        .items-table td.align-right {
            text-align: right;
        }
        
        .summary {
            margin-top: 30px;
            width: 100%;
            display: table;
        }
        .summary-left {
            width: 60%;
            display: table-cell;
        }
        .summary-right {
            width: 40%;
            display: table-cell;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 6px 10px;
        }
        .summary-table .total {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
        .summary-table .total.grand-total {
            font-size: 20px;
            color: #000;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .footer p {
            margin: 0;
        }

    </style>
</head>
<body>
    <div class="container">
        
        <div class="header">
            <div class="header-left">
                <p>Subdirent Management</p>
                <p>subdirent@gmail.com</p>
            </div>
            <div class="header-right">
                <h1>INVOICE</h1>
                <p><strong>Invoice #:</strong> {{ $payment->invoice_no }}</p>
                <p><strong>Date Paid:</strong> {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="details">
            <div class="details-left">
                <strong>Bill To:</strong>
                <p style="font-size: 16px; font-weight: bold; margin: 5px 0;">{{ $tenant->name }}</p>
                <p>{{ $tenant->email }}</p>
                <p>{{ $tenant->contact_num }}</p>
            </div>
            <div class="details-right">
                <div class="details-box">
                    <p><strong>Payment Method:</strong> {{ ucfirst($payment->payment_method) }}</p>
                    <p><strong>Reference No:</strong> {{ $payment->reference_no }}</p>
                    <p><strong>Payment For:</strong> {{ \Carbon\Carbon::parse($payment->for_month)->format('F Y') }}</p>
                </div>
            </div>
        </div>

        <div class="contract-summary">
            <h3>Contract Summary</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Unit Total Price</th>
                        <th>Monthly Rent</th>
                        <th>Contract Start Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="align-right">{{ number_format($contract->unit_price, 2) }}</td>
                        <td class="align-right">{{ number_format($contract->monthly_payment, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($contract->contract_start)->format('M d, Y') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="payment-details">
            <h3>Payment Details</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="align-right">Amount Paid</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $payment->remarks }}</td>
                        <td class="align-right">{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="summary">
            <div class="summary-left">
                </div>
            <div class="summary-right">
                <table class="summary-table">
                    <tr>
                        <td>Subtotal:</td>
                        <td class="align-right">{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Taxes:</td>
                        <td class="align-right"> 0.00</td>
                    </tr>
                    <tr class="total grand-total">
                        <td>TOTAL PAID:</td>
                        <td class="align-right">{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for your payment!</p>
            <p>This is an official receipt from Subdirent.</p>
        </div>

    </div>
</body>
</html>