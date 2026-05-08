-- Services table for Forge 720
-- Run this in phpMyAdmin or MySQL command line

USE forge720;

-- Create Services Category table
CREATE TABLE IF NOT EXISTS service_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Services table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_category_id INT NOT NULL,
    service_name VARCHAR(150) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_category_id) REFERENCES service_categories(id) ON DELETE CASCADE
);

-- Insert Service Categories
INSERT INTO service_categories (category_name, description, display_order) VALUES
('Cutting & Laser Services', 'Professional laser cutting, plasma cutting, and engraving services', 1),
('Metal Fabrication & Assembly', 'Expert welding and custom metal fabrication', 2),
('Sheet Metal & Forming', 'Complete sheet metal processing and forming services', 3),
('CNC & Machining', 'Precision CNC operations and manufacturing', 4),
('Metal Joining', 'Various metal joining techniques and fastening services', 5),
('Architectural Fabrication', 'Custom architectural metalwork and decorative elements', 6),
('Outdoor & Structural Works', 'Large-scale structural and outdoor projects', 7),
('Installation Services', 'Professional installation and assembly on-site', 8),
('Repair & Maintenance', 'Welding repairs, restoration, and maintenance', 9),
('Finishing & Surface Treatment', 'Grinding, coating, and surface treatment services', 10),
('Custom Products & Furniture', 'Custom metal furniture and product fabrication', 11),
('Industrial Fabrication', 'Industrial-scale manufacturing and fabrication', 12),
('Security Fabrication', 'Security-focused metal products and installations', 13),
('Green & Smart Solutions', 'Eco-friendly and advanced fabrication solutions', 14);

-- Insert Services under each category
INSERT INTO services (service_category_id, service_name, description, display_order) VALUES
-- Cutting & Laser Services (Category 1)
(1, 'Laser Cutting', 'Precision laser cutting for various materials', 1),
(1, 'Plasma Cutting', 'Fast and efficient plasma cutting services', 2),
(1, 'Waterjet Cutting', 'Cold cutting technology for delicate materials', 3),
(1, 'Laser Engraving', 'Detailed laser engraving on metal and other surfaces', 4),
(1, 'Laser Marking', 'Permanent marking using laser technology', 5),
(1, 'Custom Laser Design', 'Custom design and laser cutting services', 6),
(1, 'Sheet Laser Profiling', 'Sheet metal profiling using laser technology', 7),

-- Metal Fabrication & Assembly (Category 2)
(2, 'Welding (MIG, TIG, Arc)', 'Professional welding using multiple techniques', 1),
(2, 'Structural Steel Fabrication', 'Heavy-duty structural steel fabrication', 2),
(2, 'Custom Metal Product Fabrication', 'Bespoke metal product fabrication', 3),
(2, 'Metal Assembly', 'Professional assembly of metal components', 4),

-- Sheet Metal & Forming (Category 3)
(3, 'Metal Bending (Press Brake)', 'Precision metal bending using press brake equipment', 1),
(3, 'Rolling (Curving Metal)', 'Metal rolling and curving services', 2),
(3, 'Folding and Shaping', 'Complex metal folding and shaping', 3),
(3, 'Metal Casing and Enclosures', 'Custom metal casings and protective enclosures', 4),

-- CNC & Machining (Category 4)
(4, 'CNC Milling', 'Precision CNC milling operations', 1),
(4, 'CNC Turning', 'CNC turning and cylindrical machining', 2),
(4, 'CNC Routing', 'CNC routing for detailed cuts and patterns', 3),
(4, 'Precision Parts Manufacturing', 'High-precision part manufacturing', 4),
(4, 'Drilling and Tapping', 'Precision drilling and thread tapping', 5),

-- Metal Joining (Category 5)
(5, 'Riveting', 'Professional riveting services', 1),
(5, 'Bolting and Fastening', 'Bolting and mechanical fastening', 2),
(5, 'Spot Welding', 'Spot welding for rapid joining', 3),
(5, 'Mechanical Joining Systems', 'Advanced mechanical joining techniques', 4),

-- Architectural Fabrication (Category 6)
(6, 'Gates', 'Custom decorative and functional gates', 1),
(6, 'Doors', 'Custom metal doors and entryways', 2),
(6, 'Windows', 'Metal window frames and grilles', 3),
(6, 'Staircases', 'Custom metal staircases', 4),
(6, 'Railings', 'Decorative and safety railings', 5),
(6, 'Balconies', 'Custom balcony designs and fabrication', 6),
(6, 'Canopies', 'Modern canopy structures', 7),
(6, 'Decorative Metal Works', 'Artistic and decorative metalwork', 8),

-- Outdoor & Structural Works (Category 7)
(7, 'Car Shades / Parking Sheds', 'Metal car shade and parking shed structures', 1),
(7, 'Water Towers and Tanks', 'Industrial water storage structures', 2),
(7, 'Pergolas and Shelters', 'Outdoor pergolas and shelter structures', 3),
(7, 'Storage Sheds', 'Metal storage shed fabrication', 4),
(7, 'Steel Beams and Columns', 'Structural steel beams and columns', 5),
(7, 'Roof Trusses', 'Engineered roof truss systems', 6),
(7, 'Mezzanine Structures', 'Multi-level mezzanine platforms', 7),

-- Installation Services (Category 8)
(8, 'Gate and Door Installation', 'Professional gate and door installation', 1),
(8, 'Staircase and Railing Installation', 'Expert staircase and railing installation', 2),
(8, 'On-site Fabrication and Assembly', 'On-location fabrication and assembly', 3),
(8, 'Structural Installation', 'Large structural element installation', 4),

-- Repair & Maintenance (Category 9)
(9, 'Welding Repairs', 'Professional welding repair services', 1),
(9, 'Metal Restoration', 'Complete metal restoration and rehabilitation', 2),
(9, 'Rust Removal', 'Rust removal and rust-proofing services', 3),
(9, 'Equipment Maintenance', 'Regular maintenance for industrial equipment', 4),
(9, 'Structural Reinforcement', 'Reinforcement and strengthening services', 5),

-- Finishing & Surface Treatment (Category 10)
(10, 'Grinding and Polishing', 'Surface grinding and polishing', 1),
(10, 'Powder Coating', 'Professional powder coating services', 2),
(10, 'Galvanizing', 'Hot-dip galvanizing for corrosion protection', 3),
(10, 'Sandblasting', 'Surface preparation by sandblasting', 4),
(10, 'Spray Painting', 'Professional spray painting and finishing', 5),

-- Custom Products & Furniture (Category 11)
(11, 'Metal Tables', 'Custom metal dining and work tables', 1),
(11, 'Chairs', 'Custom metal chairs and seating', 2),
(11, 'Office Furniture', 'Industrial metal office furniture', 3),
(11, 'Industrial-Style Furniture', 'Contemporary industrial furniture', 4),
(11, 'Home Fittings', 'Metal home fixtures and fittings', 5),

-- Industrial Fabrication (Category 12)
(12, 'Machine Frames', 'Custom fabricated machine frames', 1),
(12, 'Conveyor Systems', 'Metal conveyor systems and components', 2),
(12, 'Industrial Platforms', 'Work platforms and industrial structures', 3),
(12, 'Factory Structures', 'Complete factory structural fabrication', 4),

-- Security Fabrication (Category 13)
(13, 'Security Doors', 'Heavy-duty security door fabrication', 1),
(13, 'Window Grills', 'Security window grilles and bars', 2),
(13, 'Perimeter Fencing', 'Security fencing systems', 3),
(13, 'Reinforced Gates', 'High-security reinforced gates', 4),

-- Green & Smart Solutions (Category 14)
(14, 'Solar Panel Mounting Structures', 'Mounting systems for solar panels', 1),
(14, 'Eco-Friendly Fabrication', 'Environmentally conscious fabrication methods', 2),
(14, 'Recycled Metal Products', 'Products made from recycled metals', 3),
(14, 'Smart/Automated Structures', 'Advanced structures with automation', 4);
