from .models import Category

def categories(request):
    """Add categories to all template contexts"""
    return {
        'categories': Category.objects.all()
    }