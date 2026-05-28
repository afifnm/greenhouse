<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <h1 class="text-3xl font-bold mb-6">Sales Report</h1>

    <table class="w-full table-auto border-collapse bg-white shadow-md text-center">
        <thead class="bg-gray-50">
            <tr>
                <th class="border-b p-4">Product ID</th>
                <th class="border-b p-4">Variety</th>
                <th class="border-b p-4">Total Kilograms Sold</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
            <tr class="border-b hover:bg-gray-200">
                <td class="p-4">{{ $sale->product_id }}</td>
                <td class="p-4">{{ $sale->variety }}</td>
                <td class="p-4">{{ number_format($sale->total_kilograms, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('sales.index') }}" class="mt-6 inline-block px-6 py-3 text-white bg-blue-500 hover:bg-blue-700 rounded">Back to Sales</a>
</body>
</html>