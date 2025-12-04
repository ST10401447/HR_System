<?php
$servername = "localhost";
$username = "root";
$password = "";
$db1 = "users1"; 

try {
    // Connect to MySQL
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if database exists
    $stmt = $conn->query("SHOW DATABASES LIKE '$db1'");
    if ($stmt->rowCount() == 0) {
        $conn->exec("CREATE DATABASE $db1");
    }

    // Helper to check table existence
    function tableExists($conn, $dbName, $tableName) {
        $stmt = $conn->query("
            SELECT COUNT(*) 
            FROM information_schema.tables 
            WHERE table_schema = '$dbName' 
            AND table_name = '$tableName'
        ");
        return $stmt->fetchColumn() > 0;
    }

    // Helper to create table if missing
    function createTable($conn, $dbName, $tableName, $columns) {
        if (!tableExists($conn, $dbName, $tableName)) {
            $conn->exec("USE $dbName");
            $sql = "CREATE TABLE $tableName ($columns)";
            $conn->exec($sql);
        }
    }

    // ---------------------
    // TABLE DEFINITIONS
    // ---------------------
    $tables = [
        $db1 => [

            // USERS (MODERNISED)
            "users" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255),
                email VARCHAR(255) UNIQUE,
                employee_id INT,
                password VARCHAR(255),
                department VARCHAR(255),
                role VARCHAR(255),
                profile_picture VARCHAR(255),
                company_id VARCHAR(255),

                -- Modern profile fields
                title VARCHAR(255),
                dob DATE,
                nationality VARCHAR(255),
                gender VARCHAR(255),
                race VARCHAR(255),
                mobile VARCHAR(20),
                address TEXT,
                city VARCHAR(255),
                country VARCHAR(255),
                bio TEXT,
                theme VARCHAR(20) DEFAULT 'light',
                profile_status VARCHAR(50) DEFAULT 'incomplete',

                -- Employment
                start_date DATE,

                -- Emergency
                emergency_name VARCHAR(255),
                emergency_number VARCHAR(20),

                -- Secure Login Fields
                last_login TIMESTAMP NULL,
                login_attempts INT DEFAULT 0,
                locked_until TIMESTAMP NULL,
                account_status ENUM('active','locked','disabled') DEFAULT 'active',
                password_reset_token VARCHAR(255) NULL,
                password_reset_expires TIMESTAMP NULL,

                INDEX(email),
                INDEX(employee_id)
            ",

            // PENDING REGISTRATION (MODERNISED)
            "pending_registrations" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255),
                email VARCHAR(255),
                phone VARCHAR(20),
                password VARCHAR(255),
                department VARCHAR(255),
                company_id VARCHAR(255),
                request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status ENUM('pending','approved','rejected') DEFAULT 'pending',
                admin_notes TEXT,

                -- Verification
                verification_code VARCHAR(10),
                verified TINYINT(1) DEFAULT 0,
                verification_expires TIMESTAMP NULL
            ",

            // TIME OFF
            "timeoff" => "
                timeoff_id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT,
                leave_type VARCHAR(255),
                start_date DATE,
                end_date DATE,
                status VARCHAR(255),
                reason VARCHAR(255)
            ",

            // LEAVE BALANCE
            "leave_balance" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT,
                study INT,
                sick INT,
                maternity INT,
                annual INT,
                unpaid INT,
                compassionate INT
            ",

            // TASKS
            "tasks" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                task_name VARCHAR(255),
                assigned_to VARCHAR(255),
                employee_id INT,
                task_date DATE,
                manager VARCHAR(255),
                status VARCHAR(255)
            ",

            // ANNOUNCEMENTS
            "announcements" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                date DATE NOT NULL,
                text VARCHAR(255) NOT NULL,
                description VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ",

            // DOCUMENTS
            "documents" => "
                document_id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT,
                document_name VARCHAR(255),
                document_original_name VARCHAR(255),
                document_type VARCHAR(100)
            ",

            // EVENTS / CALENDAR
            "events" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255),
                start DATE,
                end DATE
            ",

            "calendar" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255),
                start DATE,
                end DATE
            ",

            // FEEDBACK
            "feedback" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT,
                target_employee_id INT,
                feedback_text TEXT,
                submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ",

            // LEAVE REQUESTS
            "leave_requests" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT,
                leave_type VARCHAR(255),
                days_requested INT,
                status VARCHAR(255),
                request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ",

            // VOTES
            "votes" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT,
                vote_id INT,
                vote_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            "
        ]
    ];

    // CREATE TABLES
    foreach ($tables as $database => $tableData) {
        $conn->exec("USE $database");
        foreach ($tableData as $table => $columns) {
            createTable($conn, $database, $table, $columns);
        }
    }

    // ----------------------------
    // INSERT DEFAULT ADMIN USERS
    // ----------------------------

    $conn->exec("USE $db1");

    $defaultUsers = [
        [
            'name' => 'Prince',
            'email' => 'prince@thetechgiants.co.za',
            'employee_id' => '1111',
            'password' => password_hash('0000', PASSWORD_BCRYPT),
            'department' => 'IT',
            'role' => 'Admin',
            'profile_picture' => '../../resources/UserIcon.jpg',
            'company_id' => 'TTG-AD-2025'
        ],
        [
            'name' => 'Samson',
            'email' => 'samson@thetechgiants.co.za',
            'employee_id' => '1112',
            'password' => password_hash('1010', PASSWORD_BCRYPT),
            'department' => 'IT',
            'role' => 'Admin',
            'profile_picture' => '../../resources/UserIcon.jpg',
            'company_id' => 'TTG-IT-2025'
        ]
    ];

    foreach ($defaultUsers as $user) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE employee_id = :employee_id");
        $stmt->execute(['employee_id' => $user['employee_id']]);

        if ($stmt->fetchColumn() == 0) {
            $sql = "
                INSERT INTO users 
                (name, email, employee_id, password, department, role, profile_picture, company_id)
                VALUES 
                (:name, :email, :employee_id, :password, :department, :role, :profile_picture, :company_id)
            ";
            $stmtInsert = $conn->prepare($sql);
            $stmtInsert->execute($user);
        }
    }

    echo "✅ Database setup updated successfully!";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
