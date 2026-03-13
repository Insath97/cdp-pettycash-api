<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petty Cash Approved - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }

        .content {
            padding: 40px;
            line-height: 1.6;
        }

        .greeting {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .info-box {
            background-color: #f1f5f9;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            display: flex;
            margin-bottom: 10px;
        }

        .info-label {
            width: 140px;
            font-weight: 600;
            color: #64748b;
            font-size: 13px;
        }

        .info-value {
            font-weight: 700;
            color: #1e293b;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            background: #1e3a8a;
            color: #ffffff;
            text-align: center;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            margin-top: 10px;
        }

        .footer {
            padding: 20px;
            background-color: #f8fafc;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0;">READY FOR PAYMENT</h2>
        </div>
        <div class="content">
            <p class="greeting">Hello {{ $notifiableName }},</p>
            <p>A petty cash request has been <strong>approved</strong> and is now awaiting payment processing.</p>

            <div class="info-box">
                <div class="info-item">
                    <div class="info-label">Reference Number</div>
                    <div class="info-value">{{ $pettyCash->reference_number }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Requested By</div>
                    <div class="info-value">{{ $pettyCash->full_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Branch</div>
                    <div class="info-value">{{ $pettyCash->branch->name ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Department</div>
                    <div class="info-value">{{ $pettyCash->department->name ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Type</div>
                    <div class="info-value">{{ str_replace('_', ' ', ucfirst($pettyCash->type)) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Amount</div>
                    <div class="info-value" style="color: #1e3a8a; font-size: 18px;">
                        {{ number_format($pettyCash->amount, 2) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Category</div>
                    <div class="info-value">{{ $pettyCash->category->name ?? 'N/A' }}</div>
                </div>
                @if ($pettyCash->description)
                    <div class="info-item" style="display: block; margin-top: 15px;">
                        <div class="info-label" style="width: 100%; margin-bottom: 5px;">Description</div>
                        <div class="info-value" style="font-weight: 400; color: #475569;">{{ $pettyCash->description }}
                        </div>
                    </div>
                @endif
                @if ($pettyCash->approver)
                    <div class="info-item">
                        <div class="info-label">Approved By</div>
                        <div class="info-value">{{ $pettyCash->approver->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Approver Email</div>
                        <div class="info-value">{{ $pettyCash->approver->email }}</div>
                    </div>
                @endif

            </div>

            <a href="{{ $actionUrl }}" class="btn">Process Payment Now</a>

            <p style="margin-top: 30px; font-size: 13px;">Please complete the payment and update the status in the
                system.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
