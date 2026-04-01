from rest_framework import viewsets, status, filters
from rest_framework.decorators import action
from rest_framework.response import Response
from rest_framework.permissions import AllowAny, IsAuthenticated
from rest_framework.authentication import TokenAuthentication
from rest_framework.authtoken.models import Token
from django.contrib.auth.models import User
from django.contrib.auth import authenticate
from django.utils import timezone
from django.db import models
from datetime import timedelta
import random

from .models import UserProfile, Message, EmailVerification
from .serializers import (
    UserSerializer, UserRegistrationSerializer, ChangePasswordSerializer,
    UserProfileSerializer, MessageSerializer, MessageCreateSerializer
)


class UserAuthViewSet(viewsets.ViewSet):
    """
    ViewSet for user authentication (register, login, logout)
    """
    permission_classes = [AllowAny]

    @action(detail=False, methods=['post'])
    def register(self, request):
        """Register a new user"""
        serializer = UserRegistrationSerializer(data=request.data)
        if serializer.is_valid():
            user = serializer.save()
            # Generate verification code
            verification_code = str(random.randint(100000, 999999))
            EmailVerification.objects.create(
                user=user,
                verification_code=verification_code,
                expires_at=timezone.now() + timedelta(hours=24)
            )
            # TODO: Send email with verification code
            return Response({
                'user': UserSerializer(user).data,
                'message': 'User registered successfully. Please verify your email.'
            }, status=status.HTTP_201_CREATED)
        return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)

    @action(detail=False, methods=['post'])
    def login(self, request):
        """Login user and get auth token"""
        username = request.data.get('username')
        password = request.data.get('password')

        user = authenticate(username=username, password=password)
        if user:
            token, created = Token.objects.get_or_create(user=user)
            return Response({
                'token': token.key,
                'user': UserSerializer(user).data
            }, status=status.HTTP_200_OK)
        return Response(
            {'error': 'Invalid username or password'},
            status=status.HTTP_401_UNAUTHORIZED
        )

    @action(detail=False, methods=['post'], permission_classes=[IsAuthenticated])
    def logout(self, request):
        """Logout user and delete auth token"""
        request.user.auth_token.delete()
        return Response({'message': 'Logged out successfully'}, status=status.HTTP_200_OK)

    @action(detail=False, methods=['post'])
    def verify_email(self, request):
        """Verify email with verification code"""
        username = request.data.get('username')
        code = request.data.get('verification_code')

        try:
            user = User.objects.get(username=username)
            verification = EmailVerification.objects.get(user=user)

            if verification.verification_code == code:
                if verification.expires_at > timezone.now():
                    user.profile.is_verified = True
                    user.profile.save()
                    verification.delete()
                    return Response(
                        {'message': 'Email verified successfully'},
                        status=status.HTTP_200_OK
                    )
                else:
                    return Response(
                        {'error': 'Verification code expired'},
                        status=status.HTTP_400_BAD_REQUEST
                    )
            else:
                verification.attempts += 1
                verification.save()
                if verification.attempts >= 5:
                    verification.delete()
                    return Response(
                        {'error': 'Too many attempts. Verification code deleted.'},
                        status=status.HTTP_400_BAD_REQUEST
                    )
                return Response(
                    {'error': 'Invalid verification code'},
                    status=status.HTTP_400_BAD_REQUEST
                )
        except (User.DoesNotExist, EmailVerification.DoesNotExist):
            return Response(
                {'error': 'User or verification not found'},
                status=status.HTTP_404_NOT_FOUND
            )


class UserViewSet(viewsets.ViewSet):
    """
    ViewSet for user account management
    """
    permission_classes = [IsAuthenticated]
    authentication_classes = [TokenAuthentication]

    @action(detail=False, methods=['get'])
    def me(self, request):
        """Get current user profile"""
        serializer = UserSerializer(request.user)
        return Response(serializer.data)

    @action(detail=False, methods=['post'])
    def change_password(self, request):
        """Change user password"""
        serializer = ChangePasswordSerializer(data=request.data)
        if serializer.is_valid():
            if not request.user.check_password(serializer.validated_data['old_password']):
                return Response(
                    {'error': 'Old password is incorrect'},
                    status=status.HTTP_400_BAD_REQUEST
                )
            request.user.set_password(serializer.validated_data['new_password'])
            request.user.save()
            return Response({'message': 'Password changed successfully'}, status=status.HTTP_200_OK)
        return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)


class UserProfileViewSet(viewsets.ModelViewSet):
    """
    ViewSet for user profiles
    """
    serializer_class = UserProfileSerializer
    permission_classes = [IsAuthenticated]
    authentication_classes = [TokenAuthentication]

    def get_queryset(self):
        return UserProfile.objects.filter(user=self.request.user)

    @action(detail=False, methods=['get', 'put'])
    def me(self, request):
        """Get or update current user profile"""
        profile = request.user.profile
        if request.method == 'PUT':
            serializer = self.get_serializer(profile, data=request.data, partial=True)
            if serializer.is_valid():
                serializer.save()
                return Response(serializer.data)
            return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)
        serializer = self.get_serializer(profile)
        return Response(serializer.data)

    @action(detail=False, methods=['get'])
    def public(self, request):
        """Get public user profiles (sellers)"""
        profiles = UserProfile.objects.filter(is_seller=True)
        serializer = self.get_serializer(profiles, many=True)
        return Response(serializer.data)

    @action(detail=False, methods=['post'])
    def become_seller(self, request):
        """Convert user to seller"""
        profile = request.user.profile
        profile.is_seller = True
        profile.seller_name = request.data.get('seller_name', request.user.username)
        profile.business_info = request.data.get('business_info', '')
        profile.save()

        serializer = self.get_serializer(profile)
        return Response(serializer.data, status=status.HTTP_200_OK)


class MessageViewSet(viewsets.ModelViewSet):
    """
    ViewSet for messaging between users
    """
    serializer_class = MessageSerializer
    permission_classes = [IsAuthenticated]
    authentication_classes = [TokenAuthentication]
    filter_backends = [filters.SearchFilter, filters.OrderingFilter]
    search_fields = ['subject', 'message']
    ordering_fields = ['created_at']
    ordering = ['-created_at']

    def get_queryset(self):
        """Get messages for current user (sent and received)"""
        return Message.objects.filter(
            models.Q(sender=self.request.user) | models.Q(recipient=self.request.user)
        )

    def create(self, request, *args, **kwargs):
        """Create a new message"""
        serializer = MessageCreateSerializer(data=request.data, context={'request': request})
        if serializer.is_valid():
            serializer.save()
            message_serializer = MessageSerializer(serializer.instance)
            return Response(message_serializer.data, status=status.HTTP_201_CREATED)
        return Response(serializer.errors, status=status.HTTP_400_BAD_REQUEST)

    @action(detail=False, methods=['get'])
    def inbox(self, request):
        """Get received messages"""
        messages = Message.objects.filter(recipient=request.user)
        serializer = self.get_serializer(messages, many=True)
        return Response(serializer.data)

    @action(detail=False, methods=['get'])
    def sent(self, request):
        """Get sent messages"""
        messages = Message.objects.filter(sender=request.user)
        serializer = self.get_serializer(messages, many=True)
        return Response(serializer.data)

    @action(detail=True, methods=['post'])
    def mark_as_read(self, request, pk=None):
        """Mark message as read"""
        message = self.get_object()
        if message.recipient != request.user:
            return Response(
                {'error': 'You can only mark your received messages as read'},
                status=status.HTTP_403_FORBIDDEN
            )
        message.is_read = True
        message.save()

        serializer = self.get_serializer(message)
        return Response(serializer.data)

    @action(detail=False, methods=['get'])
    def unread_count(self, request):
        """Get count of unread messages"""
        count = Message.objects.filter(recipient=request.user, is_read=False).count()
        return Response({'unread_count': count})


# ==================== TEMPLATE VIEWS ====================

from django.shortcuts import render, redirect
from django.contrib.auth import authenticate, login, logout
from django.contrib.auth.decorators import login_required
from django.http import HttpResponse
from django.views.decorators.http import require_http_methods


@require_http_methods(["GET", "POST"])
def register_view(request):
    """User registration page"""
    if request.user.is_authenticated:
        return redirect('home')
    
    if request.method == 'POST':
        username = request.POST.get('username')
        email = request.POST.get('email')
        password = request.POST.get('password')
        confirm_password = request.POST.get('confirm_password')
        is_seller = request.POST.get('is_seller', False)
        
        if password != confirm_password:
            return render(request, 'auth/register.html', {'error': 'Passwords do not match'})
        
        if User.objects.filter(username=username).exists():
            return render(request, 'auth/register.html', {'error': 'Username already exists'})
        
        if User.objects.filter(email=email).exists():
            return render(request, 'auth/register.html', {'error': 'Email already exists'})
        
        try:
            user = User.objects.create_user(username=username, email=email, password=password)
            UserProfile.objects.create(user=user, role='seller' if is_seller else 'buyer')
            login(request, user)
            return redirect('home')
        except Exception as e:
            return render(request, 'auth/register.html', {'error': str(e)})
    
    return render(request, 'auth/register.html')


@require_http_methods(["GET", "POST"])
def login_view(request):
    """User login page"""
    if request.user.is_authenticated:
        return redirect('home')
    
    if request.method == 'POST':
        username = request.POST.get('username')
        password = request.POST.get('password')
        
        user = authenticate(request, username=username, password=password)
        if user is not None:
            login(request, user)
            next_page = request.GET.get('next', 'home')
            return redirect(next_page)
        else:
            return render(request, 'auth/login.html', {'error': 'Invalid username or password'})
    
    return render(request, 'auth/login.html')


@login_required(login_url='login')
def logout_view(request):
    """User logout"""
    logout(request)
    return redirect('home')


@login_required(login_url='login')
def profile_view(request):
    """User profile page"""
    try:
        user_profile = UserProfile.objects.get(user=request.user)
    except UserProfile.DoesNotExist:
        user_profile = UserProfile.objects.create(user=request.user)
    
    context = {
        'user_profile': user_profile,
    }
    return render(request, 'users/profile.html', context)


@login_required(login_url='login')
def messages_view(request):
    """User messages page"""
    messages = Message.objects.filter(
        models.Q(sender=request.user) | models.Q(recipient=request.user)
    ).order_by('-created_at')
    
    context = {
        'messages': messages,
    }
    return render(request, 'users/messages.html', context)


@login_required(login_url='login')
def orders_view(request):
    """User orders page"""
    from store.models import Order
    user_orders = Order.objects.filter(user=request.user).order_by('-created_at')
    
    context = {
        'orders': user_orders,
    }
    return render(request, 'users/orders.html', context)


def checkout_view(request):
    """Checkout page"""
    if not request.user.is_authenticated:
        return redirect('login')
    
    from store.models import Cart, UserAddress
    try:
        cart = Cart.objects.get(user=request.user)
        items = cart.items.all()
        total = cart.get_total_price()
    except:
        items = []
        total = 0
    
    addresses = UserAddress.objects.filter(user=request.user)
    
    context = {
        'cart_items': items,
        'total': total,
        'addresses': addresses,
    }
    return render(request, 'store/checkout.html', context)
