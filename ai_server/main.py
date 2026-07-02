"""
SIAP-Desa AI Server — FastAPI
AI Backend untuk Sistem Pelayanan Administrasi Desa

Endpoints:
    POST /api/v1/detect    — YOLOv8 Object Detection
    POST /api/v1/chat      — Gemini AI Chatbot
    POST /api/v1/sentiment — Analisis Sentimen
    GET  /health           — Health Check
"""

import logging
import os
import tempfile
from contextlib import asynccontextmanager
from pathlib import Path

import uvicorn
from fastapi import (
    FastAPI, File, UploadFile, Depends, HTTPException,
    Request, status
)
from fastapi.middleware.cors import CORSMiddleware
from fastapi.middleware.trustedhost import TrustedHostMiddleware
from fastapi.responses import JSONResponse

from config import get_settings
from middleware.auth import verify_api_key
from schemas.request import ChatRequest, SentimentRequest
from schemas.response import (
    DetectionResponse, ChatResponse, SentimentResponse, ErrorResponse
)
from services.yolo_service import YoloService
from services.gemini_service import GeminiService

# ─── Logging ────────────────────────────────────────────────────────────────
logging.basicConfig(
    level=logging.DEBUG if get_settings().DEBUG else logging.INFO,
    format="%(asctime)s | %(levelname)-8s | %(name)s | %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S",
)
logger = logging.getLogger(__name__)


# ─── Lifespan ────────────────────────────────────────────────────────────────
@asynccontextmanager
async def lifespan(app: FastAPI):
    """Inisialisasi services saat startup."""
    settings = get_settings()
    logger.info("🚀 SIAP-Desa AI Server starting...")

    # Init Gemini
    gemini = GeminiService()
    if settings.GEMINI_API_KEY:
        gemini.initialize(settings.GEMINI_API_KEY, settings.GEMINI_MODEL)
        logger.info("✅ Gemini AI initialized")
    else:
        logger.warning("⚠️  GEMINI_API_KEY tidak dikonfigurasi — menggunakan fallback")

    # YOLOv8 di-load saat pertama request (lazy)
    logger.info("✅ YOLOv8 Service ready (lazy loading)")
    logger.info(f"🌐 Server: http://{settings.HOST}:{settings.PORT}")

    yield

    logger.info("🛑 AI Server shutting down...")


# ─── App ─────────────────────────────────────────────────────────────────────
settings = get_settings()

app = FastAPI(
    title=settings.APP_NAME,
    version=settings.APP_VERSION,
    description="AI Backend — YOLOv8 Computer Vision & Gemini Chatbot",
    docs_url="/docs" if settings.DEBUG else None,
    redoc_url=None,
    lifespan=lifespan,
)

# ─── Middleware ───────────────────────────────────────────────────────────────
app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost", "http://127.0.0.1", "*"],
    allow_credentials=True,
    allow_methods=["GET", "POST"],
    allow_headers=["*"],
)

# ─── Exception Handlers ──────────────────────────────────────────────────────
@app.exception_handler(HTTPException)
async def http_exception_handler(request: Request, exc: HTTPException):
    return JSONResponse(
        status_code=exc.status_code,
        content={"success": False, "message": exc.detail},
    )

@app.exception_handler(Exception)
async def general_exception_handler(request: Request, exc: Exception):
    logger.error(f"Unhandled error: {exc}", exc_info=True)
    return JSONResponse(
        status_code=500,
        content={"success": False, "message": "Internal Server Error"},
    )


# ─── Health Check ─────────────────────────────────────────────────────────────
@app.get("/health")
@app.get("/")
async def health_check():
    return {
        "status":  "ok",
        "service": settings.APP_NAME,
        "version": settings.APP_VERSION,
        "ai": {
            "yolov8":  "ready",
            "gemini":  "ready" if settings.GEMINI_API_KEY else "no_key",
        }
    }


# ─── API Router ──────────────────────────────────────────────────────────────

@app.post(
    "/api/v1/detect",
    response_model=DetectionResponse,
    summary="YOLOv8 Object Detection",
    description="Upload gambar untuk deteksi objek pengaduan (jalan rusak, sampah, banjir, dll).",
    dependencies=[Depends(verify_api_key)],
)
async def detect_image(
    file: UploadFile = File(..., description="File gambar (JPG/PNG/WebP, maks 10MB)"),
):
    """
    Deteksi objek pada foto pengaduan menggunakan YOLOv8 + OpenCV.

    - **file**: Foto pengaduan (JPG, PNG, WebP)
    - Returns: Kategori, confidence score, prioritas, dan bounding boxes
    """
    # Validasi tipe file
    ext = Path(file.filename or "x.jpg").suffix.lower()
    if ext not in settings.ALLOWED_EXTENSIONS:
        raise HTTPException(
            status_code=status.HTTP_415_UNSUPPORTED_MEDIA_TYPE,
            detail=f"Format file tidak didukung: {ext}. Gunakan: {settings.ALLOWED_EXTENSIONS}",
        )

    # Validasi ukuran file
    contents = await file.read()
    size_mb   = len(contents) / (1024 * 1024)
    if size_mb > settings.MAX_IMAGE_SIZE_MB:
        raise HTTPException(
            status_code=status.HTTP_413_REQUEST_ENTITY_TOO_LARGE,
            detail=f"File terlalu besar ({size_mb:.1f} MB). Maks: {settings.MAX_IMAGE_SIZE_MB} MB",
        )

    # Simpan sementara & jalankan deteksi
    try:
        with tempfile.NamedTemporaryFile(suffix=ext, delete=False) as tmp:
            tmp.write(contents)
            tmp_path = tmp.name

        yolo   = YoloService()
        result = yolo.detect(
            image_path=tmp_path,
            model_path=settings.YOLO_MODEL_PATH,
            confidence_threshold=settings.YOLO_CONFIDENCE,
        )

        os.unlink(tmp_path)

        return DetectionResponse(
            success=True,
            message="Deteksi berhasil",
            category=result["category"],
            confidence=result["confidence"],
            priority=result["priority"],
            boxes=result["boxes"],
            labels=result["labels"],
            processing_time_ms=result["processing_time_ms"],
        )

    except ValueError as e:
        raise HTTPException(status_code=400, detail=str(e))
    except Exception as e:
        logger.error(f"Detection error: {e}", exc_info=True)
        raise HTTPException(status_code=500, detail="Gagal memproses gambar.")
    finally:
        try:
            if 'tmp_path' in locals() and os.path.exists(tmp_path):
                os.unlink(tmp_path)
        except Exception:
            pass


@app.post(
    "/api/v1/chat",
    response_model=ChatResponse,
    summary="Gemini AI Chatbot",
    description="Chatbot pelayanan publik menggunakan Google Gemini.",
    dependencies=[Depends(verify_api_key)],
)
async def chat(request: ChatRequest):
    """
    Chat dengan AI Asisten Desa menggunakan Gemini.

    - **message**: Pesan dari user
    - **history**: Riwayat percakapan
    - **context**: Konteks tambahan (opsional)
    """
    try:
        gemini = GeminiService()
        result = gemini.chat(
            message=request.message,
            history=[h.model_dump() for h in request.history],
            context=request.context,
        )
        return ChatResponse(
            success=True,
            reply=result["reply"],
            tokens=result.get("tokens", 0),
            model=result.get("model", "gemini"),
        )
    except Exception as e:
        logger.error(f"Chat error: {e}", exc_info=True)
        raise HTTPException(status_code=500, detail="Gagal mendapatkan respons AI.")


@app.post(
    "/api/v1/sentiment",
    response_model=SentimentResponse,
    summary="Analisis Sentimen",
    description="Analisis sentimen teks pengaduan atau feedback warga.",
    dependencies=[Depends(verify_api_key)],
)
async def analyze_sentiment(request: SentimentRequest):
    """Analisis sentimen menggunakan Gemini AI."""
    try:
        gemini = GeminiService()
        result = gemini.analyze_sentiment(request.text)
        return SentimentResponse(
            success=True,
            label=result.get("label", "neutral"),
            score=result.get("score", 0.5),
            details=result,
        )
    except Exception as e:
        logger.error(f"Sentiment error: {e}", exc_info=True)
        raise HTTPException(status_code=500, detail="Gagal menganalisis sentimen.")


# ─── Entry Point ─────────────────────────────────────────────────────────────
if __name__ == "__main__":
    uvicorn.run(
        "main:app",
        host=settings.HOST,
        port=settings.PORT,
        reload=settings.DEBUG,
        log_level="debug" if settings.DEBUG else "info",
        workers=1 if settings.DEBUG else 2,
    )
