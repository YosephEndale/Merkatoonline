from django.contrib import admin
from .models import UserProfile, Message, EmailVerification


@admin.register(UserProfile)
class UserProfileAdmin(admin.ModelAdmin):
    list_display = ['user', 'is_seller', 'is_verified', 'created_at']
    list_filter = ['is_seller', 'is_verified', 'created_at']
    search_fields = ['user__username', 'seller_name']
    readonly_fields = ['created_at', 'updated_at']


@admin.register(Message)
class MessageAdmin(admin.ModelAdmin):
    list_display = ['sender', 'recipient', 'subject', 'is_read', 'created_at']
    list_filter = ['is_read', 'created_at']
    search_fields = ['sender__username', 'recipient__username', 'subject']
    readonly_fields = ['created_at', 'updated_at']


@admin.register(EmailVerification)
class EmailVerificationAdmin(admin.ModelAdmin):
    list_display = ['user', 'attempts', 'created_at', 'expires_at']
    list_filter = ['created_at']
    search_fields = ['user__username']
    readonly_fields = ['created_at']
