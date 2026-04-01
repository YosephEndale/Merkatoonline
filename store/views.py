from rest_framework import viewsets, status, filters
from rest_framework.decorators import action
from rest_framework.response import Response
from rest_framework.permissions import IsAuthenticated, AllowAny
from django_filters.rest_framework import DjangoFilterBackend
from django.shortcuts import get_object_or_404, render, redirect
from django.views.generic import ListView, DetailView, TemplateView
from django.contrib.auth.mixins import LoginRequiredMixin
from django.http import JsonResponse
from django.core.paginator import Paginator

from .models import (
    Category, Product, ProductImage, Cart, CartItem,
    UserAddress, Order, OrderItem, Review
)
from .serializers import (
    CategorySerializer, ProductSerializer, ProductDetailSerializer,
    ProductImageSerializer, CartSerializer, CartItemSerializer,
    UserAddressSerializer, OrderSerializer, OrderItemSerializer,
    ReviewSerializer
)


class CategoryViewSet(viewsets.ReadOnlyModelViewSet):
    """
    ViewSet for browsing product categories.
    """
    queryset = Category.objects.all()
    serializer_class = CategorySerializer
    permission_classes = [AllowAny]
    filter_backends = [filters.SearchFilter, filters.OrderingFilter]
    search_fields = ['category_name']
    ordering_fields = ['category_name', 'created_at']


class ProductViewSet(viewsets.ReadOnlyModelViewSet):
    """
    ViewSet for browsing products with search and filtering.
    """
    queryset = Product.objects.all()
    serializer_class = ProductSerializer
    permission_classes = [AllowAny]
    filter_backends = [DjangoFilterBackend, filters.SearchFilter, filters.OrderingFilter]
    filterset_fields = ['category', 'price']
    search_fields = ['product_name', 'description']
    ordering_fields = ['price', 'rating', 'created_at']

    def get_serializer_class(self):
        if self.action == 'retrieve':
            return ProductDetailSerializer
        return ProductSerializer

    @action(detail=True, methods=['get'])
    def reviews(self, request, pk=None):
        """Get all reviews for a product"""
        product = self.get_object()
        reviews = product.reviews.all()
        serializer = ReviewSerializer(reviews, many=True)
        return Response(serializer.data)


class ProductImageViewSet(viewsets.ModelViewSet):
    """
    ViewSet for managing product images.
    """
    queryset = ProductImage.objects.all()
    serializer_class = ProductImageSerializer
    permission_classes = [IsAuthenticated]
    filter_backends = [DjangoFilterBackend]
    filterset_fields = ['product']


class CartViewSet(viewsets.ViewSet):
    """
    ViewSet for managing shopping cart.
    """
    permission_classes = [IsAuthenticated]

    def list(self, request):
        """Get user's cart"""
        cart, created = Cart.objects.get_or_create(user=request.user)
        serializer = CartSerializer(cart)
        return Response(serializer.data)

    @action(detail=False, methods=['post'])
    def add_item(self, request):
        """Add item to cart"""
        cart, created = Cart.objects.get_or_create(user=request.user)
        product_id = request.data.get('product_id')
        quantity = request.data.get('quantity', 1)

        product = get_object_or_404(Product, id=product_id)

        cart_item, item_created = CartItem.objects.get_or_create(
            cart=cart,
            product=product,
            defaults={'quantity': quantity}
        )

        if not item_created:
            cart_item.quantity += int(quantity)
            cart_item.save()

        serializer = CartSerializer(cart)
        return Response(serializer.data, status=status.HTTP_201_CREATED)

    @action(detail=False, methods=['post'])
    def update_item(self, request):
        """Update quantity of item in cart"""
        cart_item_id = request.data.get('cart_item_id')
        quantity = request.data.get('quantity', 1)

        cart_item = get_object_or_404(CartItem, id=cart_item_id, cart__user=request.user)
        cart_item.quantity = int(quantity)
        cart_item.save()

        cart = cart_item.cart
        serializer = CartSerializer(cart)
        return Response(serializer.data)

    @action(detail=False, methods=['post'])
    def remove_item(self, request):
        """Remove item from cart"""
        cart_item_id = request.data.get('cart_item_id')
        cart_item = get_object_or_404(CartItem, id=cart_item_id, cart__user=request.user)
        cart = cart_item.cart
        cart_item.delete()

        serializer = CartSerializer(cart)
        return Response(serializer.data)

    @action(detail=False, methods=['post'])
    def clear(self, request):
        """Clear entire cart"""
        cart = get_object_or_404(Cart, user=request.user)
        cart.items.all().delete()

        serializer = CartSerializer(cart)
        return Response(serializer.data)


class UserAddressViewSet(viewsets.ModelViewSet):
    """
    ViewSet for managing user addresses.
    """
    serializer_class = UserAddressSerializer
    permission_classes = [IsAuthenticated]

    def get_queryset(self):
        return UserAddress.objects.filter(user=self.request.user)

    def perform_create(self, serializer):
        serializer.save(user=self.request.user)

    @action(detail=True, methods=['post'])
    def set_default(self, request, pk=None):
        """Set an address as default"""
        address = self.get_object()
        UserAddress.objects.filter(user=request.user).update(is_default=False)
        address.is_default = True
        address.save()

        serializer = self.get_serializer(address)
        return Response(serializer.data)


class OrderViewSet(viewsets.ReadOnlyModelViewSet):
    """
    ViewSet for viewing orders.
    """
    serializer_class = OrderSerializer
    permission_classes = [IsAuthenticated]

    def get_queryset(self):
        return Order.objects.filter(user=self.request.user)

    @action(detail=True, methods=['get'])
    def items(self, request, pk=None):
        """Get items for a specific order"""
        order = self.get_object()
        items = order.items.all()
        serializer = OrderItemSerializer(items, many=True)
        return Response(serializer.data)


class CheckoutViewSet(viewsets.ViewSet):
    """
    ViewSet for checkout and order creation.
    """
    permission_classes = [IsAuthenticated]

    @action(detail=False, methods=['post'])
    def create_order(self, request):
        """Create order from cart"""
        cart = get_object_or_404(Cart, user=request.user)

        if not cart.items.exists():
            return Response(
                {'error': 'Cart is empty'},
                status=status.HTTP_400_BAD_REQUEST
            )

        shipping_address_id = request.data.get('address_id')
        payment_method = request.data.get('payment_method', 'credit_card')

        address = get_object_or_404(UserAddress, id=shipping_address_id, user=request.user)

        shipping_address = f"{address.address_line1}"
        if address.address_line2:
            shipping_address += f", {address.address_line2}"
        shipping_address += f", {address.city}, {address.state}, {address.postal_code}, {address.country}"

        total_amount = cart.get_total_price()

        # Create order
        order = Order.objects.create(
            user=request.user,
            total_amount=total_amount,
            shipping_address=shipping_address,
            payment_method=payment_method,
            status='pending'
        )

        # Create order items
        for cart_item in cart.items.all():
            OrderItem.objects.create(
                order=order,
                product=cart_item.product,
                quantity=cart_item.quantity,
                price=cart_item.product.price
            )

        # Clear cart
        cart.items.all().delete()

        serializer = OrderSerializer(order)
        return Response(serializer.data, status=status.HTTP_201_CREATED)


class ReviewViewSet(viewsets.ModelViewSet):
    """
    ViewSet for product reviews.
    """
    serializer_class = ReviewSerializer
    permission_classes = [IsAuthenticated]
    filter_backends = [DjangoFilterBackend]
    filterset_fields = ['product']

    def get_queryset(self):
        return Review.objects.all()

    def perform_create(self, serializer):
        serializer.save(user=self.request.user)

    @action(detail=False, methods=['get'])
    def my_reviews(self, request):
        """Get reviews written by current user"""
        reviews = Review.objects.filter(user=request.user)
        serializer = self.get_serializer(reviews, many=True)
        return Response(serializer.data)


# ==================== TEMPLATE VIEWS ====================

def home(request):
    """Home page with featured products"""
    featured_products = Product.objects.all()[:6]
    categories = Category.objects.all()
    context = {
        'featured_products': featured_products,
        'categories': categories,
    }
    return render(request, 'store/home.html', context)


def products_list(request):
    """Products listing page with filtering"""
    products = Product.objects.all()
    categories = Category.objects.all()
    
    # Filtering
    category_id = request.GET.get('category')
    search_query = request.GET.get('q')
    
    if category_id:
        products = products.filter(category_id=category_id)
    
    if search_query:
        products = products.filter(product_name__icontains=search_query)
    
    # Pagination
    paginator = Paginator(products, 12)
    page_number = request.GET.get('page', 1)
    page_obj = paginator.get_page(page_number)
    
    context = {
        'page_obj': page_obj,
        'products': page_obj.object_list,
        'categories': categories,
        'search_query': search_query,
        'selected_category': category_id,
    }
    return render(request, 'store/products.html', context)


def product_detail(request, pk):
    """Product detail page with reviews"""
    product = get_object_or_404(Product, pk=pk)
    reviews = Review.objects.filter(product=product)
    images = product.images.all()
    related_products = Product.objects.filter(category=product.category).exclude(id=pk)[:4]
    
    context = {
        'product': product,
        'reviews': reviews,
        'images': images,
        'related_products': related_products,
    }
    return render(request, 'store/product_detail.html', context)


def cart_view(request):
    """Shopping cart page"""
    if not request.user.is_authenticated:
        return redirect('login')
    
    try:
        cart = Cart.objects.get(user=request.user)
        items = cart.items.all()
        total = cart.get_total_price()
    except Cart.DoesNotExist:
        items = []
        total = 0
    
    context = {
        'cart_items': items,
        'total': total,
    }
    return render(request, 'store/cart.html', context)
