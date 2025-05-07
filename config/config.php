    -- Table for storing department information
    CREATE TABLE departments (
        department_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        description TEXT
    );

    -- Table for storing designation or job title information
    CREATE TABLE designations (
        designation_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        description TEXT
    );

    -- Table for storing team information
    CREATE TABLE teams (
        team_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        description TEXT
    );

    -- Table for storing employee profiles, login credentials, and reporting structure
    CREATE TABLE employees (
        employee_id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        phone VARCHAR(20),
        gender ENUM('Male', 'Female', 'Other'),
        dob DATE,
        join_date DATE NOT NULL,
        department_name VARCHAR(100),
        designation_name VARCHAR(100),
        team_name VARCHAR(100),
        status ENUM('Active', 'Inactive') DEFAULT 'Active',
        about TEXT,
        address TEXT,
        permanentAddress TEXT,
        photo_url VARCHAR(255),
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('Admin', 'Manager', 'Employee') DEFAULT 'Employee',
        manager_id INT,  -- Reference to employee's manager
        hr_id INT,       -- Reference to HR assigned to the employee
        employee_code VARCHAR(50) NOT NULL UNIQUE,  -- Unique company-assigned ID
        work_location VARCHAR(150),  -- Office branch or location
        employment_type ENUM('Full-Time', 'Part-Time', 'Contract') DEFAULT 'Full-Time',  -- Type of employment
        official_email VARCHAR(150),  -- Official email address
        emergency_contact VARCHAR(20),  -- Emergency contact phone number
        blood_group VARCHAR(5),  -- Blood group (optional)
        marital_status ENUM('Single', 'Married', 'Divorced', 'Widowed'),  -- Marital status
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,  -- Record creation timestamp
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  -- Last updated timestamp
        FOREIGN KEY (manager_id) REFERENCES employees(employee_id),
        FOREIGN KEY (hr_id) REFERENCES employees(employee_id)
    );


    -- Table for storing documents uploaded by employees (e.g., ID proof, resume)
    CREATE TABLE employee_documents (
        document_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        document_type ENUM('Resume', 'ID Proof', 'Certificate', 'Others') NOT NULL,
        file_name VARCHAR(255) NOT NULL,
        file_url VARCHAR(255) NOT NULL,
        uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
    );



    -- Table for storing employee education details
    CREATE TABLE educations (
        education_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT,  -- Reference to the employee
        branch_specialization VARCHAR(255),
        degree_name VARCHAR(100) NOT NULL, 
        university_college VARCHAR(255) NOT NULL,  
        cgpa_percentage DECIMAL(4,2), 
        year_of_joining DATE, 
        year_of_completion DATE, 
        FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
    );


    -- Table for storing employee family relationships (parent-child, siblings, etc.)
    CREATE TABLE employee_family_relations (
        relation_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,  -- Employee for whom the relationship is defined (could be the child or sibling)
        family_member_id INT NOT NULL,  -- The family member (parent, sibling, etc.)
        relation_type ENUM('Parent', 'Child', 'Sibling', 'Other') NOT NULL,  -- Type of relationship
        start_date DATE NOT NULL,  -- When the relationship began
        end_date DATE,  -- When the relationship ended (NULL if still active)
        FOREIGN KEY (employee_id) REFERENCES employees(employee_id),
        FOREIGN KEY (family_member_id) REFERENCES employees(employee_id)
    );


    CREATE TABLE employee_family_members (
        relation_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,  -- Reference to the main employee
        full_name VARCHAR(100) NOT NULL,
        relation_type ENUM('Father', 'Mother', 'Sibling', 'Spouse', 'Other') NOT NULL,
        email VARCHAR(100),
        mobile VARCHAR(15),
        profession VARCHAR(50),
        date_of_birth DATE,
        gender ENUM('Male', 'Female', 'Other'),
        FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
    );

    CREATE TABLE employee_attendance (
        attendance_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        attendance_date DATE NOT NULL,
        punch_in DATETIME,
        punch_out DATETIME,
        effective_hours TIME,
        gross_hours TIME,
        arrival_delay TIME,
        is_late BOOLEAN,
        log_status ENUM('Present', 'Absent', 'Weekly-Off', 'Holiday') DEFAULT 'Present',
        attendance_type ENUM('In Office', 'WFH', 'On Duty', 'Leave') DEFAULT 'In Office',
        reason TEXT,
        FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
    );

    CREATE TABLE employee_punch_log (
        punch_id INT AUTO_INCREMENT PRIMARY KEY,
        attendance_id INT NOT NULL,
        employee_id INT NOT NULL,
        punch_in DATETIME NOT NULL,
        punch_out DATETIME,
        FOREIGN KEY (attendance_id) REFERENCES employee_attendance(attendance_id),
        FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
    );

    CREATE TABLE employee_leave_balances (
        balance_id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        leave_type ENUM('Casual Leave', 'Sick Leave', 'Earned Leave', 'Maternity Leave', 'Paternity Leave', 'Other') NOT NULL,
        total_leaves DECIMAL(5,2) DEFAULT 0,
        leaves_used DECIMAL(5,2) DEFAULT 0,
        year INT NOT NULL,
        FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
    );


    CREATE TABLE company_timings (
        timing_id INT AUTO_INCREMENT PRIMARY KEY,
        shift_name VARCHAR(100) NOT NULL,
        work_start TIME NOT NULL,
        work_end TIME NOT NULL,
        break_start TIME,
        break_end TIME,
        grace_period_minutes INT DEFAULT 0,
        effective_hours_required TIME,
        gross_hours_required TIME,
        is_active BOOLEAN DEFAULT TRUE
    );

    CREATE TABLE company_timings (
        timing_id INT AUTO_INCREMENT PRIMARY KEY,
        shift_name VARCHAR(100) NOT NULL,
        work_start TIME NOT NULL,
        work_end TIME NOT NULL,
        break_start TIME,
        break_end TIME,
        grace_period_minutes INT DEFAULT 0,
        effective_hours_required TIME,
        gross_hours_required TIME,
        is_active BOOLEAN DEFAULT TRUE
    );

    CREATE TABLE department_timings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        department_id INT NOT NULL,
        timing_id INT NOT NULL,
        FOREIGN KEY (department_id) REFERENCES departments(department_id),
        FOREIGN KEY (timing_id) REFERENCES company_timings(timing_id)
    );
    CREATE TABLE employee_timings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT NOT NULL,
        timing_id INT NOT NULL,
        FOREIGN KEY (employee_id) REFERENCES employees(employee_id),
        FOREIGN KEY (timing_id) REFERENCES company_timings(timing_id)
    );


    CREATE TABLE company_holidays (
        holiday_id INT AUTO_INCREMENT PRIMARY KEY,
        holiday_name VARCHAR(255) NOT NULL,
        holiday_date DATE NOT NULL,
        description TEXT,
        holiday_type ENUM('Floater', 'Normal') NOT NULL,
        is_federal BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE company_holidays (
        holiday_id INT AUTO_INCREMENT PRIMARY KEY,
        holiday_name VARCHAR(255) NOT NULL,
        holiday_date DATE NOT NULL,
        description TEXT,
        holiday_type ENUM('Floater', 'Normal') NOT NULL,
        financial_year VARCHAR(9) NOT NULL,  -- Example: '2024-2025'
        is_federal BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );



    CREATE TABLE organization_details (
        organization_id INT AUTO_INCREMENT PRIMARY KEY,
        company_name VARCHAR(255) NOT NULL,
        company_code VARCHAR(50) NOT NULL UNIQUE,  -- Unique company identifier
        industry_type VARCHAR(100),  -- Type of industry (e.g., IT, Manufacturing, etc.)
        contact_number VARCHAR(20),  -- Organization's contact number
        email VARCHAR(150),  -- Organization's general email
        website_url VARCHAR(255),  -- Official website of the company
        founding_year INT,  -- Year the company was founded
        total_employees INT,  -- Total number of employees in the organization
        revenue DECIMAL(15,2),  -- Annual revenue of the organization
        logo_url VARCHAR(255),  -- URL to the company's logo image
        business_registration_number VARCHAR(100),  -- Company registration number
        fiscal_year_start DATE,  -- The start date of the fiscal year
        fiscal_year_end DATE,  -- The end date of the fiscal year
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,  -- Record creation timestamp
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  -- Last updated timestamp
    );

    CREATE TABLE organization_branches (
        branch_id INT AUTO_INCREMENT PRIMARY KEY,
        organization_id INT NOT NULL,  -- Foreign key to the organization
        branch_name VARCHAR(255) NOT NULL,  -- Name of the branch (e.g., "Headquarters", "Branch A")
        address TEXT,  -- Branch's physical address
        city VARCHAR(100),  -- City where the branch is located
        state VARCHAR(100),  -- State
        postal_code VARCHAR(20),  -- Postal code
        country VARCHAR(100),  -- Country
        contact_number VARCHAR(20),  -- Contact number for the branch
        manager_name VARCHAR(100),  -- Name of the branch manager
        manager_contact VARCHAR(20),  -- Manager's contact number
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,  -- Branch record creation timestamp
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  -- Branch record last updated timestamp
        FOREIGN KEY (organization_id) REFERENCES organization_details(organization_id)  -- Foreign key reference to the organization
    );
