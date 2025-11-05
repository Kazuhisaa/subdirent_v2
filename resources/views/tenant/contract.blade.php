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
        .header p {
            margin: 3px 0;
            font-size: 14px;
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
        .policy {
            margin-top: 20px;
            font-size: 12px;
            line-height: 1.6;
        }
        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .sign-box {
            width: 45%;
            text-align: center;
        }
        .sign-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            width: 100%;
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
        <h1>{{ $contract->subdivision_name ?? 'Subdirent Subdivision' }}</h1>
        <p>Official House Rental Contract</p>
        <p>Date Issued: {{ \Carbon\Carbon::now()->format('F d, Y') }}</p>
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
            <tr>
                <th>Subdivision Name</th>
                <td>{{ $contract->subdivision_name ?? 'Subdirent Residences' }}</td>
            </tr>
            <tr>
                <th>Unit / House No.</th>
                <td>{{ $contract->unit->unit_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Location</th>
                <td>{{ $contract->unit->address ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Unit Price</th>
                <td>₱{{ number_format($contract->unit_price, 2) }}</td>
            </tr>
            <tr>
                <th>Downpayment</th>
                <td>₱{{ number_format($contract->downpayment, 2) }}</td>
            </tr>
            <tr>
                <th>Monthly Rent</th>
                <td>₱{{ number_format($contract->monthly_payment, 2) }}</td>
            </tr>
            <tr>
                <th>Contract Duration</th>
                <td>{{ $contract->contract_duration }} year(s)</td>
            </tr>
            <tr>
                <th>Contract Start</th>
                <td>{{ \Carbon\Carbon::parse($contract->contract_start)->format('F d, Y') }}</td>
            </tr>
            <tr>
                <th>Contract End</th>
                <td>{{ \Carbon\Carbon::parse($contract->contract_end)->format('F d, Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Welcome Message</h3>
        <div class="highlight">
            <p>
                Welcome to {{ $contract->subdivision_name ?? 'our subdivision' }}!
                We are delighted to have you as part of our growing community. 
                This contract serves as a mutual agreement ensuring that both tenant 
                and management enjoy a peaceful, safe, and well-maintained environment. 
                We look forward to a harmonious stay during your lease period.
            </p>
        </div>
    </div>

    <div class="section policy">
        <h3>Terms and Policies</h3>
        <ol>
            <li>Rent payment is due every <strong>{{ $contract->payment_due_date }}</strong> of the month.</li>
            <li>Late payments beyond 5 days will incur a <strong>2% penalty</strong> on the total monthly rent.</li>
            <li>Any damage to property caused by negligence will be shouldered by the tenant.</li>
            <li>Subleasing or transferring this contract is strictly prohibited without prior management approval.</li>
            <li>Tenants must maintain cleanliness and avoid noise disturbances within the neighborhood.</li>
            <li>Upon contract termination, the tenant must vacate the premises on or before the contract end date.</li>
        </ol>
    </div>

    <div class="signatures">
        <div class="sign-box">
            <div class="sign-line"></div>
            <p><strong>{{ $tenant->first_name }} {{ $tenant->last_name }}</strong></p>
            <p>Tenant Signature</p>
        </div>
        <div class="sign-box">
            <div class="sign-line"></div>
            <p><strong>Subdirent Management</strong></p>
            <p>Authorized Representative</p>
        </div>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Subdirent Property Management — All Rights Reserved</p>
    </div>
</div>
</body>
</html>
