CREATE DATABASE IF NOT EXISTS medixon_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE medixon_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL DEFAULT '',
    password VARCHAR(255) NOT NULL,
    location VARCHAR(200) DEFAULT NULL,
    profile_pic VARCHAR(300) DEFAULT NULL,
    role ENUM('user','admin') NOT NULL DEFAULT 'user',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    category VARCHAR(100) NOT NULL,
    company VARCHAR(150) DEFAULT NULL,
    quantity INT NOT NULL DEFAULT 1,
    expiry DATE DEFAULT NULL,
    condition_type ENUM('New','Used') NOT NULL DEFAULT 'New',
    mode ENUM('Donate','Swap','Rent','Low Price Sale') NOT NULL DEFAULT 'Donate',
    price DECIMAL(10,2) DEFAULT NULL,
    rent_per_day DECIMAL(10,2) DEFAULT NULL,
    location VARCHAR(200) DEFAULT NULL,
    photo VARCHAR(300) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id INT NOT NULL,
    requester_id INT NOT NULL,
    owner_id INT NOT NULL,
    message TEXT DEFAULT NULL,
    status ENUM('Pending','Accepted','Rejected') NOT NULL DEFAULT 'Pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE,
    FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    generic_name VARCHAR(200) DEFAULT NULL,
    category VARCHAR(100) NOT NULL,
    dosage_form ENUM('Tablet','Capsule','Syrup','Injection','Cream','Drops','Inhaler','Powder','Other') NOT NULL DEFAULT 'Tablet',
    strength VARCHAR(100) DEFAULT NULL,
    manufacturer VARCHAR(150) DEFAULT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit VARCHAR(50) DEFAULT 'pcs',
    expiry_date DATE DEFAULT NULL,
    condition_type ENUM('Sealed','Opened','Partial') NOT NULL DEFAULT 'Sealed',
    mode ENUM('Donate','Swap','Low Price Sale') NOT NULL DEFAULT 'Donate',
    price DECIMAL(10,2) DEFAULT NULL,
    location VARCHAR(200) DEFAULT NULL,
    photo VARCHAR(300) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS medicine_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_id INT NOT NULL,
    requester_id INT NOT NULL,
    owner_id INT NOT NULL,
    message TEXT DEFAULT NULL,
    status ENUM('Pending','Accepted','Rejected') NOT NULL DEFAULT 'Pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE,
    FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT DEFAULT NULL,
    attachment VARCHAR(300) DEFAULT NULL,
    attach_type VARCHAR(100) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    name VARCHAR(120) NOT NULL DEFAULT 'Anonymous',
    email VARCHAR(160) DEFAULT NULL,
    rating TINYINT NOT NULL DEFAULT 5,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_eq_user ON equipment(user_id);
CREATE INDEX idx_eq_mode ON equipment(mode);
CREATE INDEX idx_req_owner ON requests(owner_id);
CREATE INDEX idx_req_reqr ON requests(requester_id);
CREATE INDEX idx_med_user ON medicines(user_id);
CREATE INDEX idx_med_mode ON medicines(mode);
CREATE INDEX idx_mreq_owner ON medicine_requests(owner_id);
CREATE INDEX idx_mreq_reqr ON medicine_requests(requester_id);
CREATE INDEX idx_msg_conv ON messages(sender_id,receiver_id);
