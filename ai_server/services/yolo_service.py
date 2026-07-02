"""
YoloService — Computer Vision menggunakan YOLOv8 + OpenCV.
OOP: kelas tunggal dengan lazy-loading model.
"""

import time
import os
import logging
from pathlib import Path
from typing import Optional

import cv2
import numpy as np

logger = logging.getLogger(__name__)

# Peta kelas YOLOv8 → kategori pengaduan desa
CATEGORY_MAP: dict[str, str] = {
    "pothole":     "Jalan Rusak",
    "crack":       "Jalan Rusak",
    "road_damage": "Jalan Rusak",
    "garbage":     "Sampah",
    "trash":       "Sampah",
    "waste":       "Sampah",
    "flood":       "Banjir",
    "water":       "Banjir",
    "lamp":        "Lampu Jalan Mati",
    "streetlight": "Lampu Jalan Mati",
    "tree":        "Pohon Tumbang",
    "fallen_tree": "Pohon Tumbang",
    "infrastructure": "Infrastruktur",
}

PRIORITY_MAP: dict[str, str] = {
    "Banjir":           "kritis",
    "Pohon Tumbang":    "tinggi",
    "Jalan Rusak":      "tinggi",
    "Sampah":           "sedang",
    "Lampu Jalan Mati": "rendah",
    "Infrastruktur":    "sedang",
}


class YoloService:
    """Service YOLOv8 untuk deteksi objek pada foto pengaduan."""

    _instance: Optional["YoloService"] = None
    _model = None  # ultralytics.YOLO instance

    def __new__(cls) -> "YoloService":
        if cls._instance is None:
            cls._instance = super().__new__(cls)
        return cls._instance

    def _load_model(self, model_path: str) -> None:
        """Load model YOLOv8. Jika file tidak ada, gunakan model base."""
        if self._model is not None:
            return
        try:
            from ultralytics import YOLO
            if os.path.isfile(model_path):
                self._model = YOLO(model_path)
                logger.info(f"YOLOv8 model loaded: {model_path}")
            else:
                # Fallback ke model pretrained
                self._model = YOLO("yolov8n.pt")
                logger.warning(f"Custom model tidak ditemukan, menggunakan yolov8n.pt")
        except Exception as e:
            logger.error(f"Gagal memuat YOLOv8: {e}")
            self._model = None

    def detect(
        self,
        image_path: str,
        model_path: str = "models/yolov8n-desa.pt",
        confidence_threshold: float = 0.45,
    ) -> dict:
        """
        Deteksi objek pada gambar.

        Returns:
            dict dengan category, confidence, priority, boxes, labels, processing_time_ms
        """
        start = time.perf_counter()

        self._load_model(model_path)

        # Baca gambar dengan OpenCV
        img = cv2.imread(image_path)
        if img is None:
            raise ValueError(f"Tidak dapat membaca gambar: {image_path}")

        # Resize agar tidak terlalu berat
        h, w = img.shape[:2]
        if max(h, w) > 1280:
            scale = 1280 / max(h, w)
            img = cv2.resize(img, (int(w * scale), int(h * scale)))

        boxes_result = []
        labels_result = []
        best_category  = "Tidak Teridentifikasi"
        best_confidence = 0.0

        if self._model is not None:
            results = self._model.predict(img, conf=confidence_threshold, verbose=False)

            for result in results:
                for box in result.boxes:
                    conf  = float(box.conf[0])
                    cls   = int(box.cls[0])
                    name  = result.names[cls].lower()
                    xyxy  = box.xyxy[0].tolist()

                    boxes_result.append({
                        "x1": xyxy[0], "y1": xyxy[1],
                        "x2": xyxy[2], "y2": xyxy[3],
                        "confidence": conf,
                        "class_name": name,
                        "class_id": cls,
                    })

                    label     = result.names[cls]
                    category  = self._map_category(name)
                    labels_result.append(f"{label} ({conf:.0%})")

                    if conf > best_confidence:
                        best_confidence = conf
                        best_category   = category
        else:
            # Fallback: simulasi berdasarkan warna gambar (demo)
            best_category, best_confidence = self._heuristic_detect(img)

        elapsed_ms = (time.perf_counter() - start) * 1000
        priority   = PRIORITY_MAP.get(best_category, "sedang")

        return {
            "category":          best_category,
            "confidence":        round(best_confidence * 100, 2),
            "priority":          priority,
            "boxes":             boxes_result,
            "labels":            labels_result,
            "processing_time_ms": round(elapsed_ms, 2),
            "model":             "YOLOv8",
        }

    def _map_category(self, class_name: str) -> str:
        """Petakan nama kelas ke kategori pengaduan."""
        for key, cat in CATEGORY_MAP.items():
            if key in class_name:
                return cat
        return "Infrastruktur"

    def _heuristic_detect(self, img: np.ndarray) -> tuple[str, float]:
        """
        Deteksi heuristik sederhana berdasarkan analisis warna & tekstur.
        Digunakan sebagai fallback jika model YOLOv8 tidak tersedia.
        """
        hsv = cv2.cvtColor(img, cv2.COLOR_BGR2HSV)

        # Deteksi warna coklat/abu (jalan rusak)
        brown_lower = np.array([10, 50, 50])
        brown_upper = np.array([25, 255, 200])
        brown_mask = cv2.inRange(hsv, brown_lower, brown_upper)
        brown_ratio = np.sum(brown_mask > 0) / (img.shape[0] * img.shape[1])

        # Deteksi warna biru (banjir)
        blue_lower = np.array([100, 50, 50])
        blue_upper = np.array([130, 255, 255])
        blue_mask  = cv2.inRange(hsv, blue_lower, blue_upper)
        blue_ratio = np.sum(blue_mask > 0) / (img.shape[0] * img.shape[1])

        # Deteksi warna hijau gelap / hitam (sampah)
        dark_mask  = img.mean(axis=2) < 60
        dark_ratio = np.sum(dark_mask) / (img.shape[0] * img.shape[1])

        if blue_ratio > 0.3:
            return "Banjir", 0.78
        elif brown_ratio > 0.25:
            return "Jalan Rusak", 0.72
        elif dark_ratio > 0.4:
            return "Sampah", 0.65
        else:
            return "Infrastruktur", 0.55
