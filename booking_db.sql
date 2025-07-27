-- Create Database
CREATE DATABASE IF NOT EXISTS ebookingsystem;
USE ebookingsystem;

-- Facilities Table
CREATE TABLE facilities (
    facility_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    location VARCHAR(100) NOT NULL
);

-- Sample Facilities
INSERT INTO facilities (name, location) VALUES
('Multi Media Room', ''),
('Pavilion', ''),
('Mansaka Lobby', ''),
('Mandaya Lobby', ''),
('TED Library', 'Conference Room'),
('TED Library', 'Fourth Floor'),
('MAC Lab/MT201', 'T Boli Second Floor'),
('COM Lab/TL204', 'T Boli Second Floor'),
('RSM Events Center', 'Gate 1');

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending'
);

-- Sample Admin Account
INSERT INTO users (username, password, role, status) 
VALUES ('admin', SHA1('admin123'), 'admin', 'approved');

-- Bookings Table
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    facility_id INT,
    date DATE,
    time_in TIME,
    time_out TIME,
    booked_by VARCHAR(100),
    description TEXT,
    status VARCHAR(20) DEFAULT 'Booked',
    FOREIGN KEY (facility_id) REFERENCES facilities(facility_id)
);

