document.addEventListener('DOMContentLoaded', function() {
    // --- Elemen Umum ---
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const sidebar = document.getElementById('sidebar');
    const navLinks = document.querySelectorAll('.nav-link');
    const tabPanes = document.querySelectorAll('.tab-pane');

    const charts = {};
    const dataTypes = ['ph', 'flow', 'total'];
    const MAX_DATA_POINTS = 50;
    const MAX_TABLE_ROWS = 10;
    const RECORDS_PER_PAGE = 10;

    const configs = {
        ph: { color: { border: 'rgba(52, 152, 219, 1)', bg: 'rgba(52, 152, 219, 0.2)' }, label: 'pH Air' },
        flow: { color: { border: 'rgba(46, 204, 113, 1)', bg: 'rgba(46, 204, 113, 0.2)' }, label: 'Laju Aliran' },
        total: { color: { border: 'rgba(155, 89, 182, 1)', bg: 'rgba(155, 89, 182, 0.2)' }, label: 'Volume Total' }
    };

    // --- Inisialisasi semua grafik ---
    dataTypes.forEach(type => {
        const ctx = document.getElementById(`${type}-chart`).getContext('2d');
        charts[type] = new Chart(ctx, { /* ... konfigurasi chart ... */
            type: 'line', data: { labels: [], datasets: [{ label: configs[type].label, data: [], borderColor: configs[type].color.border, backgroundColor: configs[type].color.bg, borderWidth: 2, fill: true, tension: 0.4 }] },
            options: { responsive: true, scales: { y: { beginAtZero: false } }, plugins: { legend: { display: false } } }
        });
    });

    // --- Fungsi Navigasi Tab ---
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetTab = link.getAttribute('data-tab');
            navLinks.forEach(l => l.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));
            link.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
            sidebar.classList.remove('active');
        });
    });
    hamburgerBtn.addEventListener('click', () => sidebar.classList.toggle('active'));

    // --- Fungsi Update & Fetch Data ---
    function updateLive(type, time, value) {
        // Update Grafik
        charts[type].data.labels.push(time);
        charts[type].data.datasets[0].data.push(value);
        if (charts[type].data.labels.length > MAX_DATA_POINTS) {
            charts[type].data.labels.shift();
            charts[type].data.datasets[0].data.shift();
        }
        charts[type].update();
        
        // Update Tabel Live
        const tableBody = document.getElementById(`${type}-live-table`);
        const newRowHTML = `<tr><td></td><td>${time}</td><td>${value}</td></tr>`;
        tableBody.insertAdjacentHTML('afterbegin', newRowHTML);
        if (tableBody.rows.length > MAX_TABLE_ROWS) {
            tableBody.deleteRow(tableBody.rows.length - 1);
        }
        for (let i = 0; i < tableBody.rows.length; i++) {
            tableBody.rows[i].cells[0].innerText = i + 1;
        }

        // Update Nilai Terakhir di Judul
        document.getElementById(`${type}-latest-value`).innerText = `${configs[type].label} ${value}`;
    }

    async function fetchHistoryData(type, page = 1) {
        try {
            const response = await fetch(`get_history.php?data=${type}&page=${page}`);
            const result = await response.json();
            
            const historyTableBody = document.getElementById(`${type}-history-table`);
            historyTableBody.innerHTML = '';
            const offset = (page - 1) * RECORDS_PER_PAGE;

            result.data.forEach((row, index) => {
                const rowNumber = offset + index + 1;
                const tr = `<tr><td>${rowNumber}</td><td>${row.time_label}</td><td>${row.value}</td></tr>`;
                historyTableBody.innerHTML += tr;
            });
            renderPagination(type, result.total_pages, result.current_page);

            if (page === 1) {
                const liveTableBody = document.getElementById(`${type}-live-table`);
                liveTableBody.innerHTML = '';
                 result.data.forEach((row, index) => {
                    if (index < MAX_TABLE_ROWS) {
                        const tr = `<tr><td>${index + 1}</td><td>${row.time_label}</td><td>${row.value}</td></tr>`;
                        liveTableBody.innerHTML += tr;
                    }
                });
            }
        } catch (error) { console.error(`Gagal ambil riwayat ${type}:`, error); }
    }
    
    // ========================================================================
    // ### FUNGSI renderPagination YANG DIPERBARUI ###
    // ========================================================================
    function renderPagination(type, totalPages, currentPage) {
        const container = document.getElementById(`${type}-pagination`);
        container.innerHTML = '';
        if (totalPages <= 1) return; // Jangan tampilkan jika hanya 1 halaman

        const createLink = (page, text = page, isDisabled = false) => {
            const link = document.createElement('a');
            link.href = '#';
            link.innerText = text;
            if (isDisabled) {
                link.classList.add('disabled');
            } else {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    fetchHistoryData(type, page);
                });
            }
            return link;
        };

        // Tombol "Sebelumnya"
        container.appendChild(createLink(currentPage - 1, '‹', currentPage === 1));

        // Aturan untuk menampilkan nomor halaman
        const siblingCount = 1;
        const pagesToShow = [];

        for (let i = 1; i <= totalPages; i++) {
            if (
                i === 1 || // selalu tampilkan halaman pertama
                i === totalPages || // selalu tampilkan halaman terakhir
                (i >= currentPage - siblingCount && i <= currentPage + siblingCount) // tampilkan halaman di sekitar halaman aktif
            ) {
                pagesToShow.push(i);
            }
        }

        let lastPage = 0;
        pagesToShow.forEach(page => {
            // Jika ada jeda antara nomor, tampilkan elipsis "..."
            if (lastPage !== 0 && page > lastPage + 1) {
                const ellipsis = document.createElement('span');
                ellipsis.innerText = '...';
                container.appendChild(ellipsis);
            }
            const link = createLink(page);
            if (page === currentPage) {
                link.classList.add('active');
            }
            container.appendChild(link);
            lastPage = page;
        });

        // Tombol "Selanjutnya"
        container.appendChild(createLink(currentPage + 1, '›', currentPage === totalPages));
    }
    // ========================================================================

    // --- Koneksi Real-time (SSE) ---
    const eventSource = new EventSource('sse_server.php');
    eventSource.onmessage = function(event) {
        const dataPoint = JSON.parse(event.data);
        updateLive('ph', dataPoint.time_label, dataPoint.ph_value);
        updateLive('flow', dataPoint.time_label, dataPoint.flow_rate);
        updateLive('total', dataPoint.time_label, dataPoint.total_volume);
    };
    eventSource.onerror = (err) => console.error("EventSource failed:", err);
    
    // --- Inisialisasi Data Riwayat untuk semua tab ---
    dataTypes.forEach(type => fetchHistoryData(type, 1));
});