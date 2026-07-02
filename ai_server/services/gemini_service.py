"""
GeminiService — Chatbot AI menggunakan Google Gemini API.
OOP dengan singleton pattern dan conversation history management.
"""

import logging
from typing import Optional
import google.generativeai as genai
from google.generativeai.types import HarmCategory, HarmBlockThreshold

logger = logging.getLogger(__name__)


class GeminiService:
    """Service Gemini AI untuk chatbot pelayanan publik desa."""

    _instance: Optional["GeminiService"] = None
    _model = None

    DEFAULT_CONTEXT = """
    Kamu adalah Asisten AI Desa yang bernama 'SIAP-Bot'.
    Tugasmu adalah membantu warga desa mendapatkan informasi layanan:

    1. Prosedur & persyaratan pengajuan surat (Domisili, KTP, KK, dll)
    2. Cara melaporkan pengaduan (jalan rusak, sampah, banjir, dll)
    3. Informasi bantuan sosial (PKH, BLT, BPNT, Beasiswa)
    4. Jadwal kegiatan desa (posyandu, musrenbang, gotong royong)
    5. Informasi kontak perangkat desa

    Aturan:
    - Gunakan Bahasa Indonesia yang sopan, ramah, dan mudah dipahami
    - Jawab secara ringkas dan terstruktur
    - Jika tidak tahu, arahkan ke kantor desa secara langsung
    - Jangan memberikan informasi medis, hukum, atau keuangan yang kompleks
    - Selalu sampaikan jam layanan: Senin–Jumat pukul 08.00–15.00 WIB
    """

    def __new__(cls) -> "GeminiService":
        if cls._instance is None:
            cls._instance = super().__new__(cls)
        return cls._instance

    def initialize(self, api_key: str, model_name: str = "gemini-1.5-pro") -> None:
        """Inisialisasi Gemini API."""
        if self._model is not None:
            return
        try:
            genai.configure(api_key=api_key)

            safety = {
                HarmCategory.HARM_CATEGORY_HARASSMENT:        HarmBlockThreshold.BLOCK_MEDIUM_AND_ABOVE,
                HarmCategory.HARM_CATEGORY_HATE_SPEECH:       HarmBlockThreshold.BLOCK_MEDIUM_AND_ABOVE,
                HarmCategory.HARM_CATEGORY_SEXUALLY_EXPLICIT: HarmBlockThreshold.BLOCK_MEDIUM_AND_ABOVE,
                HarmCategory.HARM_CATEGORY_DANGEROUS_CONTENT: HarmBlockThreshold.BLOCK_MEDIUM_AND_ABOVE,
            }

            self._model = genai.GenerativeModel(
                model_name=model_name,
                safety_settings=safety,
                generation_config=genai.GenerationConfig(
                    temperature=0.7,
                    top_p=0.9,
                    max_output_tokens=2048,
                ),
                system_instruction=self.DEFAULT_CONTEXT,
            )
            logger.info(f"Gemini model initialized: {model_name}")

        except Exception as e:
            logger.error(f"Gagal inisialisasi Gemini: {e}")
            self._model = None

    def chat(
        self,
        message: str,
        history: list[dict],
        context: Optional[str] = None,
    ) -> dict:
        """
        Kirim pesan ke Gemini dan dapatkan jawaban.

        Args:
            message:  Pesan terbaru dari user
            history:  List {role, content} riwayat percakapan
            context:  Konteks tambahan (opsional)

        Returns:
            {reply, tokens, model}
        """
        if self._model is None:
            return self._fallback_response(message)

        try:
            # Convert history ke format Gemini
            gemini_history = []
            for msg in history[-10:]:  # Ambil 10 pesan terakhir
                role = "user" if msg["role"] == "user" else "model"
                gemini_history.append({
                    "role": role,
                    "parts": [msg["content"]],
                })

            # Tambahkan konteks jika ada
            final_message = message
            if context:
                final_message = f"[Konteks sistem: {context}]\n\n{message}"

            # Mulai chat session
            chat_session = self._model.start_chat(history=gemini_history)
            response = chat_session.send_message(final_message)

            reply  = response.text
            tokens = response.usage_metadata.total_token_count if hasattr(response, 'usage_metadata') else 0

            return {
                "reply":  reply,
                "tokens": tokens,
                "model":  "gemini-1.5-pro",
            }

        except genai.types.BlockedPromptException:
            return {
                "reply": "Maaf, saya tidak dapat memproses pertanyaan tersebut. Silakan ubah pertanyaan Anda.",
                "tokens": 0,
                "model": "gemini-1.5-pro",
            }
        except Exception as e:
            logger.error(f"Gemini error: {e}")
            return self._fallback_response(message)

    def analyze_sentiment(self, text: str) -> dict:
        """Analisis sentimen teks (pengaduan/feedback)."""
        if self._model is None:
            return {"label": "neutral", "score": 0.5}

        try:
            prompt = f"""
            Analisis sentimen dari teks berikut dan kembalikan hanya JSON dengan format:
            {{"label": "positive|negative|neutral", "score": 0.0-1.0, "reason": "alasan singkat"}}

            Teks: {text[:500]}
            """
            response = self._model.generate_content(prompt)
            import json
            result = json.loads(response.text.strip().strip("```json").strip("```"))
            return result
        except Exception as e:
            logger.error(f"Sentiment analysis error: {e}")
            return {"label": "neutral", "score": 0.5, "reason": "tidak dapat dianalisis"}

    def _fallback_response(self, message: str) -> dict:
        """Respons fallback jika Gemini tidak tersedia."""
        faqs = {
            "surat": "Untuk mengajukan surat administrasi, silakan buka menu **Pengajuan Surat** di sidebar kiri. Persyaratan umum meliputi KTP, KK, dan berkas pendukung sesuai jenis surat. Proses pengerjaan 2-5 hari kerja.",
            "pengaduan": "Untuk melaporkan keluhan lingkungan/fasilitas umum, silakan gunakan menu **Buat Pengaduan**. Tuliskan deskripsi lengkap, lokasi kejadian, serta unggah foto bukti agar model AI YOLOv8 kami dapat mendeteksi prioritasnya.",
            "bantuan": "Informasi program bantuan sosial aktif (seperti BLT, PKH, BPNT) dapat Anda lihat secara rinci melalui menu **Informasi Desa**.",
            "jam": "Kantor Desa Sukamaju melayani warga pada hari kerja **Senin–Jumat pukul 08.00–15.00 WIB**.",
            "jadwal": "Jadwal kegiatan desa seperti Posyandu Balita, Musrenbang, atau Gotong Royong warga diperbarui secara berkala di papan pengumuman digital pada menu **Informasi Desa**.",
            "kegiatan": "Jadwal kegiatan desa seperti Posyandu Balita, Musrenbang, atau Gotong Royong warga diperbarui secara berkala di papan pengumuman digital pada menu **Informasi Desa**.",
            "kades": "Kepala Desa Sukamaju saat ini dijabat oleh **H. Ahmad Fauzi**. Beliau berkomitmen mewujudkan pelayanan desa digital yang transparan dan responsif.",
            "lurah": "Kepala Desa Sukamaju saat ini dijabat oleh **H. Ahmad Fauzi**. Beliau berkomitmen mewujudkan pelayanan desa digital yang transparan dan responsif.",
            "halo": "Halo! Saya **SIAP-Bot**, asisten AI virtual Desa Sukamaju. Silakan tanyakan apa saja seputar pelayanan surat, pengaduan, bansos, jadwal kegiatan, atau kontak desa.",
            "hai": "Halo! Saya **SIAP-Bot**, asisten AI virtual Desa Sukamaju. Silakan tanyakan apa saja seputar pelayanan surat, pengaduan, bansos, jadwal kegiatan, atau kontak desa.",
            "pagi": "Selamat pagi! Ada yang bisa saya bantu hari ini seputar pelayanan Desa Sukamaju?",
            "siang": "Selamat siang! Ada yang bisa saya bantu hari ini seputar pelayanan Desa Sukamaju?",
            "sore": "Selamat sore! Ada yang bisa saya bantu hari ini seputar pelayanan Desa Sukamaju?",
        }

        msg_lower = message.lower()
        for key, answer in faqs.items():
            if key in msg_lower:
                return {"reply": answer, "tokens": 0, "model": "fallback"}

        return {
            "reply": f"Terima kasih atas pertanyaan Anda mengenai *\"{message}\"*. \n\nInformasi terkait hal tersebut dapat dikoordinasikan langsung dengan perangkat desa di **Kantor Desa Sukamaju** pada jam kerja (Senin-Jumat, 08.00-15.00 WIB). \n\nAlternatifnya, Anda dapat mencoba bertanya menggunakan kata kunci layanan seperti: **'syarat surat'**, **'cara buat pengaduan'**, **'jadwal kegiatan'**, atau **'bansos'**.",
            "tokens": 0,
            "model": "fallback",
        }
