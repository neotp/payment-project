from fastapi import FastAPI, HTTPException
from pyrfc import Connection, ABAPApplicationError, ABAPRuntimeError, LogonError, CommunicationError
from pydantic import BaseModel
from typing import List

# SAP connection settings
ASHOST = '172.21.130.208'
CLIENT = '110'
SYSNR = '00'
USER = 'notes'
PASSWD = 'february_02'

# Initialize the FastAPI app
app = FastAPI()

# Establish the connection
try:
    conn = Connection(ashost=ASHOST, sysnr=SYSNR, client=CLIENT, user=USER, passwd=PASSWD)
except CommunicationError:
    raise Exception("Could not connect to SAP server.")
except LogonError:
    raise Exception("Could not log in to SAP. Check credentials.")
except Exception as e:
    raise Exception(f"An unexpected error occurred: {e}")

# Define input structure for POST requests
class SAPQuery(BaseModel):
    table: str
    options: List[str]
    rows_at_a_time: int = 10

@app.get("/")
def root():
    return {"message": "Welcome to the SAP API. Use /load_data to query SAP tables."}

@app.post("/load_data/{customer_code}")
def load_data(customer_code: str):
    try:
        result = conn.call('Z_SD0003', CUSTOMER_CODE=customer_code)
        return {"status": "success", "data": result}

    except CommunicationError:
        raise HTTPException(status_code=500, detail="Could not connect to server.")
    except LogonError:
        raise HTTPException(status_code=401, detail="Could not log in. Wrong credentials?")
    except (ABAPApplicationError, ABAPRuntimeError) as e:
        raise HTTPException(status_code=500, detail=f"ABAP error occurred: {e}")
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"An unexpected error occurred: {e}")
