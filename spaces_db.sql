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

INSERT INTO spaces (name, description) VALUES
('Chalimbana University', 'A renowned educational institution specializing in various fields of study.'),
('Innovative Dynamics', 'A cutting-edge technology company focused on innovation and creativity.'),
('Probase Inc', 'A leading provider of software solutions and IT services for businesses.'),
('BongoHive', 'An innovation and technology hub supporting entrepreneurs and startups.'),
('ICTAZ', 'ICT Association of Zambia, promoting ICT development and innovation in Zambia.');


CREATE TABLE user_spaces (
    user_id INT NOT NULL,
    space_id INT NOT NULL,
    PRIMARY KEY (user_id, space_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (space_id) REFERENCES spaces(id)
);

ALTER TABLE user_spaces ADD COLUMN request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP;


CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    space_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (space_id) REFERENCES spaces(id)
);


ALTER TABLE admins ADD COLUMN email VARCHAR(255) NOT NULL UNIQUE;

INSERT INTO admins (username, password, space_id, created_at, email) VALUES
('Mwazipeza Sakala', '123', 10, '2024-06-22 14:27:44', 'admin@ictaz.org.zm'),
('J Michelo', '123', 6, '2024-06-22 14:27:44', 'admin@chau.ac.zm');