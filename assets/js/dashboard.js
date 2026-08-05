document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined') {
        initDashboard();
    } else {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        script.onload = function() {
            initDashboard();
        };
        document.head.appendChild(script);
    }
});

function initDashboard() {
    initRevenueChart();
    initAppointmentsChart();
    initInteractiveElements();
}

function initRevenueChart() {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;
    
    const data = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Revenue',
            data: [2200000, 2350000, 2400000, 2550000, 2480000, 2600000, 2750000, 2800000, 2950000, 3000000, 3100000, 2450000],
            borderColor: '#00b4d8',
            backgroundColor: 'rgba(0, 180, 216, 0.1)',
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#00b4d8',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
        }]
    };
    
    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#112240',
                    bodyColor: '#555',
                    borderColor: '#e8ecf1',
                    borderWidth: 1,
                    padding: 12,
                    boxPadding: 8,
                    callbacks: {
                        label: function(context) {
                            return 'PKR ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#888',
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#888',
                        font: {
                            size: 12
                        },
                        callback: function(value) {
                            return 'PKR ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    };
    
    new Chart(ctx, config);
    
    const filterBtns = document.querySelectorAll('.grin-filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            updateChartData(this.dataset.period);
        });
    });
    
    const chartContainer = document.querySelector('.grin-chart-container');
    const tooltip = document.getElementById('chartTooltip');
    
    chartContainer.addEventListener('mousemove', function(e) {
        const rect = chartContainer.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        if (x >= 0 && x <= rect.width && y >= 0 && y <= rect.height) {
            tooltip.style.display = 'block';
            tooltip.style.left = (e.clientX + 15) + 'px';
            tooltip.style.top = (e.clientY - 30) + 'px';
        } else {
            tooltip.style.display = 'none';
        }
    });
    
    chartContainer.addEventListener('mouseleave', function() {
        tooltip.style.display = 'none';
    });
}

function updateChartData(period) {
    let data;
    let label;
    
    switch(period) {
        case '7':
            label = 'Last 7 Days';
            data = [85000, 92000, 88000, 95000, 102000, 98000, 125000];
            break;
        case '30':
            label = 'Last 30 Days';
            data = [420000, 435000, 418000, 425000, 440000, 432000, 428000, 415000, 432000, 425000, 418000, 440000, 445000, 452000, 428000, 435000, 442000, 418000, 425000, 430000, 428000, 435000, 440000, 425000, 418000, 432000, 425000, 418000, 435000, 428000];
            break;
        case '90':
            label = 'Last 90 Days';
            data = [650000, 680000, 665000, 690000, 710000, 705000, 720000, 685000, 695000, 710000, 725000, 715000, 735000];
            break;
        case '365':
            label = 'Last 365 Days';
            data = [2200000, 2350000, 2400000, 2550000, 2480000, 2600000, 2750000, 2800000, 2950000, 3000000, 3100000, 2450000];
            break;
    }
    
    const chart = Chart.getChart('revenueChart');
    if (chart) {
        chart.data.datasets[0].data = data;
        chart.update();
    }
}

function initAppointmentsChart() {
    const ctx = document.getElementById('appointmentsDonutChart');
    if (!ctx) return;
    
    const data = {
        labels: ['Completed', 'Pending', 'Cancelled', 'No Show'],
        datasets: [{
            data: [18, 9, 3, 2],
            backgroundColor: ['#2ecc71', '#00b4d8', '#f39c12', '#e74c3c'],
            borderColor: '#fff',
            borderWidth: 2
        }]
    };
    
    const config = {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#112240',
                    bodyColor: '#555',
                    borderColor: '#e8ecf1',
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw;
                            const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    };
    
    new Chart(ctx, config);
}

function initInteractiveElements() {
    initStatCards();
    initNotifications();
    initProfileDropdown();
    initSearch();
    initStatusFilter();
    initPagination();
    initQuickActions();
}

function initStatCards() {
    const statCards = document.querySelectorAll('.grin-stat-card');
    statCards.forEach(card => {
        card.addEventListener('click', function() {
            this.classList.toggle('grin-stat-card-clicked');
            setTimeout(() => {
                this.classList.remove('grin-stat-card-clicked');
            }, 200);
        });
    });
}

function initNotifications() {
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    if (!notificationBtn || !notificationDropdown) return;
    
    notificationBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        notificationDropdown.classList.toggle('show');
    });
    
    document.addEventListener('click', function(e) {
        if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.classList.remove('show');
        }
    });
    
    const notificationItems = document.querySelectorAll('.grin-notification-item');
    notificationItems.forEach(item => {
        item.addEventListener('click', function() {
            this.classList.add('read');
            const badge = document.querySelector('.grin-notification-badge');
            if (badge) {
                let count = parseInt(badge.textContent);
                if (count > 0) {
                    count--;
                    badge.textContent = count;
                    if (count === 0) {
                        badge.style.display = 'none';
                    }
                }
            }
        });
    });
}

function initProfileDropdown() {
    const profileBtn = document.getElementById('profileDropdownBtn');
    const profileDropdown = document.getElementById('profileDropdown');
    
    if (!profileBtn || !profileDropdown) return;
    
    profileBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        profileDropdown.classList.toggle('show');
    });
    
    document.addEventListener('click', function(e) {
        if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
            profileDropdown.classList.remove('show');
        }
    });
}

function initSearch() {
    const searchInput = document.querySelector('.grin-search-input');
    if (!searchInput) return;
    
    searchInput.addEventListener('focus', function() {
        this.placeholder = 'Search patients, dentists...';
    });
    
    searchInput.addEventListener('blur', function() {
        this.placeholder = 'Search patients, dentists...';
    });
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('.grin-upcoming-table tbody tr');
        
        tableRows.forEach(row => {
            const patientText = row.querySelector('td').textContent.toLowerCase();
            const dentistText = row.querySelectorAll('td')[1].textContent.toLowerCase();
            
            if (patientText.includes(searchTerm) || dentistText.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

function initStatusFilter() {
    const statusFilter = document.querySelector('.grin-status-filter');
    if (!statusFilter) return;
    
    statusFilter.addEventListener('change', function() {
        const status = this.value;
        const tableRows = document.querySelectorAll('.grin-upcoming-table tbody tr');
        
        tableRows.forEach(row => {
            const statusBadge = row.querySelector('.grin-badge');
            const statusText = statusBadge ? statusBadge.textContent.toLowerCase() : '';
            
            if (status === 'all' || status === statusText) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

function initPagination() {
    const paginationBtns = document.querySelectorAll('.grin-pagination-btn');
    if (!paginationBtns.length) return;
    
    paginationBtns.forEach(btn => {
        if (btn.tagName === 'BUTTON' && !btn.disabled) {
            btn.addEventListener('click', function(e) {
                if (this.textContent.includes('◌') || this.textContent.includes('…')) {
                    return;
                }
                
                paginationBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                if (this.textContent.includes('←')) {
                    const prevBtn = this.previousElementSibling;
                    if (prevBtn && !prevBtn.textContent.includes('◌') && !prevBtn.textContent.includes('…')) {
                        prevBtn.classList.add('active');
                    }
                } else if (this.textContent.includes('→')) {
                    const nextBtn = this.nextElementSibling;
                    if (nextBtn && !nextBtn.textContent.includes('◌') && !nextBtn.textContent.includes('…')) {
                        nextBtn.classList.add('active');
                    }
                }
            });
        }
    });
}

function initQuickActions() {
    const quickActionBtns = document.querySelectorAll('.grin-quick-action-btn');
    quickActionBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const icon = this.querySelector('.grin-action-icon i');
            const originalIcon = icon ? icon.className : '';
            
            this.classList.add('grin-action-clicked');
            
            setTimeout(() => {
                if (icon) {
                    icon.className = 'bx bx-check';
                }
                setTimeout(() => {
                    if (icon) {
                        icon.className = originalIcon;
                    }
                    this.classList.remove('grin-action-clicked');
                }, 1000);
            }, 200);
            
            this.textContent = 'Completed!';
            setTimeout(() => {
                this.textContent = this.querySelector('span').textContent;
            }, 2000);
        });
    });
}

window.addEventListener('resize', function() {
    if (typeof Chart !== 'undefined') {
        Chart.helpers.debounce(300, function() {
            const charts = document.querySelectorAll('canvas');
            charts.forEach(chart => {
                const chartInstance = Chart.getChart(chart);
                if (chartInstance) {
                    chartInstance.resize();
                }
            });
        })();
    }
});
