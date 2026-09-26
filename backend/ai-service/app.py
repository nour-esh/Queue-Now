from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from predict import predict_waiting_time

app = FastAPI(title="Waiting Time AI Service", version="2.0")

# Define the exact contract matching the keys in predict.py
class PredictionRequest(BaseModel):
    queue_length_before: int
    avg_service_time: float
    available_staff: int
    previous_waiting_time: float
    hour: int
    day_of_week: int
    service_type: str

@app.post("/predict")
def predict(data: PredictionRequest):
    try:
        # Convert incoming JSON payload to a standard Python dictionary
        customer_data = data.model_dump()
        
        # Call the AI team's prediction function directly
        predicted_minutes = predict_waiting_time(customer_data)
        
        return {
            "status": "success",
            "predicted_waiting_time_minutes": predicted_minutes
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

