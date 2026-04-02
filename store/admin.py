from django.contrib import admin
from django.utils.html import format_html
from .models import (
    Category, Product, ProductImage, Cart, CartItem,
    UserAddress, Order, OrderItem, Review
)

# Customize Admin Site
admin.site.site_header = "Merkatoonline Admin"
admin.site.site_title = "Merkatoonline Management"
admin.site.index_title = "Dashboard"


@admin.register(Category)
class CategoryAdmin(admin.ModelAdmin):
    list_display = ['category_with_icon', 'created_at']
    search_fields = ['category_name']
    ordering = ['-created_at']

    def category_with_icon(self, obj):
        return format_html(
            '<span style="font-weight: 600; color: #4CAF50;">📁 {}</span>',
            obj.category_name
        )
    category_with_icon.short_description = 'Category'


@admin.register(Product)
class ProductAdmin(admin.ModelAdmin):
    list_display = ['product_name', 'category', 'price_display', 'stock_display', 'rating_display', 'created_at']
    list_filter = ['category', 'created_at', 'rating']
    search_fields = ['product_name', 'description']
    readonly_fields = ['created_at', 'updated_at']
    ordering = ['-created_at']

    def price_display(self, obj):
        return format_html(
            '<span style="color: #4CAF50; font-weight: 600;">${}</span>',
            obj.price
        )
    price_display.short_description = 'Price'

    def stock_display(self, obj):
        color = '#4CAF50' if obj.stock_quantity > 10 else '#ff9800' if obj.stock_quantity > 0 else '#e74c3c'
        return format_html(
            '<span style="color: {}; font-weight: 600;">{} units</span>',
            color, obj.stock_quantity
        )
    stock_display.short_description = 'Stock'

    def rating_display(self, obj):
        stars = '⭐' * int(obj.rating)
        return format_html('<span>{} ({})</span>', stars, obj.rating)
    rating_display.short_description = 'Rating'


@admin.register(ProductImage)
class ProductImageAdmin(admin.ModelAdmin):
    list_display = ['image_preview', 'product', 'is_primary', 'uploaded_at']
    list_filter = ['is_primary', 'product']
    search_fields = ['image_name', 'product__product_name']
    ordering = ['-uploaded_at']

    def image_preview(self, obj):
        if obj.image:
            return format_html(
                '<img src="{}" width="50" height="50" style="border-radius: 6px;" />',
                obj.image.url
            )
        return '—'
    image_preview.short_description = 'Preview'


@admin.register(Cart)
class CartAdmin(admin.ModelAdmin):
    list_display = ['user', 'created_at', 'updated_at']
    search_fields = ['user__username']
    readonly_fields = ['created_at', 'updated_at']


@admin.register(CartItem)
class CartItemAdmin(admin.ModelAdmin):
    list_display = ['product', 'cart', 'quantity', 'added_at']
    list_filter = ['added_at']
    search_fields = ['product__product_name', 'cart__user__username']


@admin.register(UserAddress)
class UserAddressAdmin(admin.ModelAdmin):
    list_display = ['user', 'city', 'country', 'is_default', 'created_at']
    list_filter = ['is_default', 'country', 'created_at']
    search_fields = ['user__username', 'city']
    readonly_fields = ['created_at', 'updated_at']


@admin.register(Order)
class OrderAdmin(admin.ModelAdmin):
    list_display = ['order_id_display', 'user', 'status_badge', 'total_price_display', 'created_at']
    list_filter = ['status', 'created_at']
    search_fields = ['user__username', 'id']
    readonly_fields = ['created_at', 'updated_at']
    ordering = ['-created_at']

    def order_id_display(self, obj):
        return format_html(
            '<span style="font-weight: 600; color: #2196F3;">Order #{}</span>',
            obj.id
        )
    order_id_display.short_description = 'Order ID'

    def status_badge(self, obj):
        color_map = {
            'pending': '#ff9800',
            'confirmed': '#2196F3',
            'shipped': '#9C27B0',
            'delivered': '#4CAF50',
            'cancelled': '#e74c3c'
        }
        color = color_map.get(obj.status, '#999')
        return format_html(
            '<span style="background: {}; color: white; padding: 6px 12px; border-radius: 20px; font-weight: 600;">{}</span>',
            color, obj.status.upper()
        )
    status_badge.short_description = 'Status'

    def total_price_display(self, obj):
        return format_html(
            '<span style="color: #4CAF50; font-weight: 600;">${}</span>',
            obj.total_amount
        )
    total_price_display.short_description = 'Total'


@admin.register(OrderItem)
class OrderItemAdmin(admin.ModelAdmin):
    list_display = ['order', 'product', 'quantity', 'price']
    list_filter = ['order__created_at']
    search_fields = ['product__product_name', 'order__id']


@admin.register(Review)
class ReviewAdmin(admin.ModelAdmin):
    list_display = ['product', 'user', 'rating', 'created_at']
    list_filter = ['rating', 'created_at']
    search_fields = ['product__product_name', 'user__username']
    readonly_fields = ['created_at', 'updated_at']
