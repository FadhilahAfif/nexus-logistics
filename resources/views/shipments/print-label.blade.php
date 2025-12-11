<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label {{ $shipment->tracking_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 0; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white p-8 border-4 border-black relative print:border-4 print:w-full print:h-full">
        
        <!-- Header -->
        <div class="flex justify-between items-start border-b-4 border-black pb-4 mb-6">
            <div>
                <h1 class="text-4xl font-black tracking-tighter">NEXUS LOGISTICS</h1>
                <p class="font-bold text-sm uppercase tracking-widest mt-1">Express Delivery</p>
            </div>
            <div class="text-right">
                <h2 class="text-3xl font-mono font-bold">{{ $shipment->tracking_number }}</h2>
                <p class="text-sm font-bold">{{ $shipment->created_at->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Addresses -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase mb-1">From (Sender):</p>
                <p class="font-bold text-lg">{{ $shipment->sender_name }}</p>
                <p class="text-gray-600">{{ $shipment->sender_phone }}</p>
            </div>
            <div class="border-l-2 border-black pl-8">
                <p class="text-xs font-bold text-gray-500 uppercase mb-1">To (Receiver):</p>
                <p class="font-bold text-2xl">{{ $shipment->receiver_name }}</p>
                <p class="text-lg leading-tight mb-2">{{ $shipment->receiver_address }}</p>
                <p class="font-bold">{{ $shipment->receiver_phone }}</p>
            </div>
        </div>

        <!-- Details -->
        <div class="border-t-4 border-b-4 border-black py-4 mb-6 flex justify-between items-center">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase">Weight</p>
                <p class="font-bold text-xl">{{ $shipment->weight_kg }} KG</p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase">Service</p>
                <p class="font-bold text-xl">REGULAR</p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase">COD Amount</p>
                <p class="font-bold text-xl">Rp 0</p>
            </div>
        </div>

        <!-- Barcode Placeholder -->
        <div class="text-center mb-4">
            <div class="h-24 bg-black w-full max-w-sm mx-auto mb-2 flex items-center justify-center text-white font-mono">
                [ BARCODE SCAN AREA ]
            </div>
            <p class="font-mono text-sm tracking-widest">{{ $shipment->tracking_number }}</p>
        </div>

        <!-- Footer -->
        <div class="text-center text-xs font-bold uppercase border-t-2 border-black pt-4">
            Nexus Logistics System • 1-800-NEXUS-LOG • www.nexus-logistics.com
        </div>

        <!-- Print Button -->
        <button onclick="window.print()" class="no-print fixed bottom-8 right-8 bg-blue-600 text-white px-6 py-3 rounded-full shadow-lg font-bold hover:bg-blue-700 transition">
            Print Label
        </button>
    </div>
</body>
</html>
