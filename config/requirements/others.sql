CREATE TABLE company_assets (
    asset_id INT AUTO_INCREMENT PRIMARY KEY,
    asset_name VARCHAR(255) NOT NULL,
    asset_code VARCHAR(50) NOT NULL UNIQUE,
    category VARCHAR(100),  -- Category of the asset (e.g., Equipment, Vehicle, etc.)
    purchase_date DATE,  -- Date the asset was purchased
    purchase_price DECIMAL(15,2),  -- Purchase price of the asset
    current_value DECIMAL(15,2),  -- Current value (depreciated value if applicable)
    status ENUM('Active', 'Inactive', 'Disposed') DEFAULT 'Active',
    location VARCHAR(100),  -- Location of the asset (e.g., a specific branch or department)
    employee_id INT,  -- Reference to the employee to whom the asset is assigned (if applicable)
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
);


CREATE TABLE organization_projects (
    project_id INT AUTO_INCREMENT PRIMARY KEY,
    project_name VARCHAR(255) NOT NULL,
    project_code VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    start_date DATE,
    end_date DATE,
    status ENUM('Planned', 'In Progress', 'Completed', 'On Hold') DEFAULT 'Planned',
    assigned_to INT,  -- Manager or employee responsible for the project
    budget DECIMAL(15,2),  -- Project's allocated budget
    actual_cost DECIMAL(15,2),  -- Actual cost incurred
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES employees(employee_id)
);


CREATE TABLE employee_performance_reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    review_date DATE NOT NULL,
    reviewer_id INT,  -- Manager or supervisor who gave the review
    rating DECIMAL(5,2),  -- Rating of the employee's performance (e.g., 1-5 scale)
    feedback TEXT,  -- Feedback or comments for the employee
    strengths TEXT,  -- Areas of strength
    areas_for_improvement TEXT,  -- Areas for improvement
    next_steps TEXT,  -- Developmental goals for the employee
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id),
    FOREIGN KEY (reviewer_id) REFERENCES employees(employee_id)
);


CREATE TABLE employee_promotions (
    promotion_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    old_designation_id INT NOT NULL,  -- Old designation before the promotion
    new_designation_id INT NOT NULL,  -- New designation after the promotion
    promotion_date DATE NOT NULL,
    reason TEXT,  -- Reason for the promotion (e.g., performance, job expansion, etc.)
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id),
    FOREIGN KEY (old_designation_id) REFERENCES designations(designation_id),
    FOREIGN KEY (new_designation_id) REFERENCES designations(designation_id)
);

CREATE TABLE employee_compensation (
    compensation_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    basic_salary DECIMAL(15,2),
    bonuses DECIMAL(15,2),  -- Any bonuses or incentives
    deductions DECIMAL(15,2),  -- Deductions (tax, insurance, etc.)
    total_salary DECIMAL(15,2),  -- Total salary after bonuses and deductions
    compensation_date DATE NOT NULL,  -- Date for the particular salary
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
);


CREATE TABLE employee_trainings (
    training_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    training_name VARCHAR(255) NOT NULL,
    training_provider VARCHAR(255),  -- Name of the training provider or organization
    training_date DATE,  -- Date of the training
    certificate_number VARCHAR(100),  -- Certificate number (if applicable)
    training_duration INT,  -- Duration of the training in hours or days
    status ENUM('Completed', 'In Progress', 'Pending') DEFAULT 'Pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
);


-- Table for storing employee finance details
CREATE TABLE employee_finances (
    finance_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,  -- Reference to the employee
    base_salary DECIMAL(15, 2) NOT NULL,  -- Employee's base salary
    bonuses DECIMAL(15, 2) DEFAULT 0,  -- Bonuses or incentive payments
    deductions DECIMAL(15, 2) DEFAULT 0,  -- Any deductions (e.g., tax, insurance)
    net_salary DECIMAL(15, 2) GENERATED ALWAYS AS (base_salary + bonuses - deductions) STORED,  -- Net salary (auto-calculated)
    payment_date DATE NOT NULL,  -- Date of the payment
    payment_mode ENUM('Bank Transfer', 'Cheque', 'Cash', 'Other') NOT NULL,  -- Mode of payment
    bonus_type ENUM('Annual', 'Quarterly', 'Performance-Based', 'Other') DEFAULT 'Performance-Based',  -- Type of bonus
    is_taxable BOOLEAN DEFAULT TRUE,  -- Whether the salary is taxable
    tax_amount DECIMAL(15, 2) DEFAULT 0,  -- Tax deduction amount
    provident_fund DECIMAL(15, 2) DEFAULT 0,  -- Provident fund contribution
    other_deductions TEXT,  -- Any other deductions
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,  -- Record creation timestamp
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  -- Last updated timestamp
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
);
