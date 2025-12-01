<?php
$servername = "localhost";
$username = "root"; // Default for WampServer/XAMPP
$password = "";
$db1 = "users1";  // Assuming the announcements table should be in this database

try {
    // Connect to MySQL server
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if database 1 exists
    $stmt = $conn->query("SHOW DATABASES LIKE '$db1'");
    $db1Exists = $stmt->rowCount() > 0;    

    // Create databases only if they don't exist
    if (!$db1Exists) {
        $conn->exec("CREATE DATABASE $db1");
    }

    // Function to check if a table exists in a database
    function tableExists($conn, $dbName, $tableName) {
        $stmt = $conn->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '$dbName' AND table_name = '$tableName'");
        return $stmt->fetchColumn() > 0;
    }

    // Function to create a table if it doesn't exist
    function createTable($conn, $dbName, $tableName, $columns) {
        if (!tableExists($conn, $dbName, $tableName)) {
            $conn->exec("USE $dbName");
            $sql = "CREATE TABLE $tableName ($columns)";
            $conn->exec($sql);            
        }
    }

    // Define table structures (Including calendar table)
    $tables = [
        $db1 => [                       
            "timeoff" => "
                timeoff_id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT,
                leave_type VARCHAR(255),
                start_date DATE,
                end_date DATE,
                status VARCHAR(255),
                reason VARCHAR(255)
            ",
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
            "users" => "
                name VARCHAR(255),
                email VARCHAR(255),
                employee_id INT,
                password VARCHAR(255),
                department VARCHAR(255),
                role VARCHAR(255),
                profile_picture VARCHAR(255),
                company_id VARCHAR(255),
                title VARCHAR(255),
                dob DATE,
                nationality VARCHAR(255),
                gender VARCHAR(255),
                race VARCHAR(255),
                start_date DATE,
                mobile VARCHAR(20),
                emergency_name VARCHAR(255),
                emergency_number VARCHAR(20)                
            ",
            "tasks" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                task_name VARCHAR(255),
                assigned_to VARCHAR(255),
                employee_id INT,
                task_date DATE,
                manager VARCHAR(255),
                status VARCHAR(255)
            ",
            "announcements" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                date DATE NOT NULL,
                text VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                description VARCHAR(255) NOT NULL
            ",
            "documents" => "
                document_id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT,
                document_name VARCHAR(255),
                document_original_name VARCHAR(255),
                document_type VARCHAR(100)
            ",
            "events" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255),
                start DATE,
                end DATE
            ",
            "feedback" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT,
                target_employee_id INT,
                feedback_text TEXT,
                submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ",
            "leave_requests" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT,
                leave_type VARCHAR(255),
                days_requested INT,
                status VARCHAR(255),
                request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ",
            "calendar" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255),
                start DATE,
                end DATE
            ",
            "votes" => "
                id INT AUTO_INCREMENT PRIMARY KEY,
                employee_id INT,
                vote_id INT,
                vote_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ",
            "pending_registrations" => "
             id INT AUTO_INCREMENT PRIMARY KEY,
             name VARCHAR(255),
             email VARCHAR(255),
             password VARCHAR(255),
             department VARCHAR(255),
             company_id VARCHAR(255),
             request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
             status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
             admin_notes TEXT
       "

        ]
    ];

    // Create tables if they don't exist
    foreach ($tables as $database => $tableData) {
        $conn->exec("USE $database");
        foreach ($tableData as $table => $columns) {
            createTable($conn, $database, $table, $columns);
        }
    }

    // Switch to the users1 database for data insertion
    $conn->exec("USE $db1");

    // Insert data into announcements table if not exists
    if (!tableExists($conn, $db1, 'announcements')) {
        $sql = "INSERT INTO announcements (date, text, created_at, description) 
                VALUES (:date, :text, :created_at, :description)";
        $stmt = $conn->prepare($sql);
        
        $announcements = [
            [
                'date' => '2025-03-31',
                'text' => 'month end award ceremony',
                'created_at' => '2025-03-22 10:03:42',
                'description' => 'Award ceremony'
            ],
            [
                'date' => '2025-03-31',
                'text' => 'Submission',
                'created_at' => '2025-03-22 10:15:42',
                'description' => 'Monthly Reports'
            ],
            [
                'date' => '2025-03-31',
                'text' => 'month end',
                'created_at' => '2025-03-23 22:22:32',
                'description' => 'Month End Celebration'
            ]
        ];
        
        foreach ($announcements as $announcement) {
            $stmt->execute($announcement);
        }
    }

    // Insert data into documents table if not exists
    if (!tableExists($conn, $db1, 'documents')) {
        $sql = "INSERT INTO documents (employee_id, document_name, document_original_name, document_type) 
                VALUES (:employee_id, :document_name, :document_original_name, :document_type)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'employee_id' => '1112',
            'document_name' => '1112_cv.docx',
            'document_original_name' => 'Email_Signatures[1].docx',
            'document_type' => 'CV'
        ]);
    }

    // Insert default users if they don't exist
    $users = [
        [
            'name' => 'Prince',
            'email' => 'prince@thetechgiants.co.za',
            'employee_id' => '1111',
            'password' => password_hash('0000', PASSWORD_BCRYPT),
            'department' => 'IT',
            'role' => 'Admin',
            'profile_picture' => '../../resources/UserIcon.jpg',
            'company_id' => 'TTG-AD-2025',
            'title' => '',
            'dob' => null,
            'nationality' => '',
            'gender' => '',
            'race' => '',
            'start_date' => null,
            'mobile' => '',
            'emergency_name' => '',
            'emergency_number' => ''   
        ],
        [
            'name' => 'Samson',
            'email' => 'Samson@thetechgiants.co.za',
            'employee_id' => '1112',
            'password' => password_hash('1010', PASSWORD_BCRYPT),
            'department' => 'IT',
            'role' => 'Admin',
            'profile_picture' => '../../resources/UserIcon.jpg',
            'company_id' => 'TTG-IT-2025',
            'title' => '',
            'dob' => null,
            'nationality' => '',
            'gender' => '',
            'race' => '',
            'start_date' => null,
            'mobile' => '',
            'emergency_name' => '',
            'emergency_number' => ''   
        ]
    ];

    foreach ($users as $user) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE employee_id = :employee_id");
        $stmt->execute(['employee_id' => $user['employee_id']]);
        $exists = $stmt->fetchColumn();

        if (!$exists) {
            $sql = "INSERT INTO users (name, email, employee_id, password, department, role, profile_picture, company_id, title, dob, nationality, gender, race, start_date, mobile, emergency_name, emergency_number) 
                    VALUES (:name, :email, :employee_id, :password, :department, :role, :profile_picture, :company_id, :title, :dob, :nationality, :gender, :race, :start_date, :mobile, :emergency_name, :emergency_number)";
            $stmt = $conn->prepare($sql);
            $stmt->execute($user);
        }
    }

    echo "Database setup completed successfully!";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
$conn = null;
?>