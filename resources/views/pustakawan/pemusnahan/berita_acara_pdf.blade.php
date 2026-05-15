<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 58px 66px 54px; }

        body {
            color: #111;
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            margin: 0;
        }

        h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0 0 4px;
            text-align: center;
            text-transform: uppercase;
        }

        h2 {
            font-size: 14pt;
            margin: 44px 0 12px;
        }

        p {
            line-height: 1.55;
            margin: 0 0 11px;
            text-align: justify;
        }

        .doc-number {
            font-size: 10.5pt;
            margin-top: 32px;
            margin-bottom: 24px;
            text-align: left;
        }

        .dot-fill {
            border-bottom: 1px dotted #111;
            display: inline-block;
            height: 10px;
            vertical-align: baseline;
        }

        .day-fill {
            width: 72px;
        }

        .date-fill {
            width: 62px;
        }

        .month-fill {
            width: 70px;
        }

        .year-fill {
            width: 70px;
        }

        .method-fill {
            width: 156px;
        }

        .data-table {
            border-collapse: collapse;
            margin: 12px 0;
            table-layout: fixed;
            width: 100%;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #111;
            font-size: 9.5pt;
            padding: 6px 5px;
            vertical-align: top;
        }

        .data-table th {
            background: #fff;
            color: #111;
            font-weight: bold;
            text-align: center;
        }

        .main-table .col-no,
        .attachment-table .col-no {
            width: 5%;
        }

        .main-table .col-title {
            width: 17%;
        }

        .main-table .col-author {
            width: 14%;
        }

        .main-table .col-publisher {
            width: 13%;
        }

        .main-table .col-year {
            width: 11%;
        }

        .main-table .col-inventory {
            width: 20%;
        }

        .main-table .col-count {
            width: 8%;
        }

        .main-table .col-condition {
            width: 12%;
        }

        .attachment-table .col-title {
            width: 24%;
        }

        .attachment-table .col-author {
            width: 18%;
        }

        .attachment-table .col-year {
            width: 11%;
        }

        .attachment-table .col-inventory {
            width: 28%;
        }

        .attachment-table .col-count {
            width: 14%;
        }

        .center {
            text-align: center;
        }

        .method {
            line-height: 1.55;
            margin: 12px 0;
        }

        .checkbox {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10.5pt;
        }

        .page-break {
            page-break-before: always;
        }

        .date-block {
            margin: 90px 0 62px 54px;
            width: 220px;
        }

        .date-line {
            border-bottom: 1px dotted #111;
            height: 22px;
            width: 190px;
        }

        .signature-table {
            border-collapse: collapse;
            margin: 0 auto;
            table-layout: fixed;
            width: 86%;
        }

        .signature-table td {
            border: none;
            font-size: 10.5pt;
            text-align: left;
            vertical-align: top;
            width: 33.333%;
        }

        .signature-space {
            height: 62px;
        }

        .attachment-title {
            margin-bottom: 10px;
            text-align: left;
        }

        .attachment-table td {
            height: 34px;
        }
    </style>
</head>
<body>
    @include('pustakawan.pemusnahan.partials.berita_acara_document')
</body>
</html>
