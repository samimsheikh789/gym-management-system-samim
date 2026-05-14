-- Create Database
CREATE DATABASE IF NOT EXISTS gym_management_system;
USE gym_management_system;

-- --------------------------------------------------------
-- Users Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- Membership Plans Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS membership_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    duration VARCHAR(50) NOT NULL,
    features TEXT,
    benefits TEXT
);

-- --------------------------------------------------------
-- User Memberships Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS user_memberships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    start_date DATE DEFAULT (CURRENT_DATE),
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES membership_plans(id) ON DELETE CASCADE
);

-- --------------------------------------------------------
-- Classes Table
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(100) NOT NULL,
    schedule VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------
-- Trainers Table (Updated Schema)
-- --------------------------------------------------------
DROP TABLE IF EXISTS trainers;
CREATE TABLE trainers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trainer_name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    working_hours VARCHAR(100) NOT NULL,
    experience VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ========================================================
-- Insert Sample Data
-- ========================================================

-- Demo Admin Account (password is 'admin123')
INSERT IGNORE INTO users (username, password, role) VALUES 
('admin', '$2y$10$io5PBLKC1e5rZSgDrqA67ed8kXthFh094jgji7JLdDhysGmgDEe/u', 'admin'); 

-- Demo Gym Membership Plans
DELETE FROM membership_plans;
INSERT INTO membership_plans (plan_name, price, duration, features, benefits) VALUES
('Basic Plan', 20.00, '1 Month', 'Gym access', 'Access to all standard gym equipment, locker rooms, and showers.'),
('Student Plan', 25.00, '1 Month', 'Gym access, Student discount', 'Standard gym access at a reduced rate for students.'),
('Standard Plan', 35.00, '1 Month', 'Gym access, 2 Classes/Week', 'Includes all Basic benefits plus 2 instructor-led group classes per week.'),
('Premium Plan', 50.00, '1 Month', 'Gym access, Unlimited Classes', 'Full access to all gym facilities plus unlimited group classes and steam room access.'),
('Gold Plan', 70.00, '1 Month', 'Gym access, Unlimited Classes, Pool', 'Comprehensive gym, classes, and pool access.'),
('Elite Plan', 90.00, '1 Month', 'Gym access, Unlimited Classes, Pool, Trainer', 'Premium access plus 4 one-on-one sessions per month with our certified personal trainers.');

-- Demo Gym Classes
DELETE FROM classes;
INSERT INTO classes (class_name, schedule) VALUES
('Yoga', 'Mon, Wed, Fri 07:00 AM - 08:00 AM'),
('HIIT', 'Tue, Thu 06:00 PM - 07:00 PM'),
('Zumba', 'Saturday 10:00 AM - 11:30 AM'),
('Boxing', 'Mon, Thu 05:00 PM - 06:30 PM'),
('Power Lifting', 'Wednesday 05:00 PM - 06:00 PM'),
('Cardio Training', 'Friday 08:00 AM - 09:00 AM');

-- Demo Trainers (with extended fields)
INSERT INTO trainers (trainer_name, specialization, contact_number, email, working_hours, experience) VALUES
('Alex Rivers', 'Weightlifting', '555-0101', 'alex.rivers@gym.com', '06:00 AM - 02:00 PM', '5 Years'),
('Sarah Smith', 'Yoga & Pilates', '555-0102', 'sarah.smith@gym.com', '02:00 PM - 10:00 PM', '8 Years'),
('Mike Johnson', 'HIIT & Cardio', '555-0103', 'mike.j@gym.com', '06:00 AM - 02:00 PM', '3 Years'),
('Emma White', 'Zumba & Dance', '555-0104', 'emma.w@gym.com', '02:00 PM - 10:00 PM', '4 Years'),
('David Beck', 'Powerlifting', '555-0105', 'david.b@gym.com', '08:00 AM - 04:00 PM', '10 Years');
