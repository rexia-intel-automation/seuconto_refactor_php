/**
 * Admin Charts - Gráficos e visualizações para o painel administrativo
 * Utiliza Chart.js para renderizar gráficos
 */

// Global chart instances
let ordersChartInstance = null;
let themesChartInstance = null;

/**
 * Configuração padrão para todos os gráficos
 */
const defaultChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                padding: 15,
                usePointStyle: true,
                font: {
                    size: 12,
                    family: "'Inter', sans-serif"
                }
            }
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: {
                size: 13,
                weight: 'bold'
            },
            bodyFont: {
                size: 12
            },
            cornerRadius: 8,
            displayColors: true
        }
    }
};

/**
 * Palette de cores consistente
 */
const chartColors = {
    primary: '#667eea',
    success: '#10b981',
    warning: '#f59e0b',
    danger: '#ef4444',
    info: '#3b82f6',
    purple: '#8b5cf6',
    pink: '#ec4899',
    indigo: '#6366f1',
    teal: '#14b8a6',
    orange: '#f97316'
};

/**
 * Cores para temas específicos
 */
const themeColors = {
    'aventura': '#FF6B6B',
    'fantasia': '#9B59B6',
    'ciencia': '#3498DB',
    'natureza': '#2ECC71',
    'espaco': '#34495E',
    'default': '#95A5A6'
};

/**
 * Inicializa o gráfico de pedidos (linha)
 * @param {Array} data - Dados do gráfico [{date, count, revenue}]
 */
function initOrdersChart(data) {
    const ctx = document.getElementById('ordersChart');
    if (!ctx) {
        console.error('Canvas #ordersChart não encontrado');
        return;
    }

    // Destruir instância anterior se existir
    if (ordersChartInstance) {
        ordersChartInstance.destroy();
    }

    // Preparar dados
    const labels = data.map(item => {
        const date = new Date(item.date);
        return date.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' });
    });

    const orderCounts = data.map(item => item.count || 0);
    const revenues = data.map(item => (item.revenue || 0) / 100); // Converter centavos para reais

    // Criar gráfico
    ordersChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pedidos',
                    data: orderCounts,
                    borderColor: chartColors.primary,
                    backgroundColor: chartColors.primary + '20',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'Receita (R$)',
                    data: revenues,
                    borderColor: chartColors.success,
                    backgroundColor: chartColors.success + '20',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            ...defaultChartOptions,
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toFixed(2);
                        },
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            },
            plugins: {
                ...defaultChartOptions.plugins,
                tooltip: {
                    ...defaultChartOptions.plugins.tooltip,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.datasetIndex === 1) {
                                // Receita
                                label += 'R$ ' + context.parsed.y.toFixed(2);
                            } else {
                                // Pedidos
                                label += context.parsed.y + ' pedido(s)';
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
}

/**
 * Inicializa o gráfico de temas (doughnut/pizza)
 * @param {Array} data - Dados do gráfico [{theme, name, count}]
 */
function initThemesChart(data) {
    const ctx = document.getElementById('themesChart');
    if (!ctx) {
        console.error('Canvas #themesChart não encontrado');
        return;
    }

    // Destruir instância anterior se existir
    if (themesChartInstance) {
        themesChartInstance.destroy();
    }

    // Preparar dados
    const labels = data.map(item => item.name || item.theme);
    const counts = data.map(item => item.count || 0);
    const colors = data.map(item => themeColors[item.theme] || themeColors.default);

    // Criar gráfico
    themesChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: counts,
                backgroundColor: colors,
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            ...defaultChartOptions,
            cutout: '65%',
            plugins: {
                ...defaultChartOptions.plugins,
                legend: {
                    ...defaultChartOptions.plugins.legend,
                    position: 'right'
                },
                tooltip: {
                    ...defaultChartOptions.plugins.tooltip,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

/**
 * Inicializa gráfico de barras genérico
 * @param {string} canvasId - ID do canvas
 * @param {Array} labels - Labels do eixo X
 * @param {Array} data - Valores do eixo Y
 * @param {string} label - Label do dataset
 * @param {string} color - Cor do gráfico
 */
function initBarChart(canvasId, labels, data, label = 'Valor', color = chartColors.primary) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error(`Canvas #${canvasId} não encontrado`);
        return;
    }

    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                backgroundColor: color + '80',
                borderColor: color,
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            ...defaultChartOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
}

/**
 * Formata número como moeda brasileira
 * @param {number} value - Valor em centavos
 * @returns {string}
 */
function formatCurrency(value) {
    const reais = value / 100;
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(reais);
}

/**
 * Formata data no padrão brasileiro
 * @param {string} dateString - Data em formato ISO
 * @returns {string}
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

/**
 * Formata data/hora no padrão brasileiro
 * @param {string} dateString - Data em formato ISO
 * @returns {string}
 */
function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Exporta dados de um gráfico para CSV
 * @param {Chart} chartInstance - Instância do Chart.js
 * @param {string} filename - Nome do arquivo
 */
function exportChartToCSV(chartInstance, filename = 'chart-data.csv') {
    if (!chartInstance) {
        console.error('Instância do gráfico não encontrada');
        return;
    }

    const labels = chartInstance.data.labels;
    const datasets = chartInstance.data.datasets;

    // Criar header CSV
    let csv = 'Data,' + datasets.map(ds => ds.label).join(',') + '\n';

    // Adicionar linhas de dados
    labels.forEach((label, index) => {
        const row = [label];
        datasets.forEach(ds => {
            row.push(ds.data[index] || 0);
        });
        csv += row.join(',') + '\n';
    });

    // Download
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.click();
}

/**
 * Atualiza dados de um gráfico existente
 * @param {Chart} chartInstance - Instância do Chart.js
 * @param {Array} newLabels - Novos labels
 * @param {Array} newData - Novos dados
 */
function updateChart(chartInstance, newLabels, newData) {
    if (!chartInstance) {
        console.error('Instância do gráfico não encontrada');
        return;
    }

    chartInstance.data.labels = newLabels;
    chartInstance.data.datasets.forEach((dataset, i) => {
        dataset.data = newData[i] || [];
    });
    chartInstance.update();
}

/**
 * Cria um gráfico de progresso (gauge)
 * @param {string} canvasId - ID do canvas
 * @param {number} value - Valor atual (0-100)
 * @param {string} label - Label do gráfico
 */
function initGaugeChart(canvasId, value, label = 'Progresso') {
    const ctx = document.getElementById(canvasId);
    if (!ctx) {
        console.error(`Canvas #${canvasId} não encontrado`);
        return;
    }

    const remaining = 100 - value;

    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [label, 'Restante'],
            datasets: [{
                data: [value, remaining],
                backgroundColor: [chartColors.primary, '#e5e7eb'],
                borderWidth: 0
            }]
        },
        options: {
            ...defaultChartOptions,
            cutout: '75%',
            circumference: 180,
            rotation: -90,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: false
                }
            }
        }
    });
}

// Exportar funções para uso global
window.initOrdersChart = initOrdersChart;
window.initThemesChart = initThemesChart;
window.initBarChart = initBarChart;
window.initGaugeChart = initGaugeChart;
window.exportChartToCSV = exportChartToCSV;
window.updateChart = updateChart;
window.formatCurrency = formatCurrency;
window.formatDate = formatDate;
window.formatDateTime = formatDateTime;

console.log('📊 Admin Charts carregado com sucesso');
