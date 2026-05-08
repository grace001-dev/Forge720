This folder is for custom product images. The website now uses local image files for products.

To use your actual product photos:

1. Save each image file in this folder using one of these names:
   - aluminum-fence.jpg
   - handrail.jpg
   - balcony.jpg
   - gate.jpg
   - staircase.jpg

2. If the database already exists, run `update_images_local.php` or update the `image` field in the `products` table manually.

3. If you are setting up the site fresh, `db_setup.sql` already points to these filenames.

The system will automatically load local images from `images/<filename>`.
