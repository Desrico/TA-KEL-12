import os
import random
import pandas as pd
from groq import Groq
from dotenv import load_dotenv
from utils.db_connector import get_database

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
load_dotenv(dotenv_path=os.path.join(BASE_DIR, '..', '.env'))

USE_MOCK = False
GROQ_MODEL = "llama-3.3-70b-versatile"

api_key = os.getenv("GROQ_API_KEY")
if not api_key:
    USE_MOCK = True
    client = None
else:
    try:
        client = Groq(api_key=api_key)
    except Exception:
        USE_MOCK = True
        client = None

def _get_mock_fallback(feeling: str) -> str:
    """Cadangan jika API error atau kuota habis."""
    return f"Tetap semangat! Meskipun kamu sedang merasa {feeling}, ingatlah bahwa setiap hari adalah kesempatan baru untuk memulai kembali."

# FUNGSI UTAMA — dipanggil oleh main.py
def get_recommendation(nim: str) -> str:
    """
    Menghasilkan quote motivasi berdasarkan emosi terakhir mahasiswa dari MongoDB.
    """
    try:
        db = get_database()
        # Ambil check-in terakhir mahasiswa
        last_entry = db.daily_checkins.find_one(
            {"nim": nim},
            sort=[("created_at", -1)]
        )

        if not last_entry:
            return "Selamat datang! Semoga harimu menyenangkan dan penuh semangat."

        emosi_label = last_entry.get('feeling_name', 'Biasa Saja')

        # Generate via Groq
        if not USE_MOCK and client:
            try:
                chat_completion = client.chat.completions.create(
                    messages=[
                        {
                            "role": "system",
                            "content": (
                                "Tugasmu adalah membuat pesan dukungan psikologis singkat untuk beranda aplikasi kesehatan mental mahasiswa. "
                                "Pesan harus hangat, natural, dan maksimal 2 kalimat. "
                                "DILARANG menggunakan kata ganti orang pertama (saya, aku). "
                                "Konteks: Mahasiswa baru saja mencatat perasaan mereka. "
                                "Gunakan bahasa Indonesia santai (warm & supportive)."
                            )
                        },
                        {
                            "role": "user",
                            "content": f"Mahasiswa sedang merasa '{emosi_label}'. Berikan pesan dukungan yang relevan dengan perasaan tersebut."
                        }
                    ],
                    model=GROQ_MODEL,
                    temperature=0.8,
                )
                return chat_completion.choices[0].message.content.strip()
            except Exception:
                return _get_mock_fallback(emosi_label)
        else:
            return _get_mock_fallback(emosi_label)

    except Exception as e:
        print(f"Recommender error: {e}")
        return "Jaga kesehatan mentalmu dan tetaplah berbuat baik pada dirimu sendiri hari ini."

if __name__ == "__main__":
    # Test simulation
    print(f"Quote : {get_recommendation('MHS-001')}")