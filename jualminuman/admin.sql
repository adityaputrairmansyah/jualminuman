-- Insert admin dengan password yang sudah di-hash (password: admin321)
INSERT INTO users (username, password, email, role) 
VALUES ('admin', '$2y$10$YourHashedPasswordHere', 'admin@example.com', 'admin');

-- Atau bisa juga menggunakan query ini untuk membuat admin baru
INSERT INTO users (username, password, email, role) 
VALUES ('admin', '$2y$10$3Ur1mGZXECgXxhPGYmVhPuKQebpCr7kS.pKDgUq3hR7kW3kXVEwTi', 'admin@example.com', 'admin'); 