from django.contrib import admin
from django.utils.html import format_html
from .models import UserProfile, Message, EmailVerification


@admin.register(UserProfile)
class UserProfileAdmin(admin.ModelAdmin):
    list_display = ['user_display', 'seller_badge', 'verification_badge', 'created_at']
    list_filter = ['is_seller', 'is_verified', 'created_at']
    search_fields = ['user__username', 'seller_name']
    readonly_fields = ['created_at', 'updated_at']
    ordering = ['-created_at']

    def user_display(self, obj):
        return format_html(
            '<span style="font-weight: 600; color: #2c3e50;">👤 {}</span>',
            obj.user.username
        )
    user_display.short_description = 'User'

    def seller_badge(self, obj):
        if obj.is_seller:
            return format_html(
                '<span style="background: #4CAF50; color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">🏪 Seller</span>'
            )
        return '—'
    seller_badge.short_description = 'Role'

    def verification_badge(self, obj):
        if obj.is_verified:
            return format_html(
                '<span style="color: #4CAF50; font-weight: 600;">✓ Verified</span>'
            )
        return format_html(
            '<span style="color: #ff9800; font-weight: 600;">⏳ Pending</span>'
        )
    verification_badge.short_description = 'Status'


@admin.register(Message)
class MessageAdmin(admin.ModelAdmin):
    list_display = ['subject_display', 'sender', 'recipient', 'read_status', 'created_at']
    list_filter = ['is_read', 'created_at']
    search_fields = ['sender__username', 'recipient__username', 'subject']
    readonly_fields = ['created_at', 'updated_at']
    ordering = ['-created_at']

    def subject_display(self, obj):
        return format_html(
            '<span style="font-weight: 600; color: #2196F3;">💬 {}</span>',
            obj.subject[:50] + '...' if len(obj.subject) > 50 else obj.subject
        )
    subject_display.short_description = 'Subject'

    def read_status(self, obj):
        if obj.is_read:
            return format_html(
                '<span style="color: #999;">👁️ Read</span>'
            )
        return format_html(
            '<span style="color: #4CAF50; font-weight: 600;">📬 New</span>'
        )
    read_status.short_description = 'Status'


@admin.register(EmailVerification)
class EmailVerificationAdmin(admin.ModelAdmin):
    list_display = ['user', 'attempts_display', 'status_display', 'created_at']
    list_filter = ['created_at']
    search_fields = ['user__username']
    readonly_fields = ['created_at']
    ordering = ['-created_at']

    def attempts_display(self, obj):
        color = '#4CAF50' if obj.attempts < 3 else '#ff9800'
        return format_html(
            '<span style="color: {}; font-weight: 600;">{}/5 attempts</span>',
            color, obj.attempts
        )
    attempts_display.short_description = 'Attempts'

    def status_display(self, obj):
        from datetime import datetime, timezone
        if datetime.now(timezone.utc) > obj.expires_at:
            return format_html(
                '<span style="color: #e74c3c; font-weight: 600;">❌ Expired</span>'
            )
        return format_html(
            '<span style="color: #4CAF50; font-weight: 600;">✓ Valid</span>'
        )
    status_display.short_description = 'Status'
