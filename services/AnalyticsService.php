<?php
/**
 * AnalyticsService - Análises e Métricas
 *
 * Fornece dados estatísticos e analíticos para dashboards
 */

class AnalyticsService {
    /**
     * Conexão com banco de dados
     * @var PDO
     */
    private static $db;

    /**
     * Inicializa conexão com banco
     */
    private static function init() {
        if (self::$db === null) {
            if (!function_exists('getDB')) {
                require_once __DIR__ . '/../config/db.php';
            }
            self::$db = getDB();
        }
    }

    /**
     * Obtém KPIs principais do dashboard
     *
     * @param string $period Período (today, week, month, year, all)
     * @return array KPIs
     */
    public static function getMainKPIs($period = 'month') {
        self::init();

        $dateFilter = self::getDateFilter($period);

        $sql = "SELECT
                COUNT(*) as total_orders,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_orders,
                COUNT(CASE WHEN status IN ('generating', 'processing') THEN 1 END) as in_progress,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed_orders,
                SUM(CASE WHEN payment_status = 'paid' THEN price ELSE 0 END) as total_revenue,
                AVG(CASE WHEN status = 'completed' THEN price END) as avg_order_value,
                COUNT(DISTINCT user_id) as unique_customers
                FROM orders
                {$dateFilter}";

        $stmt = self::$db->query($sql);
        $kpis = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calcula taxa de conversão
        $kpis['conversion_rate'] = $kpis['total_orders'] > 0
            ? ($kpis['completed_orders'] / $kpis['total_orders']) * 100
            : 0;

        // Calcula taxa de falha
        $kpis['failure_rate'] = $kpis['total_orders'] > 0
            ? ($kpis['failed_orders'] / $kpis['total_orders']) * 100
            : 0;

        return $kpis;
    }

    /**
     * Obtém dados para gráfico de pedidos por dia
     *
     * @param int $days Número de dias
     * @return array Dados do gráfico
     */
    public static function getOrdersChart($days = 30) {
        self::init();

        $sql = "SELECT
                DATE(created_at) as date,
                COUNT(*) as orders,
                SUM(CASE WHEN payment_status = 'paid' THEN price ELSE 0 END) / 100 as revenue
                FROM orders
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Formata para gráfico
        $labels = [];
        $orders = [];
        $revenue = [];

        foreach ($data as $row) {
            $labels[] = date('d/m', strtotime($row['date']));
            $orders[] = (int)$row['orders'];
            $revenue[] = (float)$row['revenue'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pedidos',
                    'data' => $orders
                ],
                [
                    'label' => 'Receita (R$)',
                    'data' => $revenue
                ]
            ]
        ];
    }

    /**
     * Obtém distribuição de pedidos por status
     *
     * @return array Dados para gráfico de pizza
     */
    public static function getStatusDistribution() {
        self::init();

        $sql = "SELECT
                status,
                COUNT(*) as count
                FROM orders
                GROUP BY status
                ORDER BY count DESC";

        $stmt = self::$db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $values = [];

        foreach ($data as $row) {
            $labels[] = ucfirst($row['status']);
            $values[] = (int)$row['count'];
        }

        return [
            'labels' => $labels,
            'data' => $values
        ];
    }

    /**
     * Obtém temas mais populares
     *
     * @param int $limit Limite de resultados
     * @return array Temas e contagens
     */
    public static function getPopularThemes($limit = 10) {
        self::init();

        $sql = "SELECT
                theme,
                COUNT(*) as orders,
                (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM orders)) as percentage
                FROM orders
                GROUP BY theme
                ORDER BY orders DESC
                LIMIT :limit";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtém estatísticas de tempo de processamento
     *
     * @return array Estatísticas de tempo
     */
    public static function getProcessingTimeStats() {
        self::init();

        $sql = "SELECT
                AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_minutes,
                MIN(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as min_minutes,
                MAX(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as max_minutes
                FROM orders
                WHERE status = 'completed'
                AND updated_at IS NOT NULL";

        $stmt = self::$db->query($sql);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'average' => round((float)$stats['avg_minutes'], 2),
            'minimum' => (int)$stats['min_minutes'],
            'maximum' => (int)$stats['max_minutes']
        ];
    }

    /**
     * Obtém dados de receita por período
     *
     * @param string $groupBy Agrupamento (day, week, month, year)
     * @param int $limit Número de períodos
     * @return array Dados de receita
     */
    public static function getRevenueByPeriod($groupBy = 'day', $limit = 30) {
        self::init();

        $dateFormat = match($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%U',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m-%d'
        };

        $sql = "SELECT
                DATE_FORMAT(created_at, '{$dateFormat}') as period,
                SUM(CASE WHEN payment_status = 'paid' THEN price ELSE 0 END) / 100 as revenue,
                COUNT(*) as orders
                FROM orders
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL :limit {$groupBy})
                GROUP BY period
                ORDER BY period ASC";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtém top clientes por valor gasto
     *
     * @param int $limit Limite de resultados
     * @return array Top clientes
     */
    public static function getTopCustomers($limit = 10) {
        self::init();

        $sql = "SELECT
                user_id,
                customer_name,
                customer_email,
                COUNT(*) as total_orders,
                SUM(CASE WHEN payment_status = 'paid' THEN price ELSE 0 END) / 100 as total_spent,
                MAX(created_at) as last_order_date
                FROM orders
                WHERE user_id IS NOT NULL
                GROUP BY user_id, customer_name, customer_email
                ORDER BY total_spent DESC
                LIMIT :limit";

        $stmt = self::$db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtém taxa de crescimento
     *
     * @param string $metric Métrica (orders, revenue, customers)
     * @param string $period Período de comparação (day, week, month)
     * @return array Crescimento
     */
    public static function getGrowthRate($metric = 'orders', $period = 'month') {
        self::init();

        $metricField = match($metric) {
            'revenue' => 'SUM(CASE WHEN payment_status = "paid" THEN price ELSE 0 END) / 100',
            'customers' => 'COUNT(DISTINCT user_id)',
            default => 'COUNT(*)'
        };

        // Período atual
        $sql1 = "SELECT {$metricField} as value
                 FROM orders
                 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 {$period})";

        // Período anterior
        $sql2 = "SELECT {$metricField} as value
                 FROM orders
                 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 2 {$period})
                 AND created_at < DATE_SUB(NOW(), INTERVAL 1 {$period})";

        $current = self::$db->query($sql1)->fetch(PDO::FETCH_ASSOC)['value'] ?? 0;
        $previous = self::$db->query($sql2)->fetch(PDO::FETCH_ASSOC)['value'] ?? 0;

        $growthRate = $previous > 0
            ? (($current - $previous) / $previous) * 100
            : 0;

        return [
            'current' => (float)$current,
            'previous' => (float)$previous,
            'growth_rate' => round($growthRate, 2),
            'trend' => $growthRate > 0 ? 'up' : ($growthRate < 0 ? 'down' : 'neutral')
        ];
    }

    /**
     * Obtém estatísticas de leads (amostras grátis)
     *
     * @return array Estatísticas de leads
     */
    public static function getLeadsStats() {
        self::init();

        $sql = "SELECT
                COUNT(*) as total_leads,
                COUNT(CASE WHEN converted = 1 THEN 1 END) as converted_leads,
                (COUNT(CASE WHEN converted = 1 THEN 1 END) * 100.0 / COUNT(*)) as conversion_rate
                FROM leads
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";

        $stmt = self::$db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtém horários de pico de pedidos
     *
     * @return array Distribuição por hora
     */
    public static function getPeakHours() {
        self::init();

        $sql = "SELECT
                HOUR(created_at) as hour,
                COUNT(*) as orders
                FROM orders
                GROUP BY HOUR(created_at)
                ORDER BY hour ASC";

        $stmt = self::$db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Preenche horas faltantes com 0
        $hours = array_fill(0, 24, 0);
        foreach ($data as $row) {
            $hours[(int)$row['hour']] = (int)$row['orders'];
        }

        return [
            'labels' => range(0, 23),
            'data' => array_values($hours)
        ];
    }

    /**
     * Gera filtro SQL para período
     *
     * @param string $period Período (today, week, month, year, all)
     * @return string Cláusula WHERE
     */
    private static function getDateFilter($period) {
        return match($period) {
            'today' => 'WHERE DATE(created_at) = CURDATE()',
            'week' => 'WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)',
            'month' => 'WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)',
            'year' => 'WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)',
            default => ''
        };
    }

    /**
     * Exporta dados para CSV
     *
     * @param string $type Tipo de export (orders, revenue, customers)
     * @param array $filters Filtros opcionais
     * @return string Conteúdo CSV
     */
    public static function exportToCSV($type, $filters = []) {
        self::init();

        $data = match($type) {
            'revenue' => self::getRevenueByPeriod('day', 365),
            'customers' => self::getTopCustomers(1000),
            default => OrderService::searchOrders('', $filters)
        };

        if (empty($data)) {
            return '';
        }

        // Gera CSV
        $csv = [];

        // Cabeçalho
        $csv[] = implode(',', array_keys($data[0]));

        // Linhas
        foreach ($data as $row) {
            $csv[] = implode(',', array_map(function($value) {
                return '"' . str_replace('"', '""', $value ?? '') . '"';
            }, $row));
        }

        return implode("\n", $csv);
    }
}
