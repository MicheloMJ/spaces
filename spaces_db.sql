CREATE DATABASE IF NOT EXISTS spaces_db;

USE spaces_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    password VARCHAR(100) DEFAULT 'welcome',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE spaces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO spaces (name, description) VALUES 
('Tech Solutions Inc.', 'Leading provider of IT solutions and services.'),
('Global Marketing Group', 'Specializing in digital marketing strategies and campaigns.'),
('Healthcare Innovations Ltd.', 'Developing cutting-edge medical technologies and solutions.'),
('Green Energy Co.', 'Promoting sustainable energy solutions for a greener future.'),
('Creative Design Studios', 'Offering creative design services for branding and advertising.');
