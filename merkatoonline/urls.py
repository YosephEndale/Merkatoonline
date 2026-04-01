"""
URL configuration for merkatoonline project.
"""
from django.contrib import admin
from django.urls import path, include
from django.conf import settings
from django.conf.urls.static import static
from store.views import home, products_list, product_detail, cart_view
from users.views import (
    register_view, login_view, logout_view, profile_view, 
    messages_view, orders_view, checkout_view
)

urlpatterns = [
    path('admin/', admin.site.urls),
    
    # Frontend routes (Template views)
    path('', home, name='home'),
    path('products/', products_list, name='products'),
    path('products/<int:pk>/', product_detail, name='product_detail'),
    path('cart/', cart_view, name='cart'),
    path('checkout/', checkout_view, name='checkout'),
    
    # Authentication routes
    path('register/', register_view, name='register'),
    path('login/', login_view, name='login'),
    path('logout/', logout_view, name='logout'),
    
    # User routes
    path('profile/', profile_view, name='profile'),
    path('messages/', messages_view, name='messages'),
    path('orders/', orders_view, name='orders'),
    
    # API routes
    path('api/store/', include('store.urls')),
    path('api/users/', include('users.urls')),
]

if settings.DEBUG:
    urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
    urlpatterns += static(settings.STATIC_URL, document_root=settings.STATIC_ROOT)
