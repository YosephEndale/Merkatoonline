from django.urls import path, include
from rest_framework.routers import DefaultRouter
from .views import (
    CategoryViewSet, ProductViewSet, ProductImageViewSet,
    CartViewSet, UserAddressViewSet, OrderViewSet,
    CheckoutViewSet, ReviewViewSet
)

router = DefaultRouter()
router.register('categories', CategoryViewSet)
router.register('products', ProductViewSet)
router.register('product-images', ProductImageViewSet)
router.register('cart', CartViewSet, basename='cart')
router.register('addresses', UserAddressViewSet, basename='address')
router.register('orders', OrderViewSet, basename='order')
router.register('checkout', CheckoutViewSet, basename='checkout')
router.register('reviews', ReviewViewSet, basename='review')

urlpatterns = [
    path('', include(router.urls)),
]
