from django.http import JsonResponse
from pyrfc import Connection


def load_data(request):
    if request.method == 'POST':
        return JsonResponse({"message": "Data loaded successfully"})
    return JsonResponse({"error": "Invalid request method"}, status=400)

def call_sap(request):
    try:
        conn = Connection(
            user='notes',
            passwd='february_02',
            ashost='172.21.130.208',
            sysnr='00',  # SAP system number
            client='100',  # SAP client number
            lang='EN'  # Language (optional)
        )
        result = conn.call('RFC_PING')
        return JsonResponse(result)
    except Exception as e:
        return JsonResponse({"error": str(e)}, status=500)

def test_sap_connection(request):
    try:
        conn = Connection(
            user='notes',
            passwd='february_02',
            ashost='172.21.130.208',
            sysnr='00',  # SAP system number
            client='110',  # SAP client number
            lang='EN'  # Language (optional)    
        )
        result = conn.call('RFC_PING')
        return JsonResponse({"message": "Connection successful!", "result": result})
    except Exception as e:
        return JsonResponse({"error": str(e)}, status=500)