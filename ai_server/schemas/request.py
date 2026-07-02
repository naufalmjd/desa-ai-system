"""Request schemas — Pydantic v2"""

from pydantic import BaseModel, Field
from typing import Optional


class ChatMessage(BaseModel):
    role: str = Field(..., pattern="^(user|assistant|system)$")
    content: str = Field(..., min_length=1)


class ChatRequest(BaseModel):
    message: str = Field(..., min_length=1, max_length=4096)
    history: list[ChatMessage] = Field(default_factory=list)
    context: Optional[str] = Field(default=None)
    session_id: Optional[str] = None


class SentimentRequest(BaseModel):
    text: str = Field(..., min_length=1, max_length=10000)


class PredictRequest(BaseModel):
    data: dict
    model_type: str = "aduan"
