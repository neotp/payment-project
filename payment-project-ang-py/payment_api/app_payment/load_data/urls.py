from django.urls import path
from .views import test_sap_connection

urlpatterns = [
    path('test-sap/', test_sap_connection, name='test_sap_connection'),
]
