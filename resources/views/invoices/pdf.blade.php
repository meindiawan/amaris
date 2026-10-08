<!doctype html><html lang="id"><head><meta charset="utf-8"><title>{{ $invoice->file_name }}</title>
<style>@page{margin:14mm 12mm 14mm 12mm}body{margin:0}</style>
@include('invoices._css')
</head><body>@include('invoices._sheet', ['noCss' => true])</body></html>
