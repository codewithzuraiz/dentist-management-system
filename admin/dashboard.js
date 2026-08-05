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
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79,70,229,0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
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
                            callback: function(v) { return 'PKR ' + (v/1000) + 'K'; }
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: { grid: { display: false } }
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
                    backgroundColor: ['#22c55e', '#3b82f6', '#f97316', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: { legend: { display: false } }
            }
        });
    }
});
