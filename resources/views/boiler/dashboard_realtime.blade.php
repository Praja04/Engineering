<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boiler System - Isometric 3D</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 1400px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #1e3c72;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 12px;
        }

        #boiler-diagram {
            display: block;
            border: 2px solid #1e3c72;
            border-radius: 8px;
        }

        .download-btn {
            display: block;
            margin: 15px auto 0;
            padding: 10px 30px;
            background: #1e3c72;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }

        .download-btn:hover {
            background: #2a5298;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Boiler System - Isometric Diagram</h1>
            <p>2D Isometric view with 3D effect - Right click on diagram to save as image</p>
        </div>

        <svg id="boiler-diagram" width="100%" height="650" viewBox="0 0 1200 650">
            <!-- Background -->
            <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#ccc" stroke-width="0.5" opacity="0.3" />
                </pattern>

                <!-- Gradients for 3D effect -->
                <linearGradient id="boilerGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#4a90e2;stop-opacity:1" />
                    <stop offset="50%" style="stop-color:#357abd;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#2a5d8f;stop-opacity:1" />
                </linearGradient>

                <linearGradient id="pipeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#95a5a6;stop-opacity:1" />
                    <stop offset="50%" style="stop-color:#7f8c8d;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#5d6d7e;stop-opacity:1" />
                </linearGradient>

                <linearGradient id="tankGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#3498db;stop-opacity:0.9" />
                    <stop offset="100%" style="stop-color:#2980b9;stop-opacity:0.9" />
                </linearGradient>

                <linearGradient id="chimneyGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" style="stop-color:#e74c3c;stop-opacity:1" />
                    <stop offset="50%" style="stop-color:#c0392b;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#922b21;stop-opacity:1" />
                </linearGradient>

                <filter id="shadow">
                    <feDropShadow dx="4" dy="4" stdDeviation="3" flood-opacity="0.3" />
                </filter>
            </defs>

            <rect width="100%" height="100%" fill="url(#grid)" />

            <!-- Title Section -->
            <rect x="20" y="20" width="150" height="80" rx="5" fill="#34495e" filter="url(#shadow)" />
            <text x="95" y="45" text-anchor="middle" fill="#fff" font-size="14" font-weight="bold">BB/STEAM</text>
            <rect x="30" y="55" width="130" height="30" rx="3" fill="#fff" opacity="0.9" />
            <text x="95" y="77" text-anchor="middle" fill="#34495e" font-size="16" font-weight="bold">1:8</text>

            <!-- Steam Flow Info (Top) -->
            <g transform="translate(380, 30)">
                <rect width="160" height="35" rx="3" fill="#f39c12" filter="url(#shadow)" />
                <text x="80" y="23" text-anchor="middle" fill="#fff" font-size="14" font-weight="bold">STEAM FLOW: 5 Ton/H</text>
            </g>

            <!-- Steam Totalizer -->
            <g transform="translate(390, 80)">
                <rect x="0" y="0" width="200" height="90" rx="5" fill="#34495e" stroke="#2c3e50" stroke-width="2" filter="url(#shadow)" />
                <rect x="5" y="5" width="190" height="80" rx="3" fill="#ecf0f1" opacity="0.2" />
                <text x="100" y="30" text-anchor="middle" fill="#fff" font-size="13" font-weight="bold">STEAM TOTALIZER</text>
                <rect x="40" y="40" width="120" height="35" rx="3" fill="#2c3e50" />
                <text x="100" y="63" text-anchor="middle" fill="#2ecc71" font-size="18" font-weight="bold">8.5 Tons</text>

                <!-- Pressure Switches -->
                <circle cx="50" cy="75" r="6" fill="#2ecc71">
                    <animate attributeName="opacity" values="1;0.3;1" dur="1s" repeatCount="indefinite" />
                </circle>
                <text x="60" y="80" fill="#fff" font-size="10">PS1</text>

                <circle cx="140" cy="75" r="6" fill="#2ecc71">
                    <animate attributeName="opacity" values="1;0.3;1" dur="1s" repeatCount="indefinite" begin="0.5s" />
                </circle>
                <text x="150" y="80" fill="#fff" font-size="10">PS2</text>
            </g>

            <!-- Main Boiler Body (Center) -->
            <g transform="translate(440, 200)">
                <!-- Top ellipse -->
                <ellipse cx="60" cy="0" rx="60" ry="18" fill="#2c3e50" />
                <!-- Main body -->
                <rect x="0" y="0" width="120" height="240" fill="url(#boilerGrad)" filter="url(#shadow)" />
                <rect x="10" y="10" width="100" height="220" fill="#2980b9" opacity="0.2" />
                <!-- Bottom ellipse -->
                <ellipse cx="60" cy="240" rx="60" ry="18" fill="#1a252f" />

                <!-- Water Level Gauge -->
                <rect x="130" y="60" width="35" height="160" rx="4" fill="#ecf0f1" stroke="#34495e" stroke-width="2" />
                <rect x="133" y="63" width="29" height="100" rx="3" fill="#3498db">
                    <animate attributeName="height" values="90;110;90" dur="4s" repeatCount="indefinite" />
                    <animate attributeName="y" values="133;113;133" dur="4s" repeatCount="indefinite" />
                </rect>

                <!-- Level Labels -->
                <text x="147" y="50" text-anchor="middle" fill="#34495e" font-size="11" font-weight="bold">LLW</text>
                <line x1="130" y1="120" x2="165" y2="120" stroke="#e74c3c" stroke-width="2" />
                <text x="147" y="230" text-anchor="middle" fill="#34495e" font-size="11" font-weight="bold">ELLW</text>
                <line x1="130" y1="190" x2="165" y2="190" stroke="#e74c3c" stroke-width="2" />

                <!-- Percentage Display -->
                <rect x="20" y="90" width="80" height="60" rx="5" fill="#ecf0f1" opacity="0.8" />
                <text x="60" y="115" text-anchor="middle" fill="#2c3e50" font-size="28" font-weight="bold">75%</text>
                <text x="60" y="135" text-anchor="middle" fill="#2c3e50" font-size="10">Water Level</text>

                <!-- Motor indicator inside boiler -->
                <circle cx="60" cy="180" r="25" fill="none" stroke="#fff" stroke-width="2" opacity="0.4" />
                <text x="60" y="188" text-anchor="middle" fill="#fff" font-size="28" font-weight="bold">M</text>
            </g>

            <!-- LH Temperature Chamber (Left) -->
            <g transform="translate(230, 240)">
                <rect x="0" y="0" width="90" height="140" rx="6" fill="url(#boilerGrad)" stroke="#2c3e50" stroke-width="2" filter="url(#shadow)" />
                <rect x="5" y="5" width="80" height="130" rx="4" fill="#e74c3c" opacity="0.25" />

                <!-- Temperature bar -->
                <rect x="15" y="20" width="20" height="100" rx="3" fill="#34495e" />
                <rect x="17" y="90" width="16" height="0" rx="2" fill="#e74c3c">
                    <animate attributeName="height" values="20;35;20" dur="3s" repeatCount="indefinite" />
                    <animate attributeName="y" values="90;75;90" dur="3s" repeatCount="indefinite" />
                </rect>

                <text x="45" y="30" fill="#fff" font-size="12">°C</text>
                <text x="50" y="70" fill="#fff" font-size="16" font-weight="bold">LH TEMP</text>
                <text x="45" y="95" fill="#fff" font-size="20" font-weight="bold">285</text>

                <!-- Hot Air indicator -->
                <circle cx="20" cy="130" r="7" fill="#e74c3c">
                    <animate attributeName="r" values="7;9;7" dur="1s" repeatCount="indefinite" />
                </circle>
                <text x="32" y="135" fill="#fff" font-size="9">Hot Air</text>
            </g>

            <!-- RH Temperature Chamber (Right) -->
            <g transform="translate(680, 240)">
                <rect x="0" y="0" width="90" height="140" rx="6" fill="url(#boilerGrad)" stroke="#2c3e50" stroke-width="2" filter="url(#shadow)" />
                <rect x="5" y="5" width="80" height="130" rx="4" fill="#e67e22" opacity="0.25" />

                <!-- Temperature bar -->
                <rect x="55" y="20" width="20" height="100" rx="3" fill="#34495e" />
                <rect x="57" y="85" width="16" height="0" rx="2" fill="#e67e22">
                    <animate attributeName="height" values="25;40;25" dur="3s" repeatCount="indefinite" />
                    <animate attributeName="y" values="85;70;85" dur="3s" repeatCount="indefinite" />
                </rect>

                <text x="40" y="30" fill="#fff" font-size="12">°C</text>
                <text x="20" y="70" fill="#fff" font-size="16" font-weight="bold">RH TEMP</text>
                <text x="45" y="95" fill="#fff" font-size="20" font-weight="bold">292</text>

                <!-- Hot Air indicator -->
                <circle cx="70" cy="130" r="7" fill="#e67e22">
                    <animate attributeName="r" values="7;9;7" dur="1s" repeatCount="indefinite" />
                </circle>
                <text x="40" y="135" fill="#fff" font-size="9">Hot Air</text>
            </g>

            <!-- LH Components (Bottom Left) -->
            <g transform="translate(200, 420)">
                <!-- LH Guiloutine -->
                <rect x="0" y="0" width="70" height="45" rx="4" fill="#95a5a6" stroke="#7f8c8d" stroke-width="2" filter="url(#shadow)" />
                <text x="35" y="18" text-anchor="middle" fill="#fff" font-size="11" font-weight="bold">LH</text>
                <text x="35" y="32" text-anchor="middle" fill="#fff" font-size="11" font-weight="bold">Guiloutine</text>
                <text x="35" y="43" text-anchor="middle" fill="#f39c12" font-size="9" font-weight="bold">45 mm</text>

                <!-- LH Stoker -->
                <rect x="0" y="55" width="70" height="50" rx="4" fill="#7f8c8d" stroke="#34495e" stroke-width="2" filter="url(#shadow)" />
                <circle cx="35" cy="75" r="12" fill="#2ecc71">
                    <animateTransform attributeName="transform" type="rotate" from="0 35 75" to="360 35 75" dur="2s" repeatCount="indefinite" />
                </circle>
                <text x="35" y="80" text-anchor="middle" fill="#fff" font-size="14" font-weight="bold">M</text>
                <text x="35" y="100" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">LH STOKER</text>

                <!-- LH FD Fan -->
                <rect x="80" y="55" width="70" height="50" rx="4" fill="#34495e" stroke="#2c3e50" stroke-width="2" filter="url(#shadow)" />
                <circle cx="115" cy="75" r="12" fill="#3498db">
                    <animateTransform attributeName="transform" type="rotate" from="0 115 75" to="360 115 75" dur="1.5s" repeatCount="indefinite" />
                </circle>
                <text x="115" y="80" text-anchor="middle" fill="#fff" font-size="14" font-weight="bold">M</text>
                <text x="115" y="100" text-anchor="middle" fill="#fff" font-size="9" font-weight="bold">LHFD</text>
            </g>

            <!-- RH Components (Bottom Right) -->
            <g transform="translate(730, 420)">
                <!-- RH Guiloutine -->
                <rect x="0" y="0" width="70" height="45" rx="4" fill="#95a5a6" stroke="#7f8c8d" stroke-width="2" filter="url(#shadow)" />
                <text x="35" y="18" text-anchor="middle" fill="#fff" font-size="11" font-weight="bold">RH</text>
                <text x="35" y="32" text-anchor="middle" fill="#fff" font-size="11" font-weight="bold">Guiloutine</text>
                <text x="35" y="43" text-anchor="middle" fill="#f39c12" font-size="9" font-weight="bold">48 mm</text>

                <!-- RH Stoker -->
                <rect x="0" y="55" width="70" height="50" rx="4" fill="#7f8c8d" stroke="#34495e" stroke-width="2" filter="url(#shadow)" />
                <circle cx="35" cy="75" r="12" fill="#2ecc71">
                    <animateTransform attributeName="transform" type="rotate" from="0 35 75" to="360 35 75" dur="2s" repeatCount="indefinite" />
                </circle>
                <text x="35" y="80" text-anchor="middle" fill="#fff" font-size="14" font-weight="bold">M</text>
                <text x="35" y="100" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">RH STOKER</text>

                <!-- RH FD Fan (implied, can add if needed) -->
                <rect x="80" y="55" width="70" height="50" rx="4" fill="#34495e" stroke="#2c3e50" stroke-width="2" filter="url(#shadow)" />
                <text x="115" y="82" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">RHFD</text>
            </g>

            <!-- Feed Water Tanks (Bottom Center) -->
            <g transform="translate(400, 500)">
                <!-- Left Tank -->
                <ellipse cx="70" cy="0" rx="45" ry="14" fill="#34495e" />
                <rect x="25" y="0" width="90" height="90" fill="url(#tankGrad)" stroke="#2c3e50" stroke-width="2" filter="url(#shadow)" />
                <ellipse cx="70" cy="90" rx="45" ry="14" fill="#1a252f" />

                <!-- Water inside -->
                <ellipse cx="70" cy="60" rx="38" ry="10" fill="#5dade2" opacity="0.6" />

                <text x="70" y="50" text-anchor="middle" fill="#fff" font-size="28" font-weight="bold">L</text>
                <text x="70" y="110" text-anchor="middle" fill="#34495e" font-size="10">Feed Tank</text>

                <!-- Pump -->
                <circle cx="70" cy="125" r="18" fill="#2ecc71" stroke="#27ae60" stroke-width="3" filter="url(#shadow)">
                    <animateTransform attributeName="transform" type="rotate" from="0 70 125" to="360 70 125" dur="2s" repeatCount="indefinite" />
                </circle>
                <text x="70" y="132" text-anchor="middle" fill="#fff" font-size="16" font-weight="bold">M</text>

                <!-- Right Tank -->
                <ellipse cx="230" cy="0" rx="45" ry="14" fill="#34495e" />
                <rect x="185" y="0" width="90" height="90" fill="url(#tankGrad)" stroke="#2c3e50" stroke-width="2" filter="url(#shadow)" />
                <ellipse cx="230" cy="90" rx="45" ry="14" fill="#1a252f" />

                <!-- Water inside -->
                <ellipse cx="230" cy="60" rx="38" ry="10" fill="#5dade2" opacity="0.6" />

                <text x="230" y="50" text-anchor="middle" fill="#fff" font-size="28" font-weight="bold">R</text>
                <text x="230" y="110" text-anchor="middle" fill="#34495e" font-size="10">Feed Tank</text>

                <!-- Pump -->
                <circle cx="230" cy="125" r="18" fill="#2ecc71" stroke="#27ae60" stroke-width="3" filter="url(#shadow)">
                    <animateTransform attributeName="transform" type="rotate" from="0 230 125" to="360 230 125" dur="2s" repeatCount="indefinite" />
                </circle>
                <text x="230" y="132" text-anchor="middle" fill="#fff" font-size="16" font-weight="bold">M</text>

                <!-- Flow indicators -->
                <text x="150" y="80" text-anchor="middle" fill="#34495e" font-size="11" font-weight="bold">m3/hr</text>
                <rect x="120" y="85" width="60" height="20" rx="3" fill="#fff" stroke="#34495e" stroke-width="1" />
                <text x="150" y="100" text-anchor="middle" fill="#3498db" font-size="12" font-weight="bold">12.5</text>
            </g>

            <!-- Chimney (Far Right) -->
            <g transform="translate(960, 120)">
                <rect x="-5" y="0" width="90" height="15" fill="#7f2e1d" filter="url(#shadow)" />
                <rect x="0" y="15" width="80" height="300" fill="url(#chimneyGrad)" stroke="#7f2e1d" stroke-width="3" filter="url(#shadow)" />
                <rect x="5" y="20" width="70" height="290" fill="#c0392b" opacity="0.2" />

                <!-- Smoke particles -->
                <circle cx="40" cy="30" r="10" fill="#95a5a6" opacity="0.7">
                    <animate attributeName="cy" values="30;-20" dur="3s" repeatCount="indefinite" />
                    <animate attributeName="opacity" values="0.7;0" dur="3s" repeatCount="indefinite" />
                    <animate attributeName="r" values="10;20" dur="3s" repeatCount="indefinite" />
                </circle>
                <circle cx="40" cy="30" r="10" fill="#95a5a6" opacity="0.7">
                    <animate attributeName="cy" values="30;-20" dur="3s" repeatCount="indefinite" begin="1s" />
                    <animate attributeName="opacity" values="0.7;0" dur="3s" repeatCount="indefinite" begin="1s" />
                    <animate attributeName="r" values="10;20" dur="3s" repeatCount="indefinite" begin="1s" />
                </circle>
                <circle cx="40" cy="30" r="10" fill="#95a5a6" opacity="0.7">
                    <animate attributeName="cy" values="30;-20" dur="3s" repeatCount="indefinite" begin="2s" />
                    <animate attributeName="opacity" values="0.7;0" dur="3s" repeatCount="indefinite" begin="2s" />
                    <animate attributeName="r" values="10;20" dur="3s" repeatCount="indefinite" begin="2s" />
                </circle>

                <text x="40" y="160" text-anchor="middle" fill="#fff" font-size="14" font-weight="bold">FLUE</text>
                <text x="40" y="180" text-anchor="middle" fill="#fff" font-size="14" font-weight="bold">GAS</text>
                <rect x="10" y="190" width="60" height="30" rx="3" fill="#34495e" />
                <text x="40" y="210" text-anchor="middle" fill="#f39c12" font-size="16" font-weight="bold">175°C</text>
            </g>

            <!-- ID Fan (Before Chimney) -->
            <g transform="translate(850, 320)">
                <circle cx="50" cy="50" r="42" fill="#34495e" stroke="#2c3e50" stroke-width="3" filter="url(#shadow)" />
                <circle cx="50" cy="50" r="32" fill="#2ecc71" stroke="#27ae60" stroke-width="2">
                    <animateTransform attributeName="transform" type="rotate" from="0 50 50" to="360 50 50" dur="1s" repeatCount="indefinite" />
                </circle>

                <!-- Fan blades -->
                <line x1="50" y1="18" x2="50" y2="82" stroke="#fff" stroke-width="3" />
                <line x1="18" y1="50" x2="82" y2="50" stroke="#fff" stroke-width="3" />
                <line x1="27" y1="27" x2="73" y2="73" stroke="#fff" stroke-width="2" />
                <line x1="73" y1="27" x2="27" y2="73" stroke="#fff" stroke-width="2" />

                <text x="50" y="115" text-anchor="middle" fill="#34495e" font-size="13" font-weight="bold">ID FAN</text>
                <text x="50" y="132" text-anchor="middle" fill="#2ecc71" font-size="12" font-weight="bold">45 Hz</text>
            </g>

            <!-- Pipes connecting components -->
            <!-- Steam output -->
            <path d="M 490 190 Q 490 160 490 150" stroke="url(#pipeGrad)" stroke-width="10" fill="none" opacity="0.8" />

            <!-- Feed water input pipes -->
            <path d="M 470 620 L 470 440" stroke="url(#pipeGrad)" stroke-width="8" fill="none" opacity="0.8" />
            <path d="M 630 620 L 630 440" stroke="url(#pipeGrad)" stroke-width="8" fill="none" opacity="0.8" />

            <!-- Gas exhaust pipe -->
            <path d="M 560 310 Q 700 310 850 370" stroke="url(#pipeGrad)" stroke-width="8" fill="none" opacity="0.8" />
            <path d="M 900 370 Q 950 350 960 240" stroke="url(#pipeGrad)" stroke-width="8" fill="none" opacity="0.8" />

            <!-- Info Panel (Bottom Left) -->
            <g transform="translate(30, 480)">
                <rect x="0" y="0" width="140" height="140" rx="5" fill="#fff" stroke="#34495e" stroke-width="2" filter="url(#shadow)" />

                <text x="70" y="25" text-anchor="middle" fill="#34495e" font-size="13" font-weight="bold">Batubara (FK)</text>
                <text x="70" y="50" text-anchor="middle" fill="#f39c12" font-size="16" font-weight="bold">125 kg/jam</text>

                <line x1="10" y1="65" x2="130" y2="65" stroke="#ccc" stroke-width="1" />

                <text x="70" y="85" text-anchor="middle" fill="#34495e" font-size="13" font-weight="bold">Steam</text>
                <text x="70" y="110" text-anchor="middle" fill="#3498db" font-size="16" font-weight="bold">15.2 m3/jam</text>
            </g>

            <!-- Gas Analysis Panel (Top Right) -->
            <g transform="translate(1050, 380)">
                <rect x="0" y="0" width="120" height="50" rx="4" fill="#fff" stroke="#2ecc71" stroke-width="2" filter="url(#shadow)" />
                <text x="15" y="23" fill="#34495e" font-size="13" font-weight="bold">O2:</text>
                <text x="90" y="23" text-anchor="end" fill="#2ecc71" font-size="14" font-weight="bold">4.2%</text>
                <text x="15" y="42" fill="#7f8c8d" font-size="10">Oxygen</text>

                <rect x="0" y="60" width="120" height="50" rx="4" fill="#fff" stroke="#e74c3c" stroke-width="2" filter="url(#shadow)" />
                <text x="15" y="83" fill="#34495e" font-size="13" font-weight="bold">CO2:</text>
                <text x="90" y="83" text-anchor="end" fill="#e74c3c" font-size="14" font-weight="bold">12.8%</text>
                <text x="15" y="102" fill="#7f8c8d" font-size="10">Carbon Dioxide</text>
            </g>

            <!-- Bar Display (Top Left) -->
            <g transform="translate(30, 120)">
                <rect x="0" y="0" width="120" height="60" rx="5" fill="#fff" stroke="#34495e" stroke-width="2" filter="url(#shadow)" />
                <text x="60" y="25" text-anchor="middle" fill="#34495e" font-size="11" font-weight="bold">Pressure</text>
                <text x="60" y="50" text-anchor="middle" fill="#e74c3c" font-size="20" font-weight="bold">6.8 bar</text>
            </g>
        </svg>

    </div>
</body>

</html>