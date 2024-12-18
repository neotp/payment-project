
from rest_framework.test import APITestCase
from rest_framework import status
from .models import Payment

class PaymentAPITestCase(APITestCase):
    def test_create_payment(self):
        data = {
            "customer_id": "CUST123",
            "amount": 100.0,
            "status": "Pending"
        }
        response = self.client.post('/api/load_data/', data, format='json')
        self.assertEqual(response.status_code, status.HTTP_201_CREATED)
        self.assertEqual(Payment.objects.count(), 1)
        self.assertEqual(Payment.objects.first().customer_id, "CUST123")

    def test_list_payments(self):
        Payment.objects.create(customer_id="CUST123", amount=100.0, status="Pending")
        response = self.client.get('/api/load_data/')
        self.assertEqual(response.status_code, status.HTTP_200_OK)
        self.assertEqual(len(response.data), 1)
