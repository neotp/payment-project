from django.contrib import admin
from django.urls import path, include

urlpatterns = [
    path('admin/', admin.site.urls),
    path('load-data/', include('load_data.urls')),  # Include load_data app URLs
]
