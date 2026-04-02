from django.core.management.base import BaseCommand
from django.contrib.auth.models import User
from django.core.files.base import ContentFile
from store.models import Category, Product, ProductImage
from users.models import UserProfile
import requests


class Command(BaseCommand):
    help = 'Populate database with sample products and categories'

    def download_image(self, url, filename):
        """Download image from URL and return as ContentFile"""
        try:
            response = requests.get(url, timeout=15)
            if response.status_code == 200:
                return ContentFile(response.content, name=filename)
        except Exception as e:
            self.stdout.write(self.style.WARNING(f'  ⚠ Could not download {filename}'))
        return None

    def handle(self, *args, **options):
        self.stdout.write('Starting database population...')

        # Create categories
        categories_data = [
            {'name': 'Electronics', 'icon': '📱'},
            {'name': 'Fashion', 'icon': '👕'},
            {'name': 'Home & Kitchen', 'icon': '🏠'},
            {'name': 'Sports & Outdoors', 'icon': '⚽'},
            {'name': 'Books', 'icon': '📚'},
            {'name': 'Beauty & Personal Care', 'icon': '💄'},
            {'name': 'Toys & Games', 'icon': '🎮'},
            {'name': 'Health & Wellness', 'icon': '🏥'},
        ]

        categories = {}
        for cat_data in categories_data:
            category, created = Category.objects.get_or_create(
                category_name=cat_data['name']
            )
            categories[cat_data['name']] = category
            if created:
                self.stdout.write(f'✓ Created category: {cat_data["name"]}')

        # Sample products data with two reliable images each
        products_data = [
            # Electronics
            {
                'name': 'Wireless Bluetooth Headphones',
                'description': 'Premium sound quality with active noise cancellation and 30-hour battery life',
                'price': 149.99, 'stock': 45, 'rating': 4.7, 'category': 'Electronics',
                'images': [
                    'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=500&h=500&fit=crop'
                ]
            },
            {
                'name': 'USB-C Fast Charger 65W',
                'description': 'Multi-port charger compatible with laptops, phones, and tablets. Supports fast charging.',
                'price': 39.99, 'stock': 120, 'rating': 4.5, 'category': 'Electronics',
                'images': [
                    'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1595433707802-6b2626ef1c91?w=500&h=500&fit=crop'
                ]
            },
            {
                'name': '4K Webcam Pro',
                'description': 'Ultra HD 4K webcam with auto-focus and built-in microphone for streaming and video calls',
                'price': 89.99, 'stock': 32, 'rating': 4.6, 'category': 'Electronics',
                'images': [
                    'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1598928506696-9bea68e8a571?w=500&h=500&fit=crop'
                ]
            },
            {
                'name': 'Portable SSD 1TB',
                'description': 'External SSD with blazing fast transfer speeds. Rugged and durable design.',
                'price': 129.99, 'stock': 28, 'rating': 4.8, 'category': 'Electronics',
                'images': [
                    'https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1600921212383-cc8529cb6935?w=500&h=500&fit=crop'
                ]
            },

            # Fashion
            {
                'name': 'Premium Cotton T-Shirt',
                'description': 'Comfortable 100% cotton t-shirt available in multiple colors. Perfect for everyday wear.',
                'price': 24.99, 'stock': 150, 'rating': 4.4, 'category': 'Fashion',
                'images': [
                    'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1554521722-7d2c802efc52?w=500&h=500&fit=crop'
                ]
            },
            {
                'name': 'Athletic Running Shoes',
                'description': 'Lightweight running shoes with excellent cushioning and breathable mesh upper',
                'price': 99.99, 'stock': 60, 'rating': 4.7, 'category': 'Fashion',
                'images': [
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1542410227-7ab0fba55af5?w=500&h=500&fit=crop'
                ]
            },
            {
                'name': 'Denim Jeans Classic Blue',
                'description': 'Timeless classic denim jeans. Comfortable fit with a modern touch.',
                'price': 64.99, 'stock': 85, 'rating': 4.5, 'category': 'Fashion',
                'images': [
                    'https://images.unsplash.com/photo-1542270865-cbf467803f74?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1542821956-86c6d9c45dad?w=500&h=500&fit=crop'
                ]
            },
            {
                'name': 'Casual Hoodie',
                'description': 'Cozy hoodie perfect for chilly weather. Soft fleece material.',
                'price': 54.99, 'stock': 40, 'rating': 4.6, 'category': 'Fashion',
                'images': [
                    'https://images.unsplash.com/photo-1550355291-bbee04a92027?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1556821552-5a63fe2c1f18?w=500&h=500&fit=crop'
                ]
            },

            # Home & Kitchen
            {
                'name': 'Stainless Steel Cookware Set',
                'description': '10-piece cookware set including pots, pans, and lids. Dishwasher safe.',
                'price': 159.99, 'stock': 22, 'rating': 4.7, 'category': 'Home & Kitchen',
                'images': [
                    'https://images.unsplash.com/photo-1610701596007-11502861dcfa?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1578500494198-246f612d03b3?w=500&h=500&fit=crop'
                ]
            },
            {
                'name': 'Smart LED Desk Lamp',
                'description': 'Adjustable LED lamp with touch control and multiple brightness levels',
                'price': 44.99, 'stock': 75, 'rating': 4.5, 'category': 'Home & Kitchen',
                'images': [
                    'https://images.unsplash.com/photo-1565889068651-c54c69f83e06?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1565636192335-14c46fa1120f?w=500&h=500&fit=crop'
                ]
            },
            {
                'name': 'Air Purifier with HEPA Filter',
                'description': 'Removes 99.97% of particles and allergens. Quiet operation.',
                'price': 129.99, 'stock': 35, 'rating': 4.8, 'category': 'Home & Kitchen',
                'images': [
                    'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1584434209174-fa953f50f4d3?w=500&h=500&fit=crop'
                ]
            },

            # Sports & Outdoors
            {
                'name': 'Professional Yoga Mat',
                'description': 'Non-slip yoga mat with carrying strap. Eco-friendly material.',
                'price': 34.99, 'stock': 90, 'rating': 4.6, 'category': 'Sports & Outdoors',
                'images': [
                    'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1588286840104-8957b019727f?w=500&h=500&fit=crop'
                ]
            },
            {
                'name': 'Camping Tent 4-Person',
                'description': 'Waterproof tent with easy setup. Includes carrying bag.',
                'price': 149.99, 'stock': 18, 'rating': 4.7, 'category': 'Sports & Outdoors',
                'images': [
                    'https://images.unsplash.com/photo-1478131143081-80f7f84ae130?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?w=500&h=500&fit=crop'
                ]
            },
            {
                'name': 'Dumbbell Set 20kg',
                'description': 'Adjustable dumbbell set perfect for home workouts. Ergonomic grip.',
                'price': 79.99, 'stock': 50, 'rating': 4.5, 'category': 'Sports & Outdoors',
                'images': [
                    'https://images.unsplash.com/photo-1599058917212-d750089bc07e?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1574680096145-3f8f4fa61180?w=500&h=500&fit=crop'
                ]
            },

            # Books
            {
                'name': 'The Complete Python Guide',
                'description': 'Comprehensive guide to Python programming from basics to advanced concepts',
                'price': 44.99, 'stock': 35, 'rating': 4.8, 'category': 'Books',
                'images': [
                    'https://images.unsplash.com/photo-1506880018603-83d5b814b5a6?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1507842217343-583f20270319?w=500&h=500&fit=crop'
                ]
            },
            {
                'name': 'Web Development Essentials',
                'description': 'Learn HTML, CSS, and JavaScript. Perfect for beginners.',
                'price': 39.99, 'stock': 50, 'rating': 4.6, 'category': 'Books',
                'images': [
                    'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=500&h=500&fit=crop'
                ]
            },
            {
                'name': 'Business Strategy Masterclass',
                'description': 'Essential strategies for building and scaling successful businesses',
                'price': 34.99, 'stock': 42, 'rating': 4.7, 'category': 'Books',
                'images': [
                    'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=500&h=500&fit=crop'
                ]
            },

            # Beauty & Personal Care
            {
                'name': 'Organic Face Moisturizer',
                'description': 'Natural ingredients, dermatologist tested. Suitable for all skin types.',
                'price': 29.99, 'stock': 80, 'rating': 4.6, 'category': 'Beauty & Personal Care',
                'images': [
                    'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1616394584738-fc6e612e71b6?w=500&h=500&fit=crop'
                ]
            },
            {
                'name': 'Electric Toothbrush Pro',
                'description': 'Smart electric toothbrush with multiple cleaning modes and timer',
                'price': 64.99, 'stock': 45, 'rating': 4.7, 'category': 'Beauty & Personal Care',
                'images': [
                    'https://images.unsplash.com/photo-1610614957166-91aae1fa3fc3?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1628840042765-356cda07f23e?w=500&h=500&fit=crop'
                ]
            },

            # Toys & Games
            {
                'name': 'Board Game Strategy Collection',
                'description': 'Set of 5 classic strategy board games for family fun',
                'price': 49.99, 'stock': 30, 'rating': 4.5, 'category': 'Toys & Games',
                'images': [
                    'https://images.unsplash.com/photo-1516975080664-ed2fc6a32937?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1570303295866-30a21765e2ca?w=500&h=500&fit=crop'
                ]
            },
            {
                'name': 'Building Blocks Set 500pc',
                'description': 'Colorful building blocks perfect for creative play. Ages 4+',
                'price': 34.99, 'stock': 65, 'rating': 4.6, 'category': 'Toys & Games',
                'images': [
                    'https://images.unsplash.com/photo-1531746790731-6c087fecd65b?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1566576912481-82b2c01e5322?w=500&h=500&fit=crop'
                ]
            },

            # Health & Wellness
            {
                'name': 'Vitamin D3 Supplement',
                'description': 'Premium vitamin D3 capsules. 60-day supply.',
                'price': 19.99, 'stock': 120, 'rating': 4.7, 'category': 'Health & Wellness',
                'images': [
                    'https://images.unsplash.com/photo-1555684586-46f694c91d97?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1584017911766-d451b3cc3180?w=500&h=500&fit=crop'
                ]
            },
            {
                'name': 'Fitness Smartwatch',
                'description': 'Track your health with heart rate monitor, sleep tracking, and more',
                'price': 99.99, 'stock': 55, 'rating': 4.8, 'category': 'Health & Wellness',
                'images': [
                    'https://images.unsplash.com/photo-1575311373937-040b8e1fd5b6?w=500&h=500&fit=crop',
                    'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&h=500&fit=crop'
                ]
            },
        ]

        # Create products with 2 images each
        created_count = 0
        for product_data in products_data:
            category = categories[product_data['category']]
            product, created = Product.objects.get_or_create(
                product_name=product_data['name'],
                defaults={
                    'description': product_data['description'],
                    'price': product_data['price'],
                    'stock_quantity': product_data['stock'],
                    'rating': product_data['rating'],
                    'category': category,
                }
            )
            
            if created:
                created_count += 1
                self.stdout.write(f'  ✓ {product_data["name"]} - ${product_data["price"]}')
            
            # Download and add 2 images for each product
            images = product_data.get('images', [])
            for idx, image_url in enumerate(images, 1):
                if image_url:
                    filename = f'product_{product.id}_{idx}.jpg'
                    image_content = self.download_image(image_url, filename)
                    if image_content:
                        ProductImage.objects.get_or_create(
                            product=product,
                            image_name=filename,
                            defaults={
                                'image': image_content,
                                'is_primary': (idx == 1)
                            }
                        )

        # Create sample superuser if it doesn't exist
        if not User.objects.filter(username='admin').exists():
            admin = User.objects.create_superuser(
                username='admin',
                email='admin@merkatoonline.com',
                password='admin123'
            )
            UserProfile.objects.get_or_create(user=admin)
            self.stdout.write(
                self.style.SUCCESS('✓ Created superuser: admin / admin123')
            )

        self.stdout.write(
            self.style.SUCCESS(
                f'\n✅ Database populated successfully!\n'
                f'   Created {created_count} products\n'
                f'   Created {len(categories)} categories\n'
                f'   Total images: ~{created_count * 2} (2 per product)\n'
                f'   Admin: /admin/'
            )
        )
