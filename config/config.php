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
