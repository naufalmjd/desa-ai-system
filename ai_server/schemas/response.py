"""Response schemas — Pydantic v2"""

from pydantic import BaseModel
from typing import Optional, Any


class BaseResponse(BaseModel):
    success: bool = True
    message: str = "OK"


class DetectionBox(BaseModel):
    x1: float
    y1: float
    x2: float
    y2: float
    confidence: float
    class_name: str
    class_id: int


class DetectionResponse(BaseResponse):
    category: str
    confidence: float
    priority: str
    boxes: list[DetectionBox] = []
    labels: list[str] = []
    processing_time_ms: float
    model: str = "YOLOv8"


class ChatResponse(BaseResponse):
    reply: str
    tokens: int = 0
    model: str


class SentimentResponse(BaseResponse):
    label: str      # positive | negative | neutral
    score: float    # 0.0 – 1.0
    details: Optional[dict] = None


class ErrorResponse(BaseModel):
    success: bool = False
    message: str
    detail: Optional[Any] = None
