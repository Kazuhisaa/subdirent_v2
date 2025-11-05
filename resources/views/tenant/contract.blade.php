<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract - {{ $tenant->first_name }} {{ $tenant->last_name }}</title>
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
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: bold;
            color: #000;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h3 {
            border-bottom: 2px solid #222;
            padding-bottom: 5px;
            color: #000;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .details-table th, .details-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .details-table th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .highlight {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            margin-top: 10px;
        }
        .thankyou {
            text-align: center;
            margin-top: 50px;
            background-color: #f8f8f8;
            padding: 25px;
            border-radius: 10px;
        }
        .thankyou h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ $contract->subdivision_name ?? 'Residences' }}</h1>
        <p>Official House Rental Contract</p>
        <p>Date Issued: {{ \Carbon\Carbon::now()->format('F d, Y') }}</p>
    </div>

    <div class="section">
        <h3>Welcome Message</h3>
        <div class="highlight">
            <p>
                Welcome to {{ $contract->subdivision_name ?? 'our subdivision' }}!
                We are delighted to have you as part of our growing community.
                This contract ensures that both the tenant and management enjoy
                a peaceful and well-maintained environment. We look forward to
                a harmonious and enjoyable stay throughout your lease period.
            </p>
        </div>
    </div>
    
    <div class="section">
        <h3>Tenant Information</h3>
        <div class="highlight">
            <p><strong>Name:</strong> {{ $tenant->first_name }} {{ $tenant->last_name }}</p>
            <p><strong>Email:</strong> {{ $tenant->email ?? 'N/A' }}</p>
            <p><strong>Contact Number:</strong> {{ $tenant->phone ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="section">
        <h3>Property Details</h3>
        <table class="details-table">
            <tr><th>Subdivision Name</th><td>{{ $contract->subdivision_name ?? 'Subdirent Residences' }}</td></tr>
            <tr><th>Unit / House No.</th><td>{{ $contract->unit->title ?? 'N/A' }}</td></tr>
            <tr><th>Location</th><td>{{ $contract->unit->location ?? 'N/A' }}</td></tr>
            <tr><th>Unit Price</th><td>₱{{ number_format($contract->unit_price, 2) }}</td></tr>
            <tr><th>Annual Interest</th><td>8%</td></tr>
            <tr><th>Total Price to Pay</th><td>₱{{ number_format($contract->total_price, 2) }}</td></tr>
            <tr><th>Downpayment</th><td>₱{{ number_format($contract->downpayment, 2) }}</td></tr>
            <tr><th>Monthly Rent</th><td>₱{{ number_format($contract->monthly_payment, 2) }}</td></tr>
            <tr><th>Contract Duration</th><td>{{ $contract->contract_duration }} year(s)</td></tr>
            <tr><th>Contract Start</th><td>{{ \Carbon\Carbon::parse($contract->contract_start)->format('F d, Y') }}</td></tr>
            <tr><th>Contract End</th><td>{{ \Carbon\Carbon::parse($contract->contract_end)->format('F d, Y') }}</td></tr>
        </table>
    </div>


    <div class="thankyou">
        <h3>Thank You for Trusting Subdirent!</h3>
        <p>We appreciate your decision to become part of our community.</p>
        <p>Our team is always ready to assist you with any inquiries or concerns.</p>
        <p><strong>Welcome home, {{ $tenant->first_name }}!</strong></p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Subdirent Property Management — All Rights Reserved</p>
    </div>
</div>
</body>
</html>
