<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Botol Pintar - Monitoring Kualitas Air Real-time</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* Gaya Dasar & Font */
        body { 
            font-family: 'Inter', sans-serif; 
            color: #e0e0e0;
            background-color: #0a0a0f; /* Darker than original */
            background-image: radial-gradient(at 10% 80%, rgba(34, 211, 238, 0.15) 0, transparent 50%),
                                 radial-gradient(at 90% 20%, rgba(34, 211, 238, 0.15) 0, transparent 50%);
            background-attachment: fixed;
            transition: background-color 0.5s ease;
        }

        /* Glassmorphism Effect */
        .glassmorphism {
            background-color: rgba(43, 43, 56, 0.2);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        /* Neon Glow Effect */
        .glow-cyan-md { box-shadow: 0 0 15px rgba(34, 211, 238, 0.6); }
        .glow-cyan-lg { box-shadow: 0 0 25px rgba(34, 211, 238, 0.8), 0 0 40px rgba(34, 211, 238, 0.4); }

        /* Custom Hover Animations */
        .card-hover:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 10px 20px rgba(34, 211, 238, 0.25);
        }
        .btn-hover:hover {
            transform: scale(1.05) translateY(-3px);
            box-shadow: 0 0 20px rgba(34, 211, 238, 0.7);
        }

        /* Hero Title Animation */
        .hero-title {
            text-shadow: 0 0 10px rgba(34, 211, 238, 0.5);
        }

        /* Styling for specific elements */
        .nav-link-active { color: #22d3ee; }
        .text-cyber-blue { color: #22d3ee; }
        .bg-cyber-blue { background-color: #22d3ee; }
        .hover\:text-cyber-blue:hover { color: #22d3ee; }
        .bg-indigo-600 { background-color: #4f46e5; }
        .hover\:bg-indigo-700:hover { background-color: #6366f1; }
        .bg-slate-800 { background-color: #1a1a1e; }
        .border-slate-700 { border-color: #2a2a2e; }

        /* Pagination Style */
        .pagination a {
            transition: all 0.3s ease;
            box-shadow: none;
            border: 1px solid transparent;
            padding: 8px 12px;
            margin: 0 4px;
            border-radius: 8px;
            display: inline-block;
        }
        .pagination a.active, .pagination a:hover {
            background-color: #22d3ee;
            color: #0a0a0f;
            border-color: #22d3ee;
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 1rem;
            color: #94a3b8;
        }
        .pagination span {
            margin: 0 8px;
        }


        /* Chart.js overrides */
        #ph-chart, #flow-chart, #total-chart {
            background: linear-gradient(135deg, rgba(34, 211, 238, 0.05), rgba(34, 211, 238, 0.01));
        }
        .chartjs-tooltip {
            background: rgba(10, 10, 15, 0.8) !important;
            border: 1px solid #22d3ee !important;
            color: #fff !important;
            border-radius: 8px !important;
        }

        .reveal { visibility: hidden; }
    </style>
</head>
<body class="text-slate-100">

    <header id="header" class="glassmorphism sticky top-0 z-50 transition-all duration-300">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="#" class="flex items-center space-x-2">
                <svg class="w-8 h-8 text-cyber-blue" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 0 0 4.5 4.5H18a3.75 3.75 0 0 0 1.332-7.257 3 3 0 0 0-3.758-3.758A3.75 3.75 0 0 0 12 3c-1.268 0-2.417.435-3.332 1.167C6.75 5.42 6 6.674 6 8.25a4.5 4.5 0 0 0 4.5 4.5H13.5" /></svg>
                <span class="text-xl font-black text-cyber-blue">Botol Pintar</span>
            </a>
            <nav class="hidden md:flex space-x-8">
                <a href="#hero" class="nav-link text-slate-300 hover:text-cyber-blue font-semibold transition-colors">Home</a>
                <a href="#about" class="nav-link text-slate-300 hover:text-cyber-blue font-semibold transition-colors">Tentang</a>
                <a href="#sensors" class="nav-link text-slate-300 hover:text-cyber-blue font-semibold transition-colors">Sensor</a>
            </nav>
            <a href="#sensors" class="hidden md:block bg-cyber-blue text-slate-900 font-bold py-2 px-5 rounded-full transition-all btn-hover">Lihat Data</a>
            <button id="menu-btn" class="md:hidden focus:outline-none"><svg class="w-6 h-6 text-slate-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg></button>
        </div>
        <div id="mobile-menu" class="hidden md:hidden glassmorphism">
            <a href="#hero" class="block py-2 px-6 text-sm hover:bg-slate-700 text-slate-200">Home</a>
            <a href="#about" class="block py-2 px-6 text-sm hover:bg-slate-700 text-slate-200">Tentang</a>
            <a href="#sensors" class="block py-2 px-6 text-sm hover:bg-slate-700 text-slate-200">Sensor</a>
        </div>
    </header>

    <main>
        <section id="hero" class="relative min-h-[80vh] flex items-center overflow-hidden">
            <div class="container mx-auto px-6 py-20 text-center">
                <div class="reveal max-w-4xl mx-auto">
                    <h1 class="hero-title text-5xl md:text-7xl font-black text-slate-100 leading-tight mb-6">Pantau Kualitas Air Minum Anda, <span class="text-cyber-blue">Secara Real-time.</span></h1>
                    <p class="text-lg md:text-xl text-slate-300 mb-10">Botol Pintar adalah solusi inovatif untuk memastikan air yang Anda konsumsi setiap hari memiliki kualitas terbaik, langsung dari genggaman Anda.</p>
                    <a href="#sensors" class="bg-cyber-blue text-slate-900 font-bold py-4 px-10 rounded-full inline-block transition-all btn-hover glow-cyan-lg">Mulai Monitoring</a>
                </div>
            </div>
        </section>

        <section id="about" class="py-20">
            <div class="container mx-auto px-6">
                <div class="text-center mb-12 reveal">
                    <h2 class="text-3xl font-extrabold text-slate-100">Tentang Proyek Botol Pintar</h2>
                    <p class="mt-2 text-lg text-slate-400 max-w-2xl mx-auto">Sebuah langkah maju dalam teknologi kesehatan personal, dirancang untuk memberikan ketenangan pikiran.</p>
                </div>
                <div class="max-w-3xl mx-auto reveal glassmorphism p-8 rounded-3xl">
                    <h3 class="text-2xl font-bold text-slate-100 mb-4 text-center">Bagaimana Cara Kerjanya?</h3>
                    <p class="text-slate-400 leading-relaxed mb-6 text-center">Botol Pintar dilengkapi dengan serangkaian sensor canggih yang terintegrasi untuk mengukur parameter-parameter kunci kualitas air. Data yang terkumpul kemudian dikirim secara nirkabel ke server kami, dan disajikan secara real-time melalui halaman ini.</p>
                    <ul class="space-y-4">
                        <li class="flex items-start reveal transition-transform duration-300 hover:scale-105">
                            <span class="bg-cyber-blue text-slate-900 rounded-full w-8 h-8 text-sm flex items-center justify-center font-bold mr-4 mt-1 shrink-0">1</span>
                            <span class="text-slate-300">Sensor mengukur pH, laju aliran, dan total volume air yang diminum.</span>
                        </li>
                        <li class="flex items-start reveal transition-transform duration-300 hover:scale-105">
                            <span class="bg-cyber-blue text-slate-900 rounded-full w-8 h-8 text-sm flex items-center justify-center font-bold mr-4 mt-1 shrink-0">2</span>
                            <span class="text-slate-300">Data dikirim melalui mikrokontroler ke web server secara periodik.</span>
                        </li>
                        <li class="flex items-start reveal transition-transform duration-300 hover:scale-105">
                            <span class="bg-cyber-blue text-slate-900 rounded-full w-8 h-8 text-sm flex items-center justify-center font-bold mr-4 mt-1 shrink-0">3</span>
                            <span class="text-slate-300">Halaman ini menerima data dan menampilkannya secara live untuk Anda.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section id="sensors" class="py-20">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16 reveal">
                    <h2 class="text-4xl font-extrabold text-slate-100">Live Data Monitoring</h2>
                    <p class="mt-2 text-lg text-slate-400 max-w-2xl mx-auto">Lacak setiap parameter kualitas air Anda langsung dari sensor yang terpasang.</p>
                </div>

                <div class="space-y-16">
                    
                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <div class="order-2 md:order-1 reveal">
                            <h3 class="text-3xl font-bold text-slate-100 mb-2">Tingkat Keasaman (pH)</h3>
                            <p class="text-slate-400 mb-6">Memastikan air Anda tidak terlalu asam atau basa. pH ideal untuk air minum adalah antara 6.5 hingga 8.5.</p>
                            <div class="glassmorphism p-6 rounded-2xl border border-slate-700/50 card-hover transition-all duration-300">
                                <p class="text-sm font-medium text-slate-400">Nilai pH Terakhir</p>
                                <p id="ph-latest-value" class="text-5xl font-extrabold text-cyber-blue my-2 glow-cyan-md">0.00</p>
                                <p class="text-xs text-slate-500">Data diperbarui secara real-time.</p>
                            </div>
                        </div>
                        <div class="order-1 md:order-2 glassmorphism p-4 rounded-3xl h-80 reveal card-hover transition-all duration-300">
                            <canvas id="ph-chart"></canvas>
                        </div>
                    </div>
                    
                    <div class="glassmorphism p-6 rounded-2xl reveal card-hover transition-all duration-300 mt-10 md:mt-10">
                        <h4 class="text-xl font-bold text-slate-100 mb-4">Riwayat Data pH</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-slate-800/50 text-slate-400 uppercase">
                                    <tr>
                                        <th class="p-3 rounded-l-lg">No.</th>
                                        <th class="p-3">Waktu</th>
                                        <th class="p-3 rounded-r-lg">Nilai (pH)</th>
                                    </tr>
                                </thead>
                                <tbody id="ph-history-table" class="divide-y divide-slate-700/50"></tbody>
                            </table>
                        </div>
                        <div id="ph-pagination" class="pagination"></div>
                    </div>


                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <div class="reveal">
                            <h3 class="text-3xl font-bold text-slate-100 mb-2">Laju Aliran Air</h3>
                            <p class="text-slate-400 mb-6">Memberikan informasi tentang seberapa deras Anda minum, berguna untuk memonitor kebiasaan hidrasi.</p>
                            <div class="glassmorphism p-6 rounded-2xl border border-slate-700/50 card-hover transition-all duration-300">
                                <p class="text-sm font-medium text-slate-400">Laju Aliran Terakhir</p>
                                <p id="flow-latest-value" class="text-5xl font-extrabold text-green-400 my-2">0.00 <span class="text-2xl font-semibold text-slate-500">L/m</span></p>
                                <p class="text-xs text-slate-500">Data diperbarui secara real-time.</p>
                            </div>
                        </div>
                        <div class="glassmorphism p-4 rounded-3xl h-80 reveal card-hover transition-all duration-300">
                            <canvas id="flow-chart"></canvas>
                        </div>
                    </div>

                    <div class="glassmorphism p-6 rounded-2xl reveal card-hover transition-all duration-300 mt-10 md:mt-10">
                        <h4 class="text-xl font-bold text-slate-100 mb-4">Riwayat Data Laju Aliran</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-slate-800/50 text-slate-400 uppercase">
                                    <tr>
                                        <th class="p-3 rounded-l-lg">No.</th>
                                        <th class="p-3">Waktu</th>
                                        <th class="p-3 rounded-r-lg">Nilai (L/m)</th>
                                    </tr>
                                </thead>
                                <tbody id="flow-history-table" class="divide-y divide-slate-700/50"></tbody>
                            </table>
                        </div>
                        <div id="flow-pagination" class="pagination"></div>
                    </div>
                
                    
                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <div class="order-2 md:order-1 reveal">
                            <h3 class="text-3xl font-bold text-slate-100 mb-2">Total Volume Konsumsi</h3>
                            <p class="text-slate-400 mb-6">Mencatat total jumlah air yang telah Anda minum untuk memastikan target hidrasi harian tercapai.</p>
                            <div class="glassmorphism p-6 rounded-2xl border border-slate-700/50 card-hover transition-all duration-300">
                                <p class="text-sm font-medium text-slate-400">Total Volume Hari Ini</p>
                                <p id="total-latest-value" class="text-5xl font-extrabold text-indigo-400 my-2">0.00 <span class="text-2xl font-semibold text-slate-500">Liter</span></p>
                                <p class="text-xs text-slate-500">Data diperbarui secara real-time.</p>
                            </div>
                        </div>
                        <div class="order-1 md:order-2 glassmorphism p-4 rounded-3xl h-80 reveal card-hover transition-all duration-300">
                            <canvas id="total-chart"></canvas>
                        </div>
                    </div>

                    <div class="glassmorphism p-6 rounded-2xl reveal card-hover transition-all duration-300 mt-10 md:mt-10">
                        <h4 class="text-xl font-bold text-slate-100 mb-4">Riwayat Data Volume Total</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-slate-800/50 text-slate-400 uppercase">
                                    <tr>
                                        <th class="p-3 rounded-l-lg">No.</th>
                                        <th class="p-3">Waktu</th>
                                        <th class="p-3 rounded-r-lg">Nilai (L)</th>
                                    </tr>
                                </thead>
                                <tbody id="total-history-table" class="divide-y divide-slate-700/50"></tbody>
                            </table>
                        </div>
                        <div id="total-pagination" class="pagination"></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-transparent text-slate-500 py-10"><div class="container mx-auto px-6 text-center"><p>&copy; Proyek Botol Pintar . IRWAN HADI.</p></div></footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Navigasi & UI Interactivity ---
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        menuBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
        const header = document.getElementById('header');
        window.onscroll = () => { header.classList.toggle('shadow-xl', window.scrollY > 50); };

        // --- Konfigurasi Chart & Data ---
        const charts = {};
        const dataTypes = ['ph', 'flow', 'total'];
        const MAX_DATA_POINTS = 30;
        const RECORDS_PER_PAGE = 5;

        const configs = {
            ph: 	{ color: 'rgba(34, 211, 238, 1)', label: 'pH', unit: '' },
            flow: 	{ color: 'rgb(74, 222, 128)', label: 'Laju Aliran', unit: ' L/m' },
            total: { color: 'rgb(129, 140, 248)', label: 'Volume Total', unit: ' L' }
        };

        dataTypes.forEach(type => {
            const ctx = document.getElementById(`${type}-chart`).getContext('2d');
            charts[type] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: configs[type].label,
                        data: [],
                        borderColor: configs[type].color,
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: configs[type].color,
                        pointBorderColor: '#0a0a0f',
                        pointBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: type !== 'ph',
                            grid: { color: 'rgba(255, 255, 255, 0.1)', drawBorder: false },
                            ticks: { color: '#94a3b8', font: { size: 12 } }
                        },
                        x: {
                            grid: { color: 'rgba(255, 255, 255, 0.1)', drawBorder: false },
                            ticks: { color: '#94a3b8', font: { size: 12 } }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(10, 10, 15, 0.8)',
                            borderColor: '#22d3ee',
                            borderWidth: 1,
                            cornerRadius: 8,
                            titleColor: '#22d3ee',
                            bodyColor: '#e0e0e0',
                        }
                    },
                    animation: {
                        duration: 800,
                        easing: 'easeInOutQuad'
                    }
                }
            });
        });

        // --- Fungsi Update & Fetch Data ---
        function updateLiveDisplay(type, value) {
            const timeOnly = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            charts[type].data.labels.push(timeOnly);
            charts[type].data.datasets[0].data.push(value);
            if (charts[type].data.labels.length > MAX_DATA_POINTS) {
                charts[type].data.labels.shift();
                charts[type].data.datasets[0].data.shift();
            }
            charts[type].update();
            const latestValueEl = document.getElementById(`${type}-latest-value`);
            const unitSpan = type === 'flow' ? ' <span class="text-2xl font-semibold text-slate-500">L/m</span>' : (type === 'total' ? ' <span class="text-2xl font-semibold text-slate-500">Liter</span>' : '');
            latestValueEl.innerHTML = `${value}${unitSpan}`;
        }

        async function fetchHistoryData(type, page = 1) {
            try {
                // Pastikan file ini ada di server Anda
                const response = await fetch(`get_history.php?data=${type}&page=${page}&limit=${RECORDS_PER_PAGE}`);
                const result = await response.json();
                
                const historyTableBody = document.getElementById(`${type}-history-table`);
                historyTableBody.innerHTML = '';
                
                const offset = (page - 1) * RECORDS_PER_PAGE;
                
                // Membalikkan data agar data terbaru ada di atas tabel riwayat
                const displayData = result.data.slice().reverse(); 

                displayData.forEach((row, index) => {
                    const rowNumber = result.total_records - (offset + index);
                    historyTableBody.innerHTML += `<tr class="hover:bg-slate-800/50"><td class="p-3 font-medium text-slate-400">${rowNumber}</td><td class="p-3 text-slate-300">${row.time_label}</td><td class="p-3 font-semibold text-slate-100">${row.value}</td></tr>`;
                });
                
                renderPagination(type, result.total_pages, result.current_page);
                
                if (charts[type].data.datasets[0].data.length === 0 && result.data.length > 0) {
                    const initialChartData = result.data.slice().reverse();
                    charts[type].data.labels = initialChartData.map(d => new Date(d.time_label).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }));
                    charts[type].data.datasets[0].data = initialChartData.map(d => d.value);
                    charts[type].update();
                    
                    if(result.data.length > 0) updateLiveDisplay(type, result.data[result.data.length - 1].value); 
                }
            } catch (error) { 
                console.error(`Gagal mengambil riwayat ${type}:`, error); 
                document.getElementById(`${type}-history-table`).innerHTML = '<tr><td colspan="3" class="p-4 text-center text-slate-500">Gagal memuat data riwayat. Pastikan `get_history.php` berfungsi dan terhubung ke DB.</td></tr>';
            }
        }

        function renderPagination(type, totalPages, currentPage) {
            const container = document.getElementById(`${type}-pagination`);
            container.innerHTML = '';
            if (totalPages <= 1) return;
            
            const createLink = (page, text, isDisabled = false, isActive = false) => {
                const link = document.createElement('a');
                link.href = '#';
                link.innerHTML = text;
                link.classList.add('px-3', 'py-1', 'rounded', 'font-medium');
                
                if (isDisabled) link.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                if (isActive) link.classList.add('active');
                
                if (!isDisabled) link.addEventListener('click', (e) => { 
                    e.preventDefault(); 
                    fetchHistoryData(type, page); 
                });
                return link;
            };

            container.appendChild(createLink(currentPage - 1, '‹', currentPage === 1));

            const pagesToShow = new Set([1, totalPages, currentPage, currentPage - 1, currentPage + 1]);
            let lastPage = 0;
            
            Array.from(pagesToShow).sort((a,b)=>a-b).forEach(page => {
                if (page > 0 && page <= totalPages) {
                    if (lastPage !== 0 && page > lastPage + 1) {
                        const ellipsis = document.createElement('span');
                        ellipsis.innerHTML = '...';
                        container.appendChild(ellipsis);
                    }
                    container.appendChild(createLink(page, page, false, page === currentPage));
                    lastPage = page;
                }
            });

            container.appendChild(createLink(currentPage + 1, '›', currentPage === totalPages));
        }

        // --- Koneksi Real-time (SSE) & Inisialisasi ---
        const eventSource = new EventSource('sse_server.php');
        eventSource.onmessage = function(event) {
            try {
                const dataPoint = JSON.parse(event.data);
                // Bagian ini sekarang hanya fokus update nilai live dan chart, jadi sangat cepat!
                updateLiveDisplay('ph', parseFloat(dataPoint.ph_value).toFixed(2));
                updateLiveDisplay('flow', parseFloat(dataPoint.flow_rate).toFixed(2));
                updateLiveDisplay('total', parseFloat(dataPoint.total_volume).toFixed(2));
            } catch (e) {
                console.error("Gagal parse data SSE:", e, event.data);
            }
        };
        eventSource.onerror = (err) => console.error("Koneksi SSE gagal:", err);

        // Fetch data pertama kali untuk inisialisasi chart dan tabel
        dataTypes.forEach(type => fetchHistoryData(type, 1));

        // Tambahkan ini: Refresh tabel riwayat setiap 30 detik secara terpisah
        setInterval(() => {
            console.log('Refreshing history tables...');
            dataTypes.forEach(type => fetchHistoryData(type, 1));
        }, 30000); // 30000 milidetik = 30 detik


        // --- INISIALISASI SCROLLREVEAL ---
        const sr = ScrollReveal({
            origin: 'bottom',
            distance: '60px',
            duration: 1500,
            delay: 200,
            reset: false
        });

        sr.reveal('.reveal');
        sr.reveal('#about ul li', { interval: 100 });
        sr.reveal('#hero .reveal', { origin: 'top' });

    });
    </script>
</body>
</html>