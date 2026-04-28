<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error {{ $code ?? 500 }} - Konveksi Hub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="antialiased bg-gray-50 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-5xl w-full bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">
            
            {{-- Left Side: Error Content --}}
            <div class="p-10 md:p-16 flex flex-col justify-center">
                <div class="inline-flex items-center justify-center px-4 py-2 rounded-full bg-red-50 text-red-600 font-bold text-sm tracking-wide mb-6 w-fit">
                    Error Code: {{ $code ?? 500 }}
                </div>
                
                <h1 class="text-5xl md:text-6xl font-black text-gray-900 mb-4 tracking-tight">Oops!</h1>
                <p class="text-xl text-gray-500 mb-8 leading-relaxed">
                    We can't seem to find the page you're looking for, or something went wrong on our end.
                </p>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap items-center gap-4 mb-10">
                    <a href="{{ url('/') }}" class="inline-flex items-center justify-center h-12 px-8 rounded-xl font-semibold bg-gray-900 text-white hover:bg-gray-800 transition">
                        Back to Home
                    </a>
                    <a href="{{ url('/stock') }}" class="inline-flex items-center justify-center h-12 px-8 rounded-xl font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                        View Products
                    </a>
                </div>

                {{-- Technical Details (Scrollable container) --}}
                <div class="mt-6 w-full">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Technical Details</p>
                    <div class="max-h-60 overflow-y-auto bg-gray-50 border border-gray-200 p-5 rounded-2xl">
                        <p class="font-mono text-sm text-red-600 mb-3">
                            {{ $exception->getMessage() ?: 'An unexpected error occurred.' }}
                        </p>
                        
                        @if(app()->environment('local'))
                            <details class="border-t border-gray-200 pt-3 mt-3 group">
                                <summary class="text-xs font-bold text-gray-500 mb-2 select-none cursor-pointer hover:text-gray-700 transition flex items-center gap-1 outline-none">
                                    Show Stack Trace
                                    <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </summary>
                                <pre class="font-mono text-[10px] sm:text-xs text-gray-600 whitespace-pre-wrap break-all mt-3 bg-white p-4 rounded-xl border border-gray-100 shadow-inner overflow-x-auto">{{ $exception->getTraceAsString() }}</pre>
                            </details>
                        @else
                            <p class="text-xs text-gray-500">
                                Detailed traces are disabled in production for security reasons.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Side: Illustration --}}
            <div class="bg-gray-50 p-10 md:p-16 flex items-center justify-center border-l border-gray-100">
                <div class="w-full max-w-sm aspect-square bg-gray-200/50 rounded-[2rem] flex items-center justify-center relative overflow-hidden">
                    {{-- Placeholder abstract shapes to look nice --}}
                    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-indigo-500 via-purple-500 to-pink-500"></div>
                    <svg class="w-32 h-32 text-gray-400 relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
