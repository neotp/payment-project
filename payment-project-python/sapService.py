# from pyrfc import Connection
# from config import SAP_CONFIG

# def connect_to_sap():
#     """Establish a connection to the SAP system."""
#     return Connection(**SAP_CONFIG)

# def get_system_status():
#     """Call an RFC function to get SAP system status."""
#     conn = connect_to_sap()
#     result = conn.call("STFC_CONNECTION", REQUTEXT="Ping")
#     conn.close()
#     return result
