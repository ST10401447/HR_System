<?php
// report_generator.php

class ReportGenerator {
    private $db;
    
    public function __construct($dbConnection) {
        if (!($dbConnection instanceof mysqli)) {
            throw new InvalidArgumentException("Database connection must be a valid MySQLi instance");
        }
        $this->db = $dbConnection;
    }
    
    /**
     * Generate monthly comprehensive report with error handling
     */
    public function generateMonthlyReport($year, $month) {
        // Validate input parameters
        if (!is_numeric($year) || $year < 2000 || $year > 2100) {
            throw new InvalidArgumentException("Invalid year parameter");
        }
        
        if (!is_numeric($month) || $month < 1 || $month > 12) {
            throw new InvalidArgumentException("Invalid month parameter");
        }
        
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);
        $startDate = "$year-$month-01";
        $endDate = date("Y-m-t", strtotime($startDate));
        
        try {
            return [
                'system_activities' => $this->getSystemActivities($startDate, $endDate),
                'leave_requests' => $this->getLeaveSummary($startDate, $endDate),
                'task_performance' => $this->getTaskPerformance($startDate, $endDate),
                'employee_metrics' => $this->getEmployeeMetrics($startDate, $endDate),
                'department_stats' => $this->getDepartmentStatistics($startDate, $endDate),
                'report_meta' => [
                    'period' => "$year-$month",
                    'generated_at' => date('Y-m-d H:i:s'),
                    'type' => 'monthly'
                ]
            ];
        } catch (Exception $e) {
            error_log("Error generating monthly report: " . $e->getMessage());
            throw new RuntimeException("Failed to generate monthly report", 0, $e);
        }
    }
    
    /**
     * Generate yearly comprehensive report with error handling
     */
    public function generateYearlyReport($year) {
        // Validate input parameter
        if (!is_numeric($year) || $year < 2000 || $year > 2100) {
            throw new InvalidArgumentException("Invalid year parameter");
        }
        
        $startDate = "$year-01-01";
        $endDate = "$year-12-31";
        
        try {
            $monthlyData = [];
            for ($month = 1; $month <= 12; $month++) {
                $monthlyData[$month] = $this->generateMonthlyReport($year, str_pad($month, 2, '0', STR_PAD_LEFT));
            }
            
            return [
                'yearly_summary' => $this->getYearlySummary($startDate, $endDate),
                'monthly_breakdown' => $monthlyData,
                'top_performers' => $this->getTopPerformers($startDate, $endDate),
                'system_usage_trends' => $this->getSystemUsageTrends($year),
                'report_meta' => [
                    'period' => $year,
                    'generated_at' => date('Y-m-d H:i:s'),
                    'type' => 'yearly'
                ]
            ];
        } catch (Exception $e) {
            error_log("Error generating yearly report: " . $e->getMessage());
            throw new RuntimeException("Failed to generate yearly report", 0, $e);
        }
    }
    
    /**
     * Retrieve system activities within a date range
     */
    protected function getSystemActivities($startDate, $endDate) {
        if (!$startDate || !$endDate) {
            throw new InvalidArgumentException("Start date and end date are required");
        }

        try {
            $query = "SELECT * FROM system_activities WHERE activity_date BETWEEN ? AND ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ss', $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result();
            $activities = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $activities;
        } catch (Exception $e) {
            error_log("Error retrieving system activities: " . $e->getMessage());
            throw new RuntimeException("Failed to retrieve system activities", 0, $e);
        }
    }

    /**
     * Retrieve yearly summary within a date range
     */
    protected function getYearlySummary($startDate, $endDate) {
        if (!$startDate || !$endDate) {
            throw new InvalidArgumentException("Start date and end date are required");
        }

        try {
            $query = "SELECT * FROM yearly_summary WHERE summary_date BETWEEN ? AND ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ss', $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result();
            $summary = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $summary;
        } catch (Exception $e) {
            error_log("Error retrieving yearly summary: " . $e->getMessage());
            throw new RuntimeException("Failed to retrieve yearly summary", 0, $e);
        }
    }

    /**
     * Retrieve top performers within a date range
     */
    protected function getTopPerformers($startDate, $endDate) {
        if (!$startDate || !$endDate) {
            throw new InvalidArgumentException("Start date and end date are required");
        }

        try {
            $query = "SELECT employee_id, performance_score FROM employee_performance WHERE performance_date BETWEEN ? AND ? ORDER BY performance_score DESC LIMIT 10";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ss', $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result();
            $topPerformers = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $topPerformers;
        } catch (Exception $e) {
            error_log("Error retrieving top performers: " . $e->getMessage());
            throw new RuntimeException("Failed to retrieve top performers", 0, $e);
        }
    }

    /**
     * Retrieve system usage trends for a given year
     */
    protected function getSystemUsageTrends($year) {
        if (!is_numeric($year) || $year < 2000 || $year > 2100) {
            throw new InvalidArgumentException("Invalid year parameter");
        }

        try {
            $query = "SELECT month, usage_count FROM system_usage_trends WHERE year = ? ORDER BY month ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $year);
            $stmt->execute();
            $result = $stmt->get_result();
            $trends = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $trends;
        } catch (Exception $e) {
            error_log("Error retrieving system usage trends: " . $e->getMessage());
            throw new RuntimeException("Failed to retrieve system usage trends", 0, $e);
        }
    }

    /**
     * Retrieve task performance within a date range
     */
    protected function getTaskPerformance($startDate, $endDate) {
        if (!$startDate || !$endDate) {
            throw new InvalidArgumentException("Start date and end date are required");
        }

        try {
            $query = "SELECT task_id, employee_id, completion_rate FROM task_performance WHERE performance_date BETWEEN ? AND ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ss', $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result();
            $taskPerformance = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $taskPerformance;
        } catch (Exception $e) {
            error_log("Error retrieving task performance: " . $e->getMessage());
            throw new RuntimeException("Failed to retrieve task performance", 0, $e);
        }
    }

    /**
     * Retrieve leave summary within a date range
     */
    protected function getLeaveSummary($startDate, $endDate) {
        if (!$startDate || !$endDate) {
            throw new InvalidArgumentException("Start date and end date are required");
        }

        try {
            $query = "SELECT employee_id, leave_type, leave_days FROM leave_requests WHERE leave_date BETWEEN ? AND ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ss', $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result();
            $leaveSummary = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $leaveSummary;
        } catch (Exception $e) {
            error_log("Error retrieving leave summary: " . $e->getMessage());
            throw new RuntimeException("Failed to retrieve leave summary", 0, $e);
        }
    }

    /**
     * Retrieve employee metrics within a date range
     */
    protected function getEmployeeMetrics($startDate, $endDate) {
        if (!$startDate || !$endDate) {
            throw new InvalidArgumentException("Start date and end date are required");
        }

        try {
            $query = "SELECT employee_id, metric_name, metric_value FROM employee_metrics WHERE metric_date BETWEEN ? AND ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ss', $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result();
            $employeeMetrics = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $employeeMetrics;
        } catch (Exception $e) {
            error_log("Error retrieving employee metrics: " . $e->getMessage());
            throw new RuntimeException("Failed to retrieve employee metrics", 0, $e);
        }
    }

    /**
     * Retrieve department statistics within a date range
     */
    protected function getDepartmentStatistics($startDate, $endDate) {
        if (!$startDate || !$endDate) {
            throw new InvalidArgumentException("Start date and end date are required");
        }

        try {
            $query = "SELECT department_id, total_employees, avg_performance_score FROM department_statistics WHERE stat_date BETWEEN ? AND ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ss', $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result();
            $departmentStats = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $departmentStats;
        } catch (Exception $e) {
            error_log("Error retrieving department statistics: " . $e->getMessage());
            throw new RuntimeException("Failed to retrieve department statistics", 0, $e);
        }
    }

    // [Rest of your protected methods with error handling...]
    // Keep all your existing protected methods but add try-catch blocks
    // and input validation as shown in the generateMonthlyReport method
}








