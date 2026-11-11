-- Create both databases and access-scoped users.
-- The target user can touch ONLY the kestrel db, so SQLi/RCE in the target
-- can never read the console's player keys or scores.
CREATE DATABASE IF NOT EXISTS kestrel;
CREATE DATABASE IF NOT EXISTS console;

CREATE USER IF NOT EXISTS 'kestrel_app'@'%' IDENTIFIED BY 'Summer2023!';
GRANT SELECT, INSERT, UPDATE, DELETE ON kestrel.* TO 'kestrel_app'@'%';

CREATE USER IF NOT EXISTS 'console_app'@'%' IDENTIFIED BY 'console-strong-pw';
GRANT SELECT, INSERT, UPDATE, DELETE ON console.* TO 'console_app'@'%';
FLUSH PRIVILEGES;

USE kestrel;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64) UNIQUE,
  password VARCHAR(64),            -- unsalted MD5 on purpose
  full_name VARCHAR(128),
  email VARCHAR(128),
  role VARCHAR(16) DEFAULT 'customer',
  api_token VARCHAR(64),
  phone VARCHAR(32),
  ssn VARCHAR(32),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shipments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tracking VARCHAR(32) UNIQUE,
  owner_id INT,
  origin VARCHAR(128),
  destination VARCHAR(128),
  status VARCHAR(32),
  value_zar DECIMAL(12,2),
  notes TEXT
);

CREATE TABLE support_tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  owner_id INT,
  subject VARCHAR(255),
  body TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username,password,full_name,email,role,api_token,phone,ssn) VALUES
('tshepo', '25f9e794323b453885f5181f1b624d0b', 'Tshepo Mahlangu', 'tshepo@gmail.com', 'customer', 'tok_cust_8f21a', '0821234567', '8801015800081'),
('lerato', '0d107d09f5bbe40cade3de5c71e9e9b7', 'Lerato Dube', 'lerato@kestrelfreight.co.za', 'customer', 'tok_cust_4b7c2', '0739876543', '9002024800085'),
('admin',  '21232f297a57a5a743894a0e4a801fc3', 'Site Administrator', 'admin@kestrelfreight.co.za', 'admin', 'tok_adm_00ff9', '0115550100', '0000000000000'),
('driver', '5f4dcc3b5aa765d61d8327deb882cf99', 'Sipho Ndlovu', 'driver1@kestrelfreight.co.za', 'customer', 'tok_cust_d9a1', '0825551212', '8502025800088');

-- Note: flags are no longer stored. The target renders a per-player flag at
-- runtime for shipment id 42 (see shipment.php + flags.php).
INSERT INTO shipments (tracking,owner_id,origin,destination,status,value_zar,notes) VALUES
('KF-100001',1,'Johannesburg','Durban','In Transit',12500.00,'Fragile - electronics'),
('KF-100002',1,'Cape Town','Pretoria','Delivered',4300.00,'Signed by reception'),
('KF-100003',2,'Gqeberha','Bloemfontein','In Transit',98000.00,'High value - insured'),
('KF-100042',3,'Head Office','Embargo Hold','Held',0.00,'__RESTRICTED__');

INSERT INTO support_tickets (owner_id,subject,body) VALUES
(1,'Where is my parcel?','Hi, tracking KF-100001 has not moved in 3 days.'),
(2,'Invoice query','Please resend the invoice for KF-100003.');
