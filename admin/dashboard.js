document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    var revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        var labels = [];
        var data = [];
        if (typeof chartRevenueData !== 'undefined' && chartRevenueData.length > 0) {
            chartRevenueData.forEach(function(row) {
                labels.push(row.month);
                data.push(parseFloat(row.revenue) || 0);
            });
        } else {
            labels = ['No Data'];
            data = [0];
        }
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue (PKR)',
                    data: data,
                    borderColor: '#06a3da',
                    backgroundColor: 'rgba(6,163,218,0.08)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#06a3da',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 16,
                            font: { size: 12, family: 'Segoe UI' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(ctx) {
                                return 'PKR ' + ctx.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) { return 'PKR ' + (v/1000) + 'K'; },
                            font: { size: 11 },
                            color: '#94a3b8'
                        },
                        grid: { color: '#f1f5f9', drawBorder: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#94a3b8' }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    // Appointments Donut Chart
    var donutCtx = document.getElementById('appointmentsDonutChart');
    if (donutCtx) {
        var apptData = typeof chartApptData !== 'undefined' ? chartApptData : {completed:0, pending:0, cancelled:0, no_show:0};
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Pending', 'Cancelled', 'No Show'],
                datasets: [{
                    data: [apptData.completed || 0, apptData.pending || 0, apptData.cancelled || 0, apptData.no_show || 0],
                    backgroundColor: ['#16a34a', '#06a3da', '#f97316', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { size: 12 },
                        bodyFont: { size: 12 }
                    }
                }
            }
        });
    }

    // Chart filter buttons
    document.querySelectorAll('.grin-filter-group').forEach(function(group) {
        group.querySelectorAll('.grin-filter-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                group.querySelectorAll('.grin-filter-btn').forEach(function(b) { b.classList.remove('active'); });
                btn.classList.add('active');
            });
        });
    });
});
