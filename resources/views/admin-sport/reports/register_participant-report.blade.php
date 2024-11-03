<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Participants Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            /* Align vertically in the center */
            justify-content: center;
            /* Center horizontally */
        }

        .header img {
            width: 100px;
            height: auto;
            margin-right: 20px;
            /* Adds space between the image and the text */
        }

        h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            color: #800000;
        }

        h3 {
            font-size: 16px;
            font-weight: normal;
            margin: 5px 0;
            color: #800000;
        }

        .contact-info {
            margin-top: 5px;
            font-size: 12px;
            text-align: left;
            color: red;
        }

        .report-info {
            margin-top: 30px;
            margin-bottom: 20px;
            text-align: center;
        }

        .report-info p {
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
        }

        .signature .signature-line {
            margin-top: 50px;
            border-top: 1px solid black;
            width: 200px;
            text-align: center;
        }

        .footer {
            text-align: right;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header" style="display: flex">
        <img src="{{ public_path('images/bindtogether-logo.png') }}" alt="University Logo" style="float: left;">
        <div class="">
            <h1 style="color: #800000">Bataan Peninsula State University</h1>
            <h3 style="margin-left: 10px; color: #800000">Bind Together</h3>
            <div class="contact-info" style="margin-left: 10px; text-align: center">
                <p>City of Balanga, 2100 Bataan</p>
                <p style="margin-left: 70px">Tel: (047) 237-3309 | www.bpsu.edu.ph | Email: bpsu.bindtogether@gmail.com</p>
            </div>
        </div>
    </div>

    <div class="report-info">
        <h3 style="color: black;">Registered Participants Report for {{ $startDate }} - {{ $endDate }}</h3>
        <p style="font-weight: bold;">
            Type of Report <span style="font-weight: normal;">(Registered Participants)</span>
        </p>

    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Year Level</th>
                <th>Campus</th>
                <th>Email</th>
                <th>Height</th>
                <th>Weight</th>
                <th>Person to Contact</th>
                <th>Emergency Contact Number</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($registered_participants as $participants)
            <tr>
                <td>{{ $participants->user->firstname . ' ' . $participants->user->lastname }}</td>
                <td>{{ $participants->user->year_level }}</td>
                <td>{{ $participants->user->campus }}</td>
                <td>{{ $participants->user->email }}</td>
                <td>{{ $participants->height }}</td>
                <td>{{ $participants->weight }}</td>
                <td>{{ $participants->emergency_contact }}</td>
                <td>{{ $participants->user->contact}}</td>
            </tr>
            @endforeach
        </tbody>

    </table>

    <div class="signature" style="float: right">
        {{-- <p>Reported by:</p> --}}
        <div class="signature-line" style="font-size: 12px">
            (Space for Signature)
        </div>
        {{-- <p>(name of the report generator)</p> --}}
    </div>

    {{-- <div class="footer">
        <p>Generated on {{ now()->format('Y-m-d') }}</p>
    </div> --}}
</body>

</html>