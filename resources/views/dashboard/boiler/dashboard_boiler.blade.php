<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boiler Monitoring Dashboard - Demo</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Vue.js 3 -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .status-indicator {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .5;
            }
        }

        .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .metric-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Steam animation */
        @keyframes steam {
            0% {
                transform: translateY(0) scaleX(1);
                opacity: 0.7;
            }

            50% {
                transform: translateY(-10px) scaleX(1.2);
                opacity: 0.4;
            }

            100% {
                transform: translateY(-20px) scaleX(1.5);
                opacity: 0;
            }
        }

        .steam-particle {
            animation: steam 2s infinite;
        }

        /* Pipe flow animation */
        @keyframes flow {
            0% {
                stroke-dashoffset: 0;
            }

            100% {
                stroke-dashoffset: -20;
            }
        }

        .pipe-flow {
            animation: flow 1s linear infinite;
        }

        @media (max-width: 640px) {
            .responsive-text-sm {
                font-size: 0.75rem;
            }

            .responsive-text-base {
                font-size: 0.875rem;
            }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 min-h-screen">
    <div id="app">
        <!-- Header -->
        <div class="p-2 sm:p-4 md:p-6 lg:p-8">
            <div class="mb-4 sm:mb-6 md:mb-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">
                            🔥 Boiler Monitoring Dashboard
                        </h1>
                        <p class="text-gray-300 text-sm sm:text-base">Real-time monitoring system</p>
                    </div>
                    <div class="flex items-center gap-3 bg-white/10 backdrop-blur-md rounded-lg px-4 py-2">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-green-400 status-indicator"></div>
                            <span class="text-white text-sm">Connected</span>
                        </div>
                        <div class="text-white text-sm" id="current-time">14:30:25</div>
                    </div>
                </div>
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

                <!-- Left Column - Steam & Pressure -->
                <div class="lg:col-span-1 space-y-4 sm:space-y-6">

                    <!-- Steam Flow Card -->
                    <div class="card-shadow rounded-xl bg-white/10 backdrop-blur-md p-4 sm:p-6">
                        <h3 class="text-white text-lg sm:text-xl font-semibold mb-4 flex items-center gap-2">
                            <span>💨</span> Steam Flow
                        </h3>
                        <div class="space-y-4">
                            <!-- Steam Visual -->
                            <div class="bg-blue-500/20 rounded-lg p-4 relative overflow-hidden">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-300 text-sm">Ton/H</span>
                                    <span class="text-green-400 text-xs">● Active</span>
                                </div>

                                <!-- Steam pipes illustration -->
                                <div class="absolute top-0 right-0 opacity-20">
                                    <svg width="100" height="100" viewBox="0 0 100 100">
                                        <path d="M 10 50 Q 30 30, 50 50 T 90 50" stroke="white" stroke-width="8" fill="none" stroke-dasharray="10,5" class="pipe-flow" />
                                        <circle cx="20" cy="45" r="3" fill="white" opacity="0.6" class="steam-particle" style="animation-delay: 0s" />
                                        <circle cx="40" cy="40" r="3" fill="white" opacity="0.6" class="steam-particle" style="animation-delay: 0.5s" />
                                        <circle cx="60" cy="45" r="3" fill="white" opacity="0.6" class="steam-particle" style="animation-delay: 1s" />
                                    </svg>
                                </div>

                                <div class="text-white text-3xl sm:text-4xl font-bold relative z-10" id="steam-ton-per-hour">
                                    45.2
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-blue-500/10 rounded-lg p-3">
                                    <div class="text-gray-300 text-xs mb-1">Steam Flow</div>
                                    <div class="text-white text-xl font-semibold" id="steam-tons">38.5</div>
                                    <div class="text-gray-400 text-xs">Tons</div>
                                </div>
                                <div class="bg-blue-500/10 rounded-lg p-3">
                                    <div class="text-gray-300 text-xs mb-1">Pressure</div>
                                    <div class="text-white text-xl font-semibold" id="steam-bar">12.3</div>
                                    <div class="text-gray-400 text-xs">bar</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pressure Status -->
                    <div class="card-shadow rounded-xl bg-white/10 backdrop-blur-md p-4 sm:p-6">
                        <h3 class="text-white text-lg sm:text-xl font-semibold mb-4 flex items-center gap-2">
                            <span>📊</span> Pressure Status
                        </h3>
                        <div class="grid grid-cols-2 gap-4">
                            <!-- PS1 -->
                            <div class="bg-green-500/20 rounded-lg p-4 text-center relative">
                                <div class="relative w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-2">
                                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                        <circle cx="50" cy="50" r="40" stroke="#374151" stroke-width="8" fill="none" />
                                        <circle cx="50" cy="50" r="40" stroke="#4ade80" stroke-width="8" fill="none" stroke-dasharray="251.2" stroke-dashoffset="37.68" class="transition-all duration-500" />
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <span class="text-white text-xl font-bold">✓</span>
                                    </div>
                                </div>
                                <div class="text-white font-semibold">PS1</div>
                                <div class="text-green-400 text-xs">Normal</div>
                            </div>

                            <!-- PS2 -->
                            <div class="bg-green-500/20 rounded-lg p-4 text-center relative">
                                <div class="relative w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-2">
                                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                        <circle cx="50" cy="50" r="40" stroke="#374151" stroke-width="8" fill="none" />
                                        <circle cx="50" cy="50" r="40" stroke="#4ade80" stroke-width="8" fill="none" stroke-dasharray="251.2" stroke-dashoffset="45.22" class="transition-all duration-500" />
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <span class="text-white text-xl font-bold">✓</span>
                                    </div>
                                </div>
                                <div class="text-white font-semibold">PS2</div>
                                <div class="text-green-400 text-xs">Normal</div>
                            </div>
                        </div>
                    </div>

                    <!-- Coal Data -->
                    <div class="card-shadow rounded-xl bg-white/10 backdrop-blur-md p-4 sm:p-6">
                        <h3 class="text-white text-lg sm:text-xl font-semibold mb-4 flex items-center gap-2">
                            <span>⚫</span> Coal & Guillotine
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center bg-orange-500/10 rounded-lg p-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">🪨</span>
                                    <span class="text-gray-300 text-sm">LH Total Coal</span>
                                </div>
                                <span class="text-white font-semibold" id="coal-total">125.5 Tons</span>
                            </div>
                            <div class="flex justify-between items-center bg-orange-500/10 rounded-lg p-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">📏</span>
                                    <span class="text-gray-300 text-sm">LH Guillotine</span>
                                </div>
                                <span class="text-white font-semibold" id="lh-guillotine">850 mm</span>
                            </div>
                            <div class="flex justify-between items-center bg-orange-500/10 rounded-lg p-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">📏</span>
                                    <span class="text-gray-300 text-sm">RH Guillotine</span>
                                </div>
                                <span class="text-white font-semibold" id="rh-guillotine">850 mm</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Middle Column - Temperature & Level -->
                <div class="lg:col-span-1 space-y-4 sm:space-y-6">

                    <!-- Temperature Monitor -->
                    <div class="card-shadow rounded-xl bg-white/10 backdrop-blur-md p-4 sm:p-6">
                        <h3 class="text-white text-lg sm:text-xl font-semibold mb-4 flex items-center gap-2">
                            <span>🌡️</span> Temperature Monitor
                        </h3>
                        <div class="space-y-4">
                            <!-- Temp 1 -->
                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-300 text-sm">LH Temp 1</span>
                                    <span class="text-white font-semibold" id="temp-1">245 °C</span>
                                </div>
                                <div class="relative">
                                    <div class="w-full bg-gray-700 rounded-full h-3">
                                        <div class="bg-gradient-to-r from-blue-400 to-red-500 h-3 rounded-full transition-all duration-500" id="temp-1-bar" style="width: 61%">
                                        </div>
                                    </div>
                                    <div class="absolute -left-1 top-1/2 -translate-y-1/2">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                            <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Temp 2 -->
                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-300 text-sm">LH Temp 2</span>
                                    <span class="text-white font-semibold" id="temp-2">238 °C</span>
                                </div>
                                <div class="relative">
                                    <div class="w-full bg-gray-700 rounded-full h-3">
                                        <div class="bg-gradient-to-r from-blue-400 to-red-500 h-3 rounded-full transition-all duration-500" id="temp-2-bar" style="width: 59%">
                                        </div>
                                    </div>
                                    <div class="absolute -left-1 top-1/2 -translate-y-1/2">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                            <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Water Level Indicator -->
                    <div class="card-shadow rounded-xl bg-white/10 backdrop-blur-md p-4 sm:p-6">
                        <h3 class="text-white text-lg sm:text-xl font-semibold mb-4 flex items-center gap-2">
                            <span>💧</span> Water Level
                        </h3>
                        <div class="relative bg-gray-800 rounded-lg p-6 h-64 sm:h-80">
                            <!-- Tank illustration -->
                            <div class="absolute inset-4 border-4 border-gray-600 rounded-lg overflow-hidden">
                                <!-- Water -->
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-blue-500 to-blue-300 transition-all duration-1000" id="water-level-fill" style="height: 65%">
                                    <!-- Water surface animation -->
                                    <div class="absolute top-0 left-0 right-0 h-8 bg-white/20 animate-pulse"></div>

                                    <!-- Water droplets -->
                                    <div class="absolute top-2 left-1/4">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="white" opacity="0.3">
                                            <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z" />
                                        </svg>
                                    </div>
                                    <div class="absolute top-4 right-1/4">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="white" opacity="0.3">
                                            <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Center percentage display -->
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center z-10">
                                <div class="text-white text-4xl sm:text-5xl font-bold drop-shadow-lg" id="water-level-text">65%</div>
                                <div class="text-gray-300 mt-2">Water Level</div>
                            </div>

                            <!-- Level Indicators -->
                            <div class="absolute left-0 top-0 h-full flex flex-col justify-between py-4">
                                <span class="text-gray-400 text-xs">100%</span>
                                <span class="text-gray-400 text-xs">75%</span>
                                <span class="text-gray-400 text-xs">50%</span>
                                <span class="text-gray-400 text-xs">25%</span>
                                <span class="text-gray-400 text-xs">0%</span>
                            </div>

                            <!-- Status Lights -->
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 flex flex-col gap-3" id="water-status-lights">
                                <div class="w-4 h-4 rounded-full bg-gray-600"></div>
                                <div class="w-4 h-4 rounded-full bg-yellow-500 status-indicator"></div>
                                <div class="w-4 h-4 rounded-full bg-gray-600"></div>
                            </div>
                        </div>

                        <!-- Level Status Labels -->
                        <div class="flex justify-around mt-4">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                <span class="text-gray-300 text-xs">LLW</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <span class="text-gray-300 text-xs">ELLW</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Stoker & Flow -->
                <div class="lg:col-span-1 space-y-4 sm:space-y-6">

                    <!-- Stoker Status -->
                    <div class="card-shadow rounded-xl bg-white/10 backdrop-blur-md p-4 sm:p-6">
                        <h3 class="text-white text-lg sm:text-xl font-semibold mb-4 flex items-center gap-2">
                            <span>⚙️</span> Stoker Control
                        </h3>
                        <div class="space-y-4">
                            <!-- LH Stoker -->
                            <div class="bg-gray-800/50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl">🔧</span>
                                        <span class="text-gray-300 font-medium">LH Stoker</span>
                                    </div>
                                    <div class="px-3 py-1 rounded-full text-xs font-semibold bg-green-500 text-white">
                                        active
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg flex items-center justify-center relative bg-green-500">
                                        <svg width="30" height="30" viewBox="0 0 24 24" fill="white" class="animate-spin" style="animation-duration: 3s">
                                            <path d="M12 2L14 8L20 6L16 12L22 14L16 16L20 18L14 16L12 22L10 16L4 18L8 12L2 10L8 8L4 6L10 8L12 2Z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-gray-400 text-xs">Frequency</div>
                                        <div class="text-white font-semibold" id="lh-stoker-freq">45 Hz</div>
                                    </div>
                                </div>
                            </div>

                            <!-- RH Stoker -->
                            <div class="bg-gray-800/50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl">🔧</span>
                                        <span class="text-gray-300 font-medium">RH Stoker</span>
                                    </div>
                                    <div class="px-3 py-1 rounded-full text-xs font-semibold bg-green-500 text-white">
                                        active
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg flex items-center justify-center relative bg-green-500">
                                        <svg width="30" height="30" viewBox="0 0 24 24" fill="white" class="animate-spin" style="animation-duration: 3s">
                                            <path d="M12 2L14 8L20 6L16 12L22 14L16 16L20 18L14 16L12 22L10 16L4 18L8 12L2 10L8 8L4 6L10 8L12 2Z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-gray-400 text-xs">Frequency</div>
                                        <div class="text-white font-semibold" id="rh-stoker-freq">42 Hz</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Flow Meters -->
                    <div class="card-shadow rounded-xl bg-white/10 backdrop-blur-md p-4 sm:p-6">
                        <h3 class="text-white text-lg sm:text-xl font-semibold mb-4 flex items-center gap-2">
                            <span>📊</span> Flow Meters
                        </h3>
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Batubara Flow -->
                            <div class="bg-gradient-to-r from-blue-600/20 to-cyan-600/20 rounded-lg p-4 relative overflow-hidden">
                                <div class="absolute top-2 right-2 text-4xl opacity-20">⚫</div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xl">🪨</span>
                                    <div class="text-gray-300 text-sm">Batubara</div>
                                </div>
                                <div class="text-white text-2xl sm:text-3xl font-bold" id="flow-batubara">3250</div>
                                <div class="text-gray-400 text-xs mt-1">kg/jam</div>
                            </div>

                            <!-- Steam Flow -->
                            <div class="bg-gradient-to-r from-cyan-600/20 to-blue-600/20 rounded-lg p-4 relative overflow-hidden">
                                <div class="absolute top-2 right-2 text-4xl opacity-20">💨</div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xl">💨</span>
                                    <div class="text-gray-300 text-sm">Steam</div>
                                </div>
                                <div class="text-white text-2xl sm:text-3xl font-bold" id="flow-steam">42.5</div>
                                <div class="text-gray-400 text-xs mt-1">m³/jam</div>
                            </div>

                            <!-- Demin Water -->
                            <div class="bg-gradient-to-r from-blue-600/20 to-indigo-600/20 rounded-lg p-4 relative overflow-hidden">
                                <div class="absolute top-2 right-2 text-4xl opacity-20">💧</div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xl">💧</span>
                                    <div class="text-gray-300 text-sm">Demin Water</div>
                                </div>
                                <div class="text-white text-2xl sm:text-3xl font-bold" id="flow-water">38.2</div>
                                <div class="text-gray-400 text-xs mt-1">m³/hr</div>
                            </div>
                        </div>
                    </div>

                    <!-- Alert Panel -->
                 
                </div>
            </div>

            <!-- Bottom Stats Row -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mt-4 sm:mt-6">
                <div class="metric-card card-shadow rounded-lg bg-gradient-to-br from-blue-500/20 to-blue-600/20 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-2xl">⚡</span>
                        <div class="text-gray-300 text-xs sm:text-sm">Efficiency</div>
                    </div>
                    <div class="text-white text-xl sm:text-2xl font-bold" id="efficiency">87.5%</div>
                </div>
                <div class="metric-card card-shadow rounded-lg bg-gradient-to-br from-green-500/20 to-green-600/20 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-2xl">⏱️</span>
                        <div class="text-gray-300 text-xs sm:text-sm">Uptime</div>
                    </div>
                    <div class="text-white text-xl sm:text-2xl font-bold" id="uptime">156h</div>
                </div>
                <div class="metric-card card-shadow rounded-lg bg-gradient-to-br from-purple-500/20 to-purple-600/20 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-2xl">📈</span>
                        <div class="text-gray-300 text-xs sm:text-sm">Load</div>
                    </div>
                    <div class="text-white text-xl sm:text-2xl font-bold" id="load">78%</div>
                </div>
                <div class="metric-card card-shadow rounded-lg bg-gradient-to-br from-orange-500/20 to-orange-600/20 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-2xl">🔋</span>
                        <div class="text-gray-300 text-xs sm:text-sm">Output</div>
                    </div>
                    <div class="text-white text-xl sm:text-2xl font-bold" id="output">45.2 MW</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('current-time').textContent = timeString;
        }

        // Simulate data updates
        function simulateDataUpdate() {
            // Steam flow
            const steamTonPerHour = (45 + Math.random() * 5).toFixed(1);
            const steamTons = (38 + Math.random() * 4).toFixed(1);
            const steamBar = (12 + Math.random() * 1.5).toFixed(1);

            document.getElementById('steam-ton-per-hour').textContent = steamTonPerHour;
            document.getElementById('steam-tons').textContent = steamTons;
            document.getElementById('steam-bar').textContent = steamBar;

            // Temperature
            const temp1 = Math.floor(240 + Math.random() * 10);
            const temp2 = Math.floor(235 + Math.random() * 10);
            document.getElementById('temp-1').textContent = temp1 + ' °C';
            document.getElementById('temp-2').textContent = temp2 + ' °C';
            document.getElementById('temp-1-bar').style.width = (temp1 / 400 * 100) + '%';
            document.getElementById('temp-2-bar').style.width = (temp2 / 400 * 100) + '%';

            // Water level
            let currentLevel = parseFloat(document.getElementById('water-level-text').textContent);
            let newLevel = Math.max(30, Math.min(95, currentLevel + (Math.random() - 0.5) * 3));
            newLevel = Math.round(newLevel);
            document.getElementById('water-level-text').textContent = newLevel + '%';
            document.getElementById('water-level-fill').style.height = newLevel + '%';

            // Update water status lights
            const lightsContainer = document.getElementById('water-status-lights');
            lightsContainer.innerHTML = '';

            const light1 = document.createElement('div');
            light1.className = 'w-4 h-4 rounded-full ' + (newLevel < 30 ? 'bg-red-500 status-indicator' : 'bg-gray-600');

            const light2 = document.createElement('div');
            light2.className = 'w-4 h-4 rounded-full ' + (newLevel >= 30 && newLevel < 70 ? 'bg-yellow-500 status-indicator' : 'bg-gray-600');

            const light3 = document.createElement('div');
            light3.className = 'w-4 h-4 rounded-full ' + (newLevel >= 70 ? 'bg-green-500 status-indicator' : 'bg-gray-600');

            lightsContainer.appendChild(light1);
            lightsContainer.appendChild(light2);
            lightsContainer.appendChild(light3);

            // Flow meters
            const flowBatubara = Math.floor(3200 + Math.random() * 100);
            const flowSteam = (42 + Math.random() * 4).toFixed(1);
            const flowWater = (38 + Math.random() * 4).toFixed(1);

            document.getElementById('flow-batubara').textContent = flowBatubara;
            document.getElementById('flow-steam').textContent = flowSteam;
            document.getElementById('flow-water').textContent = flowWater;

            // Bottom stats
            const efficiency = (87 + Math.random() * 3).toFixed(1);
            const load = Math.floor(75 + Math.random() * 8);
            const output = (45 + Math.random() * 3).toFixed(1);

            document.getElementById('efficiency').textContent = efficiency + '%';
            document.getElementById('load').textContent = load + '%';
            document.getElementById('output').textContent = output + ' MW';

            // Random alert (rarely)
            if (Math.random() > 0.97) {
                addAlert();
            }
        }

      
        // Start updates
        setInterval(updateTime, 1000);
        setInterval(simulateDataUpdate, 3000);
        updateTime();
    </script>
</body>

</html>