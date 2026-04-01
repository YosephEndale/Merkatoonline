from django.urls import path, include
from rest_framework.routers import DefaultRouter
from .views import (
    UserAuthViewSet, UserViewSet, UserProfileViewSet, MessageViewSet
)

router = DefaultRouter()
router.register('auth', UserAuthViewSet, basename='auth')
router.register('profile', UserProfileViewSet, basename='profile')
router.register('messages', MessageViewSet, basename='message')

urlpatterns = [
    path('', include(router.urls)),
    path('account/', UserViewSet.as_view({
        'get': 'me',
        'post': 'change_password'
    }), name='user-account'),
]
