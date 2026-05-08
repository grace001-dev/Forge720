# Services Management System - Setup Guide

## Overview
A comprehensive services menu system for Forge 720 with 14 service categories and 60+ individual services. Services are organized in tabbed interface for easy navigation.

## Setup Instructions

### 1. Database Setup
Run the SQL script to create the services tables and populate initial data:

**File:** `services_db.sql`

**Steps:**
- Open phpMyAdmin in your browser
- Select your `forge720` database
- Go to the "SQL" tab
- Copy and paste the entire content of `services_db.sql`
- Click "Go" to execute

The script will create:
- `service_categories` table - 14 predefined categories
- `services` table - 60+ predefined services

### 2. Files Created/Modified

#### New Files:
- `services.php` - Public services page with tab interface
- `admin/services.php` - Admin panel for managing services
- `services_db.sql` - Database setup script

#### Modified Files:
- `functions.php` - Added 8 new service-related functions
- `index.php` - Added "Services" navigation link
- `admin/index.php` - Added "Services" to admin sidebar
- `admin/products.php` - Added "Services" to admin sidebar
- `admin/categories.php` - Added "Services" to admin sidebar
- `admin/content.php` - Added "Services" to admin sidebar

### 3. New Functions Added to functions.php

```php
getServiceCategories()              // Get all service categories
getServicesByCategory($categoryId)  // Get services in a category
getAllServices()                    // Get all active services
getServiceById($serviceId)          // Get specific service
saveServiceCategory($categoryData)  // Create/update category (admin)
saveService($serviceData)           // Create/update service (admin)
deleteService($serviceId)           // Delete service (admin)
deleteServiceCategory($categoryId)  // Delete category (admin)
```

### 4. Adding Service Images

**Image Storage:**
- Place service images in the `images/` directory
- Recommended size: 300x200px or larger (images will be cropped to fit)
- Supported formats: JPG, PNG, GIF, WebP

**How to Add Images:**
1. Upload your image to `images/` folder
2. Go to Admin Panel → Services
3. Edit a service and enter the filename (e.g., `laser-cutting.jpg`)
4. Click "Update Service"

**Initial Setup:**
- For now, service cards will show a default icon if no image is provided
- Upload images later as they become available

### 5. Accessing the Services

**Public Page:**
- URL: `http://yourdomain.com/services.php`
- Features:
  - Tab interface for 14 service categories
  - Card-based service layout
  - Responsive design
  - Call-to-action button linking to contact form

**Admin Panel:**
- URL: `http://yourdomain.com/admin/services.php`
- Requires admin login
- Features:
  - Two tabs: "Manage Categories" and "Manage Services"
  - Add, edit, delete categories and services
  - Control display order
  - Activate/deactivate services
  - Upload service images

## Service Categories

1. **Cutting & Laser Services** (7 services)
   - Laser cutting, Plasma cutting, Waterjet cutting, Engraving, Marking, Custom design, Sheet profiling

2. **Metal Fabrication & Assembly** (4 services)
   - MIG/TIG/Arc welding, Structural steel, Custom fabrication, Assembly

3. **Sheet Metal & Forming** (4 services)
   - Bending, Rolling, Folding, Casings

4. **CNC & Machining** (5 services)
   - Milling, Turning, Routing, Precision manufacturing, Drilling

5. **Metal Joining** (4 services)
   - Riveting, Bolting, Spot welding, Mechanical systems

6. **Architectural Fabrication** (8 services)
   - Gates, Doors, Windows, Staircases, Railings, Balconies, Canopies, Decorative

7. **Outdoor & Structural Works** (7 services)
   - Car shades, Water towers, Pergolas, Storage, Beams, Trusses, Mezzanines

8. **Installation Services** (4 services)
   - Gate/door installation, Stairs/railings, On-site fabrication, Structural

9. **Repair & Maintenance** (5 services)
   - Welding repairs, Restoration, Rust removal, Maintenance, Reinforcement

10. **Finishing & Surface Treatment** (5 services)
    - Grinding, Powder coating, Galvanizing, Sandblasting, Painting

11. **Custom Products & Furniture** (5 services)
    - Tables, Chairs, Office furniture, Industrial furniture, Home fittings

12. **Industrial Fabrication** (4 services)
    - Machine frames, Conveyor systems, Platforms, Factory structures

13. **Security Fabrication** (4 services)
    - Security doors, Window grills, Perimeter fencing, Reinforced gates

14. **Green & Smart Solutions** (4 services)
    - Solar mounting, Eco-friendly, Recycled products, Automated structures

## Customization

### Changing Tab Colors
Edit `services.php` and modify CSS variables:
```css
:root {
    --jungle-green: #4B8B3E;    /* Primary color */
    --dark-green: #2F5D2A;      /* Dark accent */
    --light-green: #6BAF5C;     /* Light accent */
    --gold: #FFD700;            /* Highlight color */
}
```

### Adding New Categories
1. Go to Admin Panel → Services
2. Click "Manage Categories" tab
3. Fill in the form and click "Add Category"
4. New category appears in the tab list

### Reordering Services
1. Edit any category or service
2. Change the "Display Order" number
3. Lower numbers appear first

### Deactivating Services
1. Edit a service
2. Uncheck the "Active" checkbox
3. Service won't appear on public page but remains in database

## Troubleshooting

**Services page shows "Services coming soon!"**
- Check if database tables were created properly
- Verify SQL script was executed successfully
- Check database connection in `config.php`

**Services not appearing in admin dropdown**
- Ensure at least one category exists
- Refresh the page
- Check browser console for JavaScript errors

**Images not displaying**
- Verify image filename is correct (case-sensitive)
- Ensure image file exists in `images/` directory
- Check file permissions (should be readable)

**Tabs not switching**
- Check browser console for JavaScript errors
- Ensure JavaScript is enabled
- Try clearing browser cache

## API Reference

### Getting Services in Code
```php
// Get all service categories
$categories = getServiceCategories();

// Get services in a specific category
$services = getServicesByCategory(1);  // 1 = category ID

// Get all active services
$allServices = getAllServices();

// Get specific service details
$service = getServiceById(5);  // 5 = service ID
```

### Example Output
```php
Array (
    [id] => 1
    [service_category_id] => 1
    [service_name] => "Laser Cutting"
    [description] => "Precision laser cutting for various materials"
    [image] => "laser-cutting.jpg"
    [display_order] => 1
    [is_active] => 1
    [category_name] => "Cutting & Laser Services"
    [created_at] => "2024-04-17 10:00:00"
    [updated_at] => "2024-04-17 10:00:00"
)
```

## Performance Notes

- Services are cached when loaded
- Database queries are optimized with proper indexes
- Tab switching uses client-side JavaScript (no server calls)
- Images are lazy-loaded for better page performance

## Security

- All user inputs are sanitized
- Admin panel requires authentication
- SQL injection prevention using prepared statements
- XSS protection through output encoding

## Support

For issues or questions about the services system, check:
1. Admin Panel → Services (manage and test)
2. `functions.php` (service functions)
3. `services.php` (public interface)
4. `admin/services.php` (admin interface)

---
**Last Updated:** April 17, 2024
**Version:** 1.0
