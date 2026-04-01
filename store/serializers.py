from rest_framework import serializers
from .models import (
    Category, Product, ProductImage, Cart, CartItem,
    UserAddress, Order, OrderItem, Review
)


class CategorySerializer(serializers.ModelSerializer):
    """Serializer for categories"""
    class Meta:
        model = Category
        fields = ['id', 'category_name', 'description', 'created_at', 'updated_at']


class ProductImageSerializer(serializers.ModelSerializer):
    """Serializer for product images"""
    class Meta:
        model = ProductImage
        fields = ['id', 'product', 'image', 'image_name', 'is_primary', 'uploaded_at']


class ProductSerializer(serializers.ModelSerializer):
    """Serializer for products with images"""
    images = ProductImageSerializer(many=True, read_only=True)
    category_name = serializers.CharField(source='category.category_name', read_only=True)

    class Meta:
        model = Product
        fields = [
            'id', 'category', 'category_name', 'product_name', 'description',
            'price', 'stock_quantity', 'rating', 'seller_id', 'images',
            'created_at', 'updated_at'
        ]


class ProductDetailSerializer(serializers.ModelSerializer):
    """Detailed serializer for product with related data"""
    images = ProductImageSerializer(many=True, read_only=True)
    reviews = serializers.SerializerMethodField()
    category_name = serializers.CharField(source='category.category_name', read_only=True)

    class Meta:
        model = Product
        fields = [
            'id', 'category', 'category_name', 'product_name', 'description',
            'price', 'stock_quantity', 'rating', 'seller_id', 'images', 'reviews',
            'created_at', 'updated_at'
        ]

    def get_reviews(self, obj):
        reviews = obj.reviews.all()
        return ReviewSerializer(reviews, many=True).data


class CartItemSerializer(serializers.ModelSerializer):
    """Serializer for cart items"""
    product_details = ProductSerializer(source='product', read_only=True)
    total_price = serializers.SerializerMethodField()

    class Meta:
        model = CartItem
        fields = ['id', 'product', 'product_details', 'quantity', 'total_price', 'added_at']

    def get_total_price(self, obj):
        return str(obj.get_total_price())


class CartSerializer(serializers.ModelSerializer):
    """Serializer for shopping cart"""
    items = CartItemSerializer(many=True, read_only=True)
    total_price = serializers.SerializerMethodField()

    class Meta:
        model = Cart
        fields = ['id', 'user', 'items', 'total_price', 'created_at', 'updated_at']

    def get_total_price(self, obj):
        return str(obj.get_total_price())


class UserAddressSerializer(serializers.ModelSerializer):
    """Serializer for user addresses"""
    class Meta:
        model = UserAddress
        fields = [
            'id', 'user', 'address_line1', 'address_line2', 'city',
            'state', 'postal_code', 'country', 'is_default', 'created_at', 'updated_at'
        ]


class OrderItemSerializer(serializers.ModelSerializer):
    """Serializer for order items"""
    product_details = ProductSerializer(source='product', read_only=True)
    total_price = serializers.SerializerMethodField()

    class Meta:
        model = OrderItem
        fields = ['id', 'order', 'product', 'product_details', 'quantity', 'price', 'total_price']

    def get_total_price(self, obj):
        return str(obj.get_total_price())


class OrderSerializer(serializers.ModelSerializer):
    """Serializer for orders"""
    items = OrderItemSerializer(many=True, read_only=True)

    class Meta:
        model = Order
        fields = [
            'id', 'user', 'total_amount', 'shipping_address', 'status',
            'payment_method', 'items', 'created_at', 'updated_at'
        ]


class ReviewSerializer(serializers.ModelSerializer):
    """Serializer for product reviews"""
    username = serializers.CharField(source='user.username', read_only=True)

    class Meta:
        model = Review
        fields = ['id', 'product', 'user', 'username', 'rating', 'comment', 'created_at', 'updated_at']
